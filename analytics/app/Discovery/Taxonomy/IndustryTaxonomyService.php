<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

/**
 * Reads the Industry → Sub-Niche taxonomy from config/discovery.php —
 * the single source of truth for the Website Discovery module's
 * Industry/Niche search filters (see that config file's own docblock).
 *
 * PRODUCTION INCIDENT HISTORY — read before changing this class's own
 * data source again: this class briefly switched to reading DISTINCT
 * real values straight out of discovered_websites, reasoning that
 * App\Discovery\Sources\YelpBusinessSource's raw Yelp category text
 * (stored directly as `industry`) almost never matched one of this
 * curated taxonomy's own top-level names, making the dropdown
 * effectively useless. That fix solved the wrong half of the problem:
 * it made the dropdown always show SOMETHING that matched real data,
 * but at the cost of the dropdown itself becoming a messy list of raw
 * Yelp category strings — no more curated grouping, and sub_niche
 * (only ever populated from a business's own SECOND Yelp category, if
 * it even had one) was empty for almost everything, so the nice
 * Industry -> Sub-Niche cascade this class's own consumers
 * (search-panel.blade.php) were built around stopped working
 * meaningfully either way.
 *
 * The ACTUAL fix: this class goes back to being the curated taxonomy's
 * own reader (exactly as originally built) — the dropdown stays nice
 * and grouped. What changed instead is UPSTREAM:
 * App\Discovery\Taxonomy\YelpCategoryClassifier now maps a business's
 * raw Yelp category text ONTO this same curated taxonomy at discovery
 * time (see YelpBusinessSource's own docblock), so what actually gets
 * stored on discovered_websites.industry/sub_niche is drawn from THIS
 * class's own vocabulary in the first place — the dropdown and the
 * real data agree because the data was classified into the dropdown's
 * own terms, not because the dropdown gave up and started mirroring
 * whatever the data happened to say.
 *
 * The only class in the app that should read
 * config('discovery.industries') directly; everywhere else (search
 * filter forms, validation, YelpCategoryClassifier's own keyword map,
 * ...) goes through this service (or is itself the classifier that
 * feeds it) instead, so the config array's own shape can change
 * without every caller needing to change with it.
 *
 * Stateless and has no constructor dependencies — config() is a global
 * helper already, so injecting the config repository here would only
 * add ceremony without adding testability (the config array itself is
 * trivial to override in a test via config()->set(), same as any other
 * config-backed service in this codebase).
 */
final class IndustryTaxonomyService
{
    public function industries(): array
    {
        return array_keys($this->taxonomy());
    }

    public function subNiches(string $industry): array
    {
        return $this->taxonomy()[$industry] ?? [];
    }

    public function hasIndustry(string $industry): bool
    {
        return array_key_exists($industry, $this->taxonomy());
    }

    public function all(): array
    {
        return $this->taxonomy();
    }

    private function taxonomy(): array
    {
        /** @var array<string, array<int, string>> $industries */
        $industries = config('discovery.industries', []);

        return $industries;
    }
}