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
 *   - Phone numbers come from tel: links only (see
 *     HtmlParser::parseSchemeLinks(), added specifically to support
 *     this class) — never from scanning body prose. A page's raw HTML
 *     isn't retained past parsing (see BusinessSignalsDetector's
 *     docblock for the same architectural note), and even where it
 *     were, prose-scanning for phone-shaped text produces real false
 *     positives (prices, dates, product SKUs). A phone number
 *     published as plain text with no tel: link will not be found
 *     here. Under-extracting is preferred over guessing.
 *
 *     Emails, as of Phase M5, are DIFFERENT: they now also come from a
 *     real regex scan of each page's own visible text (see
 *     HtmlParser::parsePlainTextEmails()'s own docblock for why an
 *     email-shaped string in genuinely visible body text carries far
 *     less false-positive risk than a phone-shaped one does — that
 *     scan already excludes script/style/noscript/template content,
 *     the actual source of the risk this limitation originally
 *     warned about). See extractEmails() below for how mailto:-sourced
 *     and plain-text-sourced addresses are combined, deduplicated, and
 *     prioritized.
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
     *
     * Public so App\Discovery\Enums\SocialPlatform (Website Discovery
     * module) can keep its own filter vocabulary in sync with the
     * platforms this extractor actually recognizes — PHP enum cases
     * can't be generated from this array directly (enums are fixed at
     * compile time), so that enum's cases are hand-kept matching these
     * same five keys rather than auto-derived, but at least both now
     * live where a future change to one is visible next to the other.
     */
    public const array SOCIAL_PLATFORM_PATTERNS = [
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
            emails: $this->extractEmails($crawledPages, $url),
            phones: $this->extractPhones($crawledPages),
            socialProfiles: $this->extractSocialProfiles($crawledPages),
            teamMembers: $this->extractTeamMembers($crawledPages),
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    /**
     * PRODUCTION INCIDENT (Phase M5) — read before reverting to
     * mailto:-only: this used to read ONLY $page->mailtoLinks, missing
     * a real, common pattern — a site whose only mailto: link is a
     * generic personal address (a founder's own Gmail in a footer,
     * say) while the REAL business contact email is published as
     * plain, unlinked text elsewhere (e.g. "Contact us:
     * info@example.com" with no mailto: href around it). That pattern
     * made this extractor reliably find the WRONG email — a personal
     * one — while missing the actual business address entirely, which
     * is the opposite of what a lead-generation tool needs.
     *
     * Combines BOTH sources now (mailto: links, still the primary,
     * higher-confidence source, PLUS a plain-text regex scan — see
     * HtmlParser::parsePlainTextEmails()'s own docblock), deduplicates
     * by address across both, and — this is the actual fix for
     * "finds the wrong email" specifically, not just "finds more
     * emails" — sorts the result so an address whose own domain
     * matches $websiteUrl's own domain (a real business email) always
     * comes before a personal/free-provider address, regardless of
     * which source found it or which page order it appeared in. A
     * caller reading $emails[0] (the common case: "what's THE contact
     * email for this business") now gets this extractor's own best
     * guess at the real business address, not merely whichever mailto:
     * link happened to appear first in the crawled HTML.
     *
     * @param  array<int, CrawledPage>  $crawledPages
     * @return array<int, array{value: string, sourceUrl: string, source: string, isBusinessDomain: bool}>
     */
    private function extractEmails(array $crawledPages, string $websiteUrl): array
    {
        $websiteDomain = $this->domainFor($websiteUrl);
        $emails = [];
        $seen = [];

        foreach ($crawledPages as $page) {
            foreach ($page->mailtoLinks as $address) {
                $this->addEmailIfNew($emails, $seen, $address, $page->url, 'mailto', $websiteDomain);
            }
        }

        foreach ($crawledPages as $page) {
            foreach ($page->plainTextEmails as $address) {
                $this->addEmailIfNew($emails, $seen, $address, $page->url, 'plain_text', $websiteDomain);
            }
        }

        // Business-domain matches first; within each group, keep
        // insertion order (mailto: links were all added before any
        // plain-text match above, so a stable sort here preserves that
        // "mailto before plain_text" preference automatically without
        // needing a second sort key).
        usort($emails, static fn (array $a, array $b): int => ($b['isBusinessDomain'] ? 1 : 0) <=> ($a['isBusinessDomain'] ? 1 : 0));

        return array_values($emails);
    }

    /**
     * @param  array<int, array{value: string, sourceUrl: string, source: string, isBusinessDomain: bool}>  $emails
     * @param  array<string, bool>  $seen
     */
    private function addEmailIfNew(array &$emails, array &$seen, string $address, string $pageUrl, string $source, ?string $websiteDomain): void
    {
        if ($this->isPlaceholderEmail($address)) {
            return;
        }

        if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
            return;
        }

        $normalized = strtolower($address);

        if (isset($seen[$normalized])) {
            return;
        }

        $seen[$normalized] = true;
        $emails[] = [
            'value' => $normalized,
            'sourceUrl' => $pageUrl,
            'source' => $source,
            'isBusinessDomain' => $this->isBusinessDomainEmail($normalized, $websiteDomain),
        ];
    }

    /**
     * True when $email's own domain matches $websiteDomain — allowing
     * for a subdomain on either side (mail.example.com and example.com
     * both count as matching example.com), since a real business
     * contact address is just as often on a mail subdomain as on the
     * bare root domain. False (never a guess) when $websiteDomain
     * itself couldn't be determined at all.
     */
    private function isBusinessDomainEmail(string $email, ?string $websiteDomain): bool
    {
        if ($websiteDomain === null) {
            return false;
        }

        $emailDomain = strtolower((string) substr($email, strrpos($email, '@') + 1));

        return $emailDomain === $websiteDomain
            || str_ends_with($emailDomain, '.'.$websiteDomain)
            || str_ends_with($websiteDomain, '.'.$emailDomain);
    }

    /**
     * The website's own registrable-ish domain for comparison against
     * an email's own domain — a plain host lowercase + "www." stripped,
     * not a full public-suffix-list-aware registrable-domain parse
     * (this codebase has no such library dependency), which is
     * accurate enough for "does this email look like it belongs to
     * this business" without needing to correctly handle every
     * multi-part TLD (co.uk, com.au, ...) edge case — a false negative
     * there (missing a real match) only means this extractor falls
     * back to its OLD mailto:-first-found behavior for that one site,
     * never a false positive that would misidentify a personal email
     * as a business one.
     */
    private function domainFor(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = strtolower($host);

        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
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