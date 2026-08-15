<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Advanced Filters panel's "Contact Availability" radio group — a
 * single choice (Any is represented by null, not a case of its own —
 * see App\Discovery\Search\DiscoveryFilterCriteria) rather than
 * checkboxes: "No contact information" is mutually exclusive with the
 * other three by definition, so a multi-select checkbox group could
 * represent contradictory combinations (e.g. "Email available" AND "No
 * contact information" both checked) a radio group can't.
 *
 * Unlike every other Advanced Filters group added so far, this one is
 * actually wired into a real query — see
 * App\Discovery\Search\WebsiteSearchService::applyContactAvailability()
 * — since discovered_websites already has real, populated columns
 * (email, phone, contact_page_url) for it to filter on; most other
 * filter groups are still UI-only because the columns/enrichment they
 * need don't exist yet.
 *
 * CONTACT_FORM is filtered as "contact_page_url is not null" —
 * App\Discovery\Enrichment\DiscoveryContactExtractor detects a contact
 * *page*, not the presence of an actual <form> element on it, so this
 * is a reasonable but approximate proxy signal, documented here rather
 * than silently assumed to be exact.
 */
enum ContactAvailability: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case CONTACT_FORM = 'contact_form';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email available',
            self::PHONE => 'Phone available',
            self::CONTACT_FORM => 'Contact form available',
            self::NONE => 'No contact information',
        };
    }
}