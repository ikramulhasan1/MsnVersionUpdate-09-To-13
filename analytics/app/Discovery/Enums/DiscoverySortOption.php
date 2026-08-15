<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Results section's "Sort By" dropdown
 * (resources/views/discovery/index.blade.php, Phase D4) — genuinely
 * wired into App\Discovery\Search\WebsiteSearchService::query(), not
 * UI-only, since every column it sorts by already exists and is
 * queryable.
 *
 * RELEVANCE and RECENTLY_DISCOVERED currently produce the IDENTICAL
 * ordering (both sort by discovered_at descending) — there is no
 * relevance-scoring algorithm in this module (no full-text search rank,
 * no filter-match-count weighting, nothing beyond the filters
 * WebsiteSearchService already applies as plain WHERE clauses), so
 * "Relevance" honestly has nothing more sophisticated to fall back to
 * yet than "newest first". Kept as two separate options anyway (rather
 * than only offering "Recently Discovered") since a future relevance
 * algorithm should be able to slot in as RELEVANCE's own ordering
 * without renaming a dropdown option a user may already be used to.
 */
enum DiscoverySortOption: string
{
    case RELEVANCE = 'relevance';
    case OPPORTUNITY = 'opportunity';
    case SEO = 'seo';
    case PERFORMANCE = 'performance';
    case DOMAIN_AGE = 'domain_age';
    case TRAFFIC = 'traffic';
    case RECENTLY_DISCOVERED = 'recently_discovered';

    public function label(): string
    {
        return match ($this) {
            self::RELEVANCE => 'Relevance',
            self::OPPORTUNITY => 'Opportunity Score',
            self::SEO => 'SEO Score',
            self::PERFORMANCE => 'Performance Score',
            self::DOMAIN_AGE => 'Domain Age',
            self::TRAFFIC => 'Traffic',
            self::RECENTLY_DISCOVERED => 'Recently Discovered',
        };
    }
}