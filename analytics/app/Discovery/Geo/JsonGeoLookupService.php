<?php

declare(strict_types=1);

namespace App\Discovery\Geo;

use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;

/**
 * Reads country/region data from two static JSON files bundled
 * alongside this class (app/Discovery/Geo/data/countries.json and
 * regions.json) — a deliberately lightweight starting point for the
 * Website Discovery module's Location filter, chosen over a Composer
 * package (e.g. league/iso3166) so this module has no new external
 * dependency to install/verify for what is, for the country list at
 * least, a genuinely small, rarely-changing dataset.
 *
 * Resolved via __DIR__ (a PHP-native constant that always points to
 * THIS file's own directory, regardless of how Laravel's own path
 * helpers are configured), NOT storage_path() — this is a fix, not a
 * style preference: on at least one real shared-hosting deployment of
 * this app, storage_path('app/geo/countries.json') silently resolved
 * to a location where the file didn't exist. readJsonFile()'s own
 * is_file()/is_readable() guard then quietly returned an empty array
 * rather than throwing, so the symptom wasn't a crash — it was the
 * Country dropdown rendering with zero options and no error anywhere
 * to explain why. Storing the data next to this class and resolving it
 * with __DIR__ removes the dependency on Laravel's base_path()/
 * storage_path() detection working correctly on any given host
 * entirely; a plain PHP constant can't be misconfigured by a hosting
 * environment's document-root/symlink setup the way those helpers can.
 *
 * What this implementation can and can't do, by design:
 *   - countries(): the full ISO 3166-1 alpha-2 country list — small
 *     (~190 entries) and stable enough to bake in outright.
 *   - regionsFor(): only covers a handful of major countries (US, CA,
 *     GB, AU, IN) as a starter set, not every country's subdivisions
 *     — baking every country's full region list is a much bigger,
 *     less stable dataset than the country list itself.
 *   - citiesFor(): ALWAYS returns an empty array. Baking city data
 *     for the whole world isn't realistic (millions of cities, and
 *     "which cities matter" depends entirely on population/relevance
 *     thresholds this module doesn't have an opinion on yet) — a real
 *     implementation needs a live geocoding/places API, not a static
 *     file.
 *
 * This gap is exactly why GeoLookupServiceInterface exists as a
 * separate contract from this class: a later phase can bind a new
 * ApiGeoLookupService (or similar) to that interface — swapping the
 * regions/cities implementation for a real geocoding API — without
 * any caller of the interface changing. See
 * App\Providers\DiscoveryServiceProvider for where that binding lives
 * today (bound to this class) and where it would be repointed later.
 *
 * JSON is decoded once per request and cached on the instance
 * (Laravel resolves this class fresh per request in the default
 * container lifecycle, so an instance-level cache is enough — no
 * static state, which would leak between requests under a
 * long-running worker like Octane).
 */
final class JsonGeoLookupService implements GeoLookupServiceInterface
{
    /**
     * @var array<int, array{code: string, name: string}>|null
     */
    private ?array $countriesCache = null;

    /**
     * @var array<string, array<int, string>>|null
     */
    private ?array $regionsCache = null;

    public function __construct(
        private readonly string $countriesPath = __DIR__.'/data/countries.json',
        private readonly string $regionsPath = __DIR__.'/data/regions.json',
    ) {}

    /**
     * @return array<int, array{code: string, name: string}>
     */
    public function countries(): array
    {
        return $this->countriesCache ??= $this->readJsonFile($this->countriesPath, []);
    }

    /**
     * @return array<int, string>
     */
    public function regionsFor(string $countryCode): array
    {
        $regions = $this->regionsCache ??= $this->readJsonFile($this->regionsPath, []);

        return $regions[strtoupper($countryCode)] ?? [];
    }

    /**
     * Always empty — see this class's own docblock for why city data
     * isn't baked into a static file, and where a real implementation
     * belongs instead.
     *
     * @return array<int, string>
     */
    public function citiesFor(string $countryCode, ?string $region = null): array
    {
        return [];
    }

    /**
     * @param  array<string|int, mixed>  $default
     * @return array<mixed>
     */
    private function readJsonFile(string $fullPath, array $default): array
    {
        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            return $default;
        }

        $contents = file_get_contents($fullPath);

        if ($contents === false) {
            return $default;
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : $default;
    }
}
