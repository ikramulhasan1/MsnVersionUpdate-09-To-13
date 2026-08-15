<?php

declare(strict_types=1);

namespace App\Discovery\Search;

use App\Discovery\Enums\ContactAvailability;

/**
 * Normalized Website Discovery search filters, built from the raw
 * query-string array App\Http\Controllers\DiscoveryController::index()
 * receives (the same shape search-panel.blade.php's form submits and
 * DiscoveryController::search() round-trips — see that controller's
 * own docblock).
 *
 * Deliberately scoped to ONLY $contactAvailability for now — every
 * other Advanced Filters group (Industry/Niche, Location, Technology,
 * Website Quality, Opportunity, Age & Size, Traffic, Social Media,
 * ...) still submits and round-trips through the URL exactly as before
 * (see search-panel.blade.php's own docblock for each group's status),
 * but none of them are parsed into this DTO or applied to a query yet.
 * Contact Availability is the first filter with real, already-
 * populated columns behind it (discovered_websites.email/phone/
 * contact_page_url), so it's the first one actually wired end-to-end;
 * a future phase extends both this DTO and WebsiteSearchService one
 * filter group at a time as their own backing columns/enrichment
 * become real, rather than this class trying to represent filters that
 * don't do anything yet.
 */
final readonly class DiscoveryFilterCriteria
{
    public function __construct(
        public ?ContactAvailability $contactAvailability = null,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function fromRequestFilters(array $filters): self
    {
        $rawContactAvailability = $filters['contact_availability'] ?? null;

        return new self(
            contactAvailability: is_string($rawContactAvailability) && $rawContactAvailability !== ''
                ? ContactAvailability::tryFrom($rawContactAvailability)
                : null,
        );
    }
}