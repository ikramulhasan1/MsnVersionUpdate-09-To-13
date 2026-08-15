<?php

declare(strict_types=1);

namespace App\Discovery\Search\DTO;

use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\DiscoverySortOption;
use App\Discovery\Enums\LastUpdatedRange;
use App\Discovery\Search\BooleanQueryParser;

/**
 * Normalized Website Discovery search filters, built from the raw
 * query-string array App\Http\Controllers\DiscoveryController::index()
 * receives (the same shape search-panel.blade.php's form submits and
 * DiscoveryController::search() round-trips — see that controller's
 * own docblock).
 *
 * Moved here from app/Discovery/Search/DiscoveryFilterCriteria.php
 * (Phase C6, where it only had $contactAvailability) into this DTO
 * subfolder as part of Phase D1 building out the rest of the fields —
 * every property below now has a matching apply*() method in
 * WebsiteSearchService, EXCEPT the ones this class deliberately still
 * leaves unparsed because no query can honestly be built from them yet
 * (see the "Deliberately NOT represented here" list below) — the same
 * "don't represent a filter that doesn't do anything" rule Phase C6
 * established, just re-affirmed per field now that most of them do.
 *
 * Deliberately NOT represented here, and why:
 *   - Website Status (status[]): no connectivity/reachability column
 *     exists on discovered_websites yet — see
 *     App\Discovery\Enums\WebsiteConnectivityStatus's own docblock.
 *   - Opportunity (opportunity[]): each case's criterion (see
 *     App\Discovery\Enums\OpportunityFilter::criterion()) needs a
 *     signal this table doesn't reliably have yet — e.g. "Security
 *     Opportunity (no HTTPS)" needs ssl_status, which
 *     EnrichDiscoveredWebsiteJob deliberately does not populate (out
 *     of that job's own score/grade/technology-only scope).
 *   - SEO Issues / Security Issues (issue[seo][]/issue[security][]):
 *     no per-site issue list is stored anywhere on discovered_websites
 *     — only aggregate scores are, and a specific issue can't be
 *     derived from a score alone.
 *   - Employee Estimate (employees[min]/[max]): no employee-count
 *     column exists on discovered_websites at all (only the coarser
 *     business_size band, which IS represented below).
 *   - Radius (radius): a radius search needs a center-point
 *     latitude/longitude to measure distance from, and the search
 *     panel's Radius field has no paired "search near this point"
 *     coordinate input yet — only a plain number, which alone isn't
 *     enough to build a meaningful distance query.
 */
final readonly class DiscoveryFilterCriteria
{
    /**
     * @param array<int, string> $websiteTypes raw App\Discovery\Enums\WebsiteType values
     * @param array<int, string> $businessSizes raw App\Discovery\Enums\BusinessSize values
     * @param array<string, array<int, string>> $technology group => slug list, e.g.
     *        ['cms' => ['wordpress'], 'framework' => ['react', 'bootstrap']] — group keys
     *        match search-panel.blade.php's technology[GROUP][] field names exactly
     *        (cms/framework/ecommerce_platform/cdn/server).
     * @param array<string, array{min: int, max: int}> $qualityRanges keyed by
     *        seo/performance/security/accessibility, each 0-100.
     * @param array<int, string> $trafficRanges raw App\Discovery\Enums\TrafficRange values
     * @param array<string, string> $socialPlatforms platform value (see
     *        App\Discovery\Enums\SocialPlatform) => 'has'|'missing'; a platform absent from
     *        this array means "don't filter on it" (the form's own "Any" option).
     * @param ?\App\Discovery\Enums\DiscoverySortOption $sort the Results section's "Sort By"
     *        dropdown value (Phase D4) — genuinely applied via WebsiteSearchService::query(),
     *        null (nothing selected / unrecognized value) falls back to that method's own
     *        default ordering.
     * @param array<int, \App\Discovery\Search\BooleanQueryTerm> $booleanTerms the "Boolean
     *        Query (Advanced)" field parsed via BooleanQueryParser (Phase F1) — see that
     *        class's own docblock for the exact AND/OR/NOT grammar it supports. Genuinely
     *        applied via WebsiteSearchService::applyBooleanQuery().
     */
    public function __construct(
        public ?string $industry = null,
        public ?string $subNiche = null,
        public ?string $country = null,
        public ?string $region = null,
        public ?string $city = null,
        public array $websiteTypes = [],
        public array $businessSizes = [],
        public array $technology = [],
        public array $qualityRanges = [],
        public ?int $domainAgeMinYears = null,
        public ?int $domainAgeMaxYears = null,
        public ?LastUpdatedRange $lastUpdated = null,
        public array $trafficRanges = [],
        public array $socialPlatforms = [],
        public ?ContactAvailability $contactAvailability = null,
        public ?DiscoverySortOption $sort = null,
        public array $booleanTerms = [],
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function fromRequestFilters(array $filters): self
    {
        return new self(
            industry: self::nonEmptyString($filters['industry'] ?? null),
            subNiche: self::nonEmptyString($filters['sub_niche'] ?? null),
            country: self::nonEmptyString($filters['country'] ?? null),
            region: self::nonEmptyString($filters['region'] ?? null),
            city: self::nonEmptyString($filters['city'] ?? null),
            websiteTypes: self::stringList($filters['website_type'] ?? null),
            businessSizes: self::stringList($filters['business_size'] ?? null),
            technology: self::technologyFilters($filters['technology'] ?? null),
            qualityRanges: self::qualityRanges($filters['quality'] ?? null),
            domainAgeMinYears: self::nonEmptyInt($filters['domain_age']['min'] ?? null),
            domainAgeMaxYears: self::nonEmptyInt($filters['domain_age']['max'] ?? null),
            lastUpdated: self::enumFrom(LastUpdatedRange::class, $filters['last_updated'] ?? null),
            trafficRanges: self::stringList($filters['traffic'] ?? null),
            socialPlatforms: self::socialPlatformFilters($filters['social'] ?? null),
            contactAvailability: self::enumFrom(ContactAvailability::class, $filters['contact_availability'] ?? null),
            sort: self::enumFrom(DiscoverySortOption::class, $filters['sort'] ?? null),
            booleanTerms: self::booleanTerms($filters['boolean_query'] ?? null),
        );
    }

    private static function nonEmptyString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function nonEmptyInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @return array<int, string>
     */
    private static function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_string($item) && $item !== ''));
    }

    /**
     * @template T of \BackedEnum
     * @param class-string<T> $enumClass
     * @return T|null
     */
    private static function enumFrom(string $enumClass, mixed $value): ?object
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        /** @var T|null */
        return $enumClass::tryFrom($value);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function technologyFilters(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $result = [];

        foreach ($value as $group => $slugs) {
            if (! is_string($group)) {
                continue;
            }

            $list = self::stringList($slugs);

            if ($list !== []) {
                $result[$group] = $list;
            }
        }

        return $result;
    }

    /**
     * @return array<string, array{min: int, max: int}>
     */
    private static function qualityRanges(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $result = [];

        foreach (['seo', 'performance', 'security', 'accessibility'] as $category) {
            $range = $value[$category] ?? null;

            if (! is_array($range)) {
                continue;
            }

            $min = self::nonEmptyInt($range['min'] ?? null) ?? 0;
            $max = self::nonEmptyInt($range['max'] ?? null) ?? 100;

            // Skip a range that covers the full 0-100 scale — the user
            // never moved the slider, so there is nothing to filter on
            // for this category, and adding a no-op WHERE clause would
            // only cost a query plan for no benefit.
            if ($min <= 0 && $max >= 100) {
                continue;
            }

            $result[$category] = ['min' => $min, 'max' => $max];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private static function socialPlatformFilters(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $result = [];

        foreach ($value as $platform => $choice) {
            if (is_string($platform) && in_array($choice, ['has', 'missing'], true)) {
                $result[$platform] = $choice;
            }
        }

        return $result;
    }

    /**
     * @return array<int, \App\Discovery\Search\BooleanQueryTerm>
     */
    private static function booleanTerms(mixed $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return (new BooleanQueryParser())->parse($value);
    }
}