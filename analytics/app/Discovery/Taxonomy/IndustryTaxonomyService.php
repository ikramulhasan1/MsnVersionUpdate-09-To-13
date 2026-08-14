<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

/**
 * Reads the Industry → Sub-Niche taxonomy from config/discovery.php —
 * the single source of truth for the Website Discovery module's
 * Industry/Niche search filters (see that config file's own docblock).
 *
 * The only class in the app that should read
 * config('discovery.industries') directly; everywhere else (search
 * filter forms, validation, a future DiscoveredWebsite factory/seeder,
 * ...) goes through this service instead, so the config array's own
 * shape can change without every caller needing to change with it.
 *
 * Stateless and has no constructor dependencies — config() is a global
 * helper already, so injecting the config repository here would only
 * add ceremony without adding testability (the config array itself is
 * trivial to override in a test via config()->set(), same as any other
 * config-backed service in this codebase).
 */
final class IndustryTaxonomyService
{
    /**
     * Every top-level industry name, in the order config/discovery.php
     * defines them.
     *
     * @return array<int, string>
     */
    public function industries(): array
    {
        return array_keys($this->taxonomy());
    }

    /**
     * Every sub-niche defined under the given industry, or an empty
     * array for an industry name that isn't in the taxonomy — never an
     * exception, since an unrecognized industry (e.g. stale data, or a
     * typo in a search request) simply has no sub-niches to offer
     * rather than being an error condition this service should enforce.
     *
     * @return array<int, string>
     */
    public function subNiches(string $industry): array
    {
        return $this->taxonomy()[$industry] ?? [];
    }

    /**
     * Whether $industry is one of the taxonomy's own top-level industry
     * names — useful for validating a search filter or an enrichment
     * result against the known taxonomy before it's stored/searched on.
     */
    public function hasIndustry(string $industry): bool
    {
        return array_key_exists($industry, $this->taxonomy());
    }

    /**
     * The full Industry => [Sub-Niche, ...] taxonomy, for callers that
     * need the whole structure at once (e.g. rendering a grouped
     * <select>/filter UI) rather than one industry's sub-niches at a
     * time.
     *
     * @return array<string, array<int, string>>
     */
    public function all(): array
    {
        return $this->taxonomy();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function taxonomy(): array
    {
        /** @var array<string, array<int, string>> $industries */
        $industries = config('discovery.industries', []);

        return $industries;
    }
}