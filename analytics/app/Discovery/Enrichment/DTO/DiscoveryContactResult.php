<?php

declare(strict_types=1);

namespace App\Discovery\Enrichment\DTO;

/**
 * The Website Discovery module's contact-enrichment result — one
 * email, one phone, one contact page URL, and the social profile map,
 * ready to be written onto DiscoveredWebsite::$email/$phone/
 * $contact_page_url/$social_profiles.
 *
 * Deliberately a single email/phone rather than
 * App\Audit\Contacts\DTO\ContactInfoResult's own deduplicated lists
 * (each with a sourceUrl — see that DTO's own docblock): a discovered
 * site's row has exactly one email/phone column, not a list, so
 * App\Discovery\Enrichment\DiscoveryContactExtractor picks the first
 * (i.e. earliest-found) entry from each list when building this
 * result — the same "first occurrence wins" precedence
 * ContactInfoResult's own fields already use internally.
 */
final readonly class DiscoveryContactResult
{
    /**
     * @param array<string, string> $socialProfiles keyed by platform (see
     *        App\Audit\Contacts\ContactInfoExtractor::SOCIAL_PLATFORM_PATTERNS),
     *        value is the profile URL — passed straight through from
     *        ContactInfoResult::$socialProfiles unchanged.
     */
    public function __construct(
        public ?string $email,
        public ?string $phone,
        public ?string $contactPageUrl,
        public array $socialProfiles,
    ) {
    }
}