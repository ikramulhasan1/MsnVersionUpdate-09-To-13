<?php

declare(strict_types=1);

namespace App\Audit\Contacts;

use App\Audit\Contacts\DTO\ContactInfoResult;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Fetching\DTO\SchemaBlock;

/**
 * Extracts publicly-published contact information from already-crawled
 * pages: emails, phones, social profile links, and team member
 * name/title pairs.
 *
 * KNOWN LIMITATIONS (documented rather than silently worked around):
 *   - Emails/phones come from mailto:/tel: links only (see
 *     HtmlParser::parseSchemeLinks(), added specifically to support
 *     this class) — never from scanning body prose. A page's raw HTML
 *     isn't retained past parsing (see BusinessSignalsDetector's
 *     docblock for the same architectural note), and even where it
 *     were, prose-scanning for email/phone-shaped text produces real
 *     false positives (image filenames, example numbers in copy). An
 *     address or number only published as plain text with no mailto:/
 *     tel: link will not be found here. Under-extracting is preferred
 *     over guessing.
 *   - Phone numbers are returned exactly as published in the tel: link
 *     (e.g. "+1-555-0100", "555.0100", "(555) 0100") with no
 *     international-format normalization or validation — this is a
 *     known gap, not an oversight, since phone formats vary too widely
 *     across countries to normalize reliably without a dedicated
 *     library this codebase doesn't currently depend on.
 *   - Team members are extracted ONLY from schema.org Person markup
 *     found on a team/about-shaped page — never guessed from heading
 *     text followed by nearby prose, even though that pattern is
 *     common on real team pages. CrawledPage carries no DOM-adjacency
 *     information (which heading "belongs to" which paragraph), so
 *     any such guess would risk attributing a role to the wrong named
 *     person — a real-world harm to that person, not just a
 *     data-quality bug. A team page with no Person markup yields no
 *     team members here, honestly, rather than a guessed list.
 *   - Emails and phones each carry the sourceUrl of the first crawled
 *     page they were found linked from — the same first-occurrence
 *     rule teamMembers already applies per name is applied here per
 *     address/number, since a contact address/number can legitimately
 *     appear (via mailto:/tel: links) on more than one page (e.g. a
 *     footer repeated site-wide), and only the first page it was seen
 *     on is kept rather than a list of every page.
 */
final class ContactInfoExtractor
{
    private const string TEAM_PAGE_PATTERN = '/team|about|our-people|staff/i';

    /**
     * @var array<string, string> platform => URL-substring pattern
     */
    private const array SOCIAL_PLATFORM_PATTERNS = [
        'linkedin' => '#linkedin\.com/(company|in)/#i',
        'facebook' => '#facebook\.com/#i',
        'instagram' => '#instagram\.com/#i',
        'twitter' => '#(twitter\.com|x\.com)/#i',
        'youtube' => '#youtube\.com/#i',
    ];

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     */
    public function extract(array $crawledPages): ContactInfoResult
    {
        $url = $crawledPages[0]->url ?? '';

        return new ContactInfoResult(
            url: $url,
            emails: $this->extractEmails($crawledPages),
            phones: $this->extractPhones($crawledPages),
            socialProfiles: $this->extractSocialProfiles($crawledPages),
            teamMembers: $this->extractTeamMembers($crawledPages),
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     * @return array<int, array{value: string, sourceUrl: string}>
     */
    private function extractEmails(array $crawledPages): array
    {
        $emails = [];
        $seen = [];

        foreach ($crawledPages as $page) {
            foreach ($page->mailtoLinks as $address) {
                if ($this->isPlaceholderEmail($address)) {
                    continue;
                }

                if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
                    continue;
                }

                $normalized = strtolower($address);

                if (isset($seen[$normalized])) {
                    continue;
                }

                $seen[$normalized] = true;
                $emails[] = ['value' => $normalized, 'sourceUrl' => $page->url];
            }
        }

        return $emails;
    }

    private function isPlaceholderEmail(string $address): bool
    {
        $normalized = strtolower($address);

        return in_array($normalized, [
            'example@example.com',
            'test@test.com',
            'you@example.com',
            'name@example.com',
            'email@example.com',
        ], true) || str_ends_with($normalized, '@example.com') || str_ends_with($normalized, '@test.com');
    }

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     * @return array<int, array{value: string, sourceUrl: string}>
     */
    private function extractPhones(array $crawledPages): array
    {
        $phones = [];
        $seen = [];

        foreach ($crawledPages as $page) {
            foreach ($page->telLinks as $number) {
                if (isset($seen[$number])) {
                    continue;
                }

                $seen[$number] = true;
                $phones[] = ['value' => $number, 'sourceUrl' => $page->url];
            }
        }

        return $phones;
    }

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     * @return array<string, string>
     */
    private function extractSocialProfiles(array $crawledPages): array
    {
        $profiles = [];

        foreach ($crawledPages as $page) {
            foreach ($page->anchors as $anchor) {
                foreach (self::SOCIAL_PLATFORM_PATTERNS as $platform => $pattern) {
                    if (isset($profiles[$platform])) {
                        continue;
                    }

                    if (preg_match($pattern, $anchor->url) === 1) {
                        $profiles[$platform] = $anchor->url;
                    }
                }
            }
        }

        return $profiles;
    }

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     * @return array<int, array{name: string, title: ?string, linkedinUrl: ?string, sourceUrl: string}>
     */
    private function extractTeamMembers(array $crawledPages): array
    {
        $members = [];
        $seenNames = [];

        foreach ($crawledPages as $page) {
            $looksLikeTeamPage = preg_match(self::TEAM_PAGE_PATTERN, $page->url) === 1
                || ($page->title !== null && preg_match(self::TEAM_PAGE_PATTERN, $page->title) === 1);

            if (! $looksLikeTeamPage) {
                continue;
            }

            foreach ($page->schema as $block) {
                /** @var SchemaBlock $block */
                if (! $block->valid || $block->data === null) {
                    continue;
                }

                foreach ($this->findPersonNodes($block->data) as $person) {
                    $name = $person['name'] ?? null;

                    if (! is_string($name) || trim($name) === '') {
                        continue;
                    }

                    $dedupeKey = strtolower(trim($name)).'|'.$page->url;

                    if (isset($seenNames[$dedupeKey])) {
                        continue;
                    }

                    $seenNames[$dedupeKey] = true;

                    $members[] = [
                        'name' => trim($name),
                        'title' => is_string($person['jobTitle'] ?? null) ? trim($person['jobTitle']) : null,
                        'linkedinUrl' => $this->findLinkedInUrl($person),
                        'sourceUrl' => $page->url,
                    ];
                }
            }
        }

        return $members;
    }

    /**
     * Recursively finds every node carrying an "@type" of "Person"
     * within a decoded JSON-LD payload, including nested "@graph"
     * arrays — mirroring HtmlParser::collectSchemaTypes()'s traversal
     * shape, but collecting whole Person nodes rather than just type
     * names.
     *
     * @param  array<mixed>  $node
     * @return array<int, array<string, mixed>>
     */
    private function findPersonNodes(array $node): array
    {
        $people = [];

        if (array_is_list($node)) {
            foreach ($node as $entry) {
                if (is_array($entry)) {
                    $people = [...$people, ...$this->findPersonNodes($entry)];
                }
            }

            return $people;
        }

        $types = (array) ($node['@type'] ?? []);

        if (in_array('Person', $types, true)) {
            $people[] = $node;
        }

        if (isset($node['@graph']) && is_array($node['@graph'])) {
            $people = [...$people, ...$this->findPersonNodes($node['@graph'])];
        }

        return $people;
    }

    /**
     * @param  array<string, mixed>  $person
     */
    private function findLinkedInUrl(array $person): ?string
    {
        $candidates = [];

        if (isset($person['url']) && is_string($person['url'])) {
            $candidates[] = $person['url'];
        }

        if (isset($person['sameAs'])) {
            $sameAs = is_array($person['sameAs']) ? $person['sameAs'] : [$person['sameAs']];

            foreach ($sameAs as $link) {
                if (is_string($link)) {
                    $candidates[] = $link;
                }
            }
        }

        foreach ($candidates as $candidate) {
            if (preg_match('#linkedin\.com/in/#i', $candidate) === 1) {
                return $candidate;
            }
        }

        return null;
    }
}
