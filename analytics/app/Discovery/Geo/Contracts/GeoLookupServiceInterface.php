<?php

declare(strict_types=1);

namespace App\Discovery\Geo\Contracts;

/**
 * Country/Region/City lookups for the Website Discovery module's
 * Location filter. A contract rather than calling
 * App\Discovery\Geo\JsonGeoLookupService directly anywhere: the
 * JSON-backed implementation is a deliberately lightweight starting
 * point (see that class's own docblock for what it can and can't do
 * yet), and every caller — search filter forms, validation, a future
 * enrichment pipeline — is expected to depend on this interface so a
 * later phase can swap in an external geocoding/places API
 * implementation (regions and especially cities for the whole world)
 * without any caller changing.
 */
interface GeoLookupServiceInterface
{
    /**
     * Every country this service knows about, each as {code, name} —
     * code is an ISO 3166-1 alpha-2 country code (e.g. "US", "GB"),
     * the same code discovered_websites.country is expected to store
     * (see database/migrations/2026_08_14_000000_create_discovered_websites_table.php's
     * own docblock on why that column is left a free-form string
     * rather than a DB-level enum: whichever value this service
     * returns here is what a caller should actually store/filter on).
     *
     * @return array<int, array{code: string, name: string}>
     */
    public function countries(): array;

    /**
     * Every region (state/province/similar top-level subdivision)
     * known for the given country, or an empty array for a country
     * this service has no region data for — never an exception, since
     * an unrecognized/uncovered country simply has no regions to
     * offer rather than being an error condition this service should
     * enforce.
     *
     * @return array<int, string>
     */
    public function regionsFor(string $countryCode): array;

    /**
     * Every city known for the given country (optionally narrowed to
     * one region within it), or an empty array when this service has
     * no city data for that scope. $region is optional since a caller
     * may want every known city in a country regardless of region.
     *
     * @return array<int, string>
     */
    public function citiesFor(string $countryCode, ?string $region = null): array;
}