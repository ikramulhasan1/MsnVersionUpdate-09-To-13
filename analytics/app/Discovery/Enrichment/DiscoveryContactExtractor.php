<?php

declare(strict_types=1);

namespace App\Discovery\Enrichment;

use App\Audit\Contacts\ContactInfoExtractor;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Discovery\Enrichment\DTO\DiscoveryContactResult;

/**
 * The Website Discovery module's contact-enrichment step — wraps
 * App\Audit\Contacts\ContactInfoExtractor rather than re-implementing
 * any of its email/phone/social-profile extraction: every regex/
 * parsing rule for those three already lives there (mailto:/tel:
 * links, schema.org Person markup, social platform URL patterns — see
 * that class's own docblock for the full list of documented
 * limitations, which apply here unchanged since nothing about the
 * underlying extraction changes), and this class calls it rather than
 * duplicating any of it.
 *
 * The one piece this class adds — because ContactInfoExtractor has no
 * equivalent of its own — is finding a "contact page" URL among the
 * crawled pages, using the same URL/title pattern-matching approach
 * ContactInfoExtractor::extractTeamMembers() already uses to spot a
 * team/about page, just for "contact" instead of "team/about".
 */
final class DiscoveryContactExtractor
{
    private const string CONTACT_PAGE_PATTERN = '/\bcontact\b/i';

    public function __construct(
        private readonly ContactInfoExtractor $contactInfoExtractor,
    ) {
    }

    /**
     * @param array<int, CrawledPage> $crawledPages
     */
    public function extract(array $crawledPages): DiscoveryContactResult
    {
        $contactInfo = $this->contactInfoExtractor->extract($crawledPages);

        return new DiscoveryContactResult(
            email: $contactInfo->emails[0]['value'] ?? null,
            phone: $contactInfo->phones[0]['value'] ?? null,
            contactPageUrl: $this->findContactPageUrl($crawledPages),
            socialProfiles: $contactInfo->socialProfiles,
        );
    }

    /**
     * The first crawled page whose URL or title looks like a contact
     * page, or null if none do — mirrors
     * ContactInfoExtractor::extractTeamMembers()'s own
     * TEAM_PAGE_PATTERN URL-or-title check exactly, just matching
     * "contact" instead of "team|about|our-people|staff".
     *
     * @param array<int, CrawledPage> $crawledPages
     */
    private function findContactPageUrl(array $crawledPages): ?string
    {
        foreach ($crawledPages as $page) {
            $looksLikeContactPage = preg_match(self::CONTACT_PAGE_PATTERN, $page->url) === 1
                || ($page->title !== null && preg_match(self::CONTACT_PAGE_PATTERN, $page->title) === 1);

            if ($looksLikeContactPage) {
                return $page->url;
            }
        }

        return null;
    }
}