<?php

declare(strict_types=1);

namespace App\Discovery\Search;

use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\DiscoverySortOption;
use App\Discovery\Enums\LastUpdatedRange;
use App\Discovery\Search\BooleanQueryOperator;
use App\Discovery\Search\BooleanQueryTerm;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Models\DiscoveredWebsite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Builds and runs the Website Discovery search query from a
 * DiscoveryFilterCriteria — this module's real search backend (see
 * that DTO's own docblock for exactly which filters are wired in here
 * vs. still deliberately unrepresented, and why).
 *
 * Follows App\Audit\Repositories\AuditRepository's own query-building
 * conventions: a constructor-injected model instance
 * (`$this->model->newQuery()`, not the static DiscoveredWebsite::query()
 * facade call), one small private method per concern with a docblock
 * explaining *why* that query shape was chosen — not just *what* it
 * does — and no query logic duplicated between methods.
 *
 * query() is exposed separately from search()/paginate() so a future
 * phase can compose additional clauses (sorting, eager-loading the
 * watchlist relationship for the results list, ...) onto the same
 * Builder without this class needing a combinatorial explosion of
 * search-with-this-option-or-that-option methods.
 */
final class WebsiteSearchService
{
    /**
     * discovered_websites.cms/framework/ecommerce_platform/cdn/server
     * hold EnrichDiscoveredWebsiteJob's own comma-joined DISPLAY NAMES
     * (e.g. "WordPress", "React, Bootstrap"), not the lowercase slugs
     * the Technology filter's checkboxes submit (e.g. "wordpress",
     * "react") — see that job's technologyColumnValue() and
     * App\Discovery\Taxonomy\TechnologyFilterOptions, which both share
     * TechnologyDetector::TECHNOLOGY_NAMES as their slug->display-name
     * source. applyTechnology() below bridges the two with a
     * case-insensitive LIKE (SQL LIKE is case-insensitive for ASCII on
     * both this app's SQLite and a MySQL/PostgreSQL production
     * database under their respective default collations) rather than
     * an exact whereIn match, which would never match a submitted slug
     * against a stored display name.
     *
     * @var array<string, string> filter group => discovered_websites column
     */
    private const array TECHNOLOGY_GROUP_COLUMNS = [
        'cms' => 'cms',
        'framework' => 'framework',
        'ecommerce_platform' => 'ecommerce_platform',
        'cdn' => 'cdn',
        'server' => 'server',
    ];

    /**
     * @var array<string, string> quality range key => discovered_websites score column
     */
    private const array QUALITY_SCORE_COLUMNS = [
        'seo' => 'seo_score',
        'performance' => 'performance_score',
        'security' => 'security_score',
        'accessibility' => 'accessibility_score',
    ];

    /**
     * The free-text columns each Boolean Query term (Phase F1) is
     * matched against — a fixed, deliberately narrow set of columns
     * that genuinely hold free-text/short-identifier data (a business
     * name, a domain, an industry/sub-niche label, or a technology
     * name), not every column on the table. Score/date/enum columns
     * are excluded on purpose: LIKE-matching a search term like
     * "WordPress" against a numeric score column would never
     * meaningfully match anything, so including it would only slow the
     * query down for no benefit.
     *
     * @var array<int, string>
     */
    private const array BOOLEAN_SEARCHABLE_COLUMNS = [
        'business_name',
        'domain',
        'industry',
        'sub_niche',
        'cms',
        'framework',
        'ecommerce_platform',
        'server',
        'cdn',
    ];

    public function __construct(
        private readonly DiscoveredWebsite $model,
    ) {
    }

    /**
     * @return Collection<int, DiscoveredWebsite>
     */
    public function search(DiscoveryFilterCriteria $criteria, int $limit = 50): Collection
    {
        return $this->query($criteria)->limit($limit)->get();
    }

    /**
     * The paginated counterpart to search() —
     * App\Http\Controllers\DiscoveryController::index() uses this one
     * (Phase D2) so the search results page can page through more than
     * a single fixed-size batch; search() itself is left in place for
     * any future caller (e.g. an API endpoint, a CLI command) that
     * genuinely wants a flat, unpaginated batch instead.
     * withQueryString() carries every current filter (and any other
     * query-string value, e.g. the Sort By dropdown's own `sort` value
     * — Phase D4) onto each pagination link automatically, so paging to
     * page 2 never silently drops the search/sort that produced these
     * results.
     */
    public function paginate(DiscoveryFilterCriteria $criteria, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($criteria)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function query(DiscoveryFilterCriteria $criteria): Builder
    {
        // Eager-loads watchlistItem so a results list (see
        // partials/result-card.blade.php's Save/Watch button, Phase D3)
        // can check "is this site already watchlisted?" per row without
        // an N+1 query — one extra query for the whole page of results
        // instead of one per card.
        $query = $this->model->newQuery()->with('watchlistItem');

        $this->applyIndustry($query, $criteria);
        $this->applyLocation($query, $criteria);
        $this->applyWebsiteTypes($query, $criteria);
        $this->applyBusinessSizes($query, $criteria);
        $this->applyTechnology($query, $criteria);
        $this->applyQualityRanges($query, $criteria);
        $this->applyDomainAge($query, $criteria);
        $this->applyLastUpdated($query, $criteria);
        $this->applyTrafficRanges($query, $criteria);
        $this->applySocialPlatforms($query, $criteria);
        $this->applyContactAvailability($query, $criteria->contactAvailability);
        $this->applySort($query, $criteria->sort);
        $this->applyBooleanQuery($query, $criteria->booleanTerms);

        return $query;
    }

    /**
     * Sub-Niche is only ever meaningful alongside an Industry (the
     * search panel's own Sub-Niche <select> stays disabled until an
     * Industry is chosen — see search-panel.blade.php), so it's applied
     * here rather than its own method: there is no query shape where
     * subNiche is set but industry isn't.
     */
    private function applyIndustry(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->industry !== null) {
            $query->where('industry', $criteria->industry);
        }

        if ($criteria->subNiche !== null) {
            $query->where('sub_niche', $criteria->subNiche);
        }
    }

    private function applyLocation(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->country !== null) {
            $query->where('country', $criteria->country);
        }

        if ($criteria->region !== null) {
            $query->where('region', $criteria->region);
        }

        if ($criteria->city !== null) {
            $query->where('city', $criteria->city);
        }
    }

    private function applyWebsiteTypes(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->websiteTypes !== []) {
            $query->whereIn('website_type', $criteria->websiteTypes);
        }
    }

    private function applyBusinessSizes(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->businessSizes !== []) {
            $query->whereIn('business_size', $criteria->businessSizes);
        }
    }

    /**
     * See this class's own TECHNOLOGY_GROUP_COLUMNS docblock for why
     * this is a LIKE match rather than whereIn. Each selected value
     * within a group is OR'd together (checking both "WordPress" and
     * "Shopify" for ecommerce_platform should match a site with
     * either), while different groups are AND'd together (a Framework
     * filter and a CMS filter both narrow the results, independently).
     */
    private function applyTechnology(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        foreach ($criteria->technology as $group => $slugs) {
            $column = self::TECHNOLOGY_GROUP_COLUMNS[$group] ?? null;

            if ($column === null || $slugs === []) {
                continue;
            }

            $query->where(function (Builder $groupQuery) use ($column, $slugs): void {
                foreach ($slugs as $slug) {
                    $groupQuery->orWhere($column, 'like', '%'.$slug.'%');
                }
            });
        }
    }

    /**
     * DiscoveryFilterCriteria::qualityRanges() already drops any
     * category left at the full 0-100 default, so every entry that
     * reaches here genuinely narrows the results.
     */
    private function applyQualityRanges(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        foreach ($criteria->qualityRanges as $category => $range) {
            $column = self::QUALITY_SCORE_COLUMNS[$category] ?? null;

            if ($column !== null) {
                $query->whereBetween($column, [$range['min'], $range['max']]);
            }
        }
    }

    /**
     * The Domain Age filter is collected in years (see
     * search-panel.blade.php's own "Domain Age (years)" slider), but
     * discovered_websites.domain_age_days is stored in days — converted
     * here, not in the DTO, since the DTO's job is to normalize *what*
     * was submitted, not translate it into another unit a specific
     * query implementation happens to need. The max end of the slider
     * (20 years) is treated as open-ended ("20+") rather than an exact
     * upper bound, matching the "+" suffix the slider's own label
     * already shows at that value.
     */
    private function applyDomainAge(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->domainAgeMinYears === null && $criteria->domainAgeMaxYears === null) {
            return;
        }

        $minYears = $criteria->domainAgeMinYears ?? 0;
        $maxYears = $criteria->domainAgeMaxYears ?? 20;

        if ($minYears <= 0 && $maxYears >= 20) {
            return;
        }

        $query->where('domain_age_days', '>=', $minYears * 365);

        if ($maxYears < 20) {
            $query->where('domain_age_days', '<=', $maxYears * 365);
        }
    }

    /**
     * Every LastUpdatedRange case except OVER_YEAR means "at or after
     * this cutoff" (a recency floor); OVER_YEAR is the one case that
     * means the opposite — "before this cutoff" (a staleness ceiling)
     * — see LastUpdatedRange's own docblock for the full bucket list.
     */
    private function applyLastUpdated(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->lastUpdated === null) {
            return;
        }

        if ($criteria->lastUpdated === LastUpdatedRange::OVER_YEAR) {
            $query->where('last_updated_at', '<', now()->subYear());

            return;
        }

        $cutoff = match ($criteria->lastUpdated) {
            LastUpdatedRange::WITHIN_WEEK => now()->subWeek(),
            LastUpdatedRange::WITHIN_MONTH => now()->subMonth(),
            LastUpdatedRange::WITHIN_3_MONTHS => now()->subMonths(3),
            LastUpdatedRange::WITHIN_6_MONTHS => now()->subMonths(6),
            LastUpdatedRange::WITHIN_YEAR => now()->subYear(),
            LastUpdatedRange::OVER_YEAR => now()->subYear(), // unreachable, handled above
        };

        $query->where('last_updated_at', '>=', $cutoff);
    }

    /**
     * Matches discovered_websites.estimated_traffic_range against each
     * selected TrafficRange case's own raw enum value (e.g.
     * 'under_1k'), NOT its display label ("Est. Traffic: Under 1K/mo")
     * — this presumes a future enrichment step stores that same raw
     * value in the column, matching how every other enum-backed column
     * in this table already works (e.g. website_type stores
     * WebsiteType's raw value, not WebsiteType::label()'s text). No
     * current job populates this column yet (see
     * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own docblock for
     * what it does and doesn't write), so this filter is real query
     * logic sitting ahead of the data that will eventually satisfy it
     * — harmless (it simply matches nothing) until that data exists.
     */
    private function applyTrafficRanges(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        if ($criteria->trafficRanges !== []) {
            $query->whereIn('estimated_traffic_range', $criteria->trafficRanges);
        }
    }

    /**
     * social_profiles is a JSON object keyed by platform (see
     * DiscoveredWebsite::$casts — 'social_profiles' => 'array' — and
     * App\Discovery\Enums\SocialPlatform's own docblock for why the
     * five platform keys match ContactInfoExtractor::SOCIAL_PLATFORM_PATTERNS
     * exactly). "Has" a platform means that key exists with a non-null
     * value; "Doesn't Have" means the reverse. Laravel translates the
     * `column->key` path syntax into the correct JSON operator for
     * whichever database driver is active (MySQL/PostgreSQL/SQLite all
     * support it), so this same code works unchanged across this app's
     * local SQLite database and a MySQL/PostgreSQL production one.
     */
    private function applySocialPlatforms(Builder $query, DiscoveryFilterCriteria $criteria): void
    {
        foreach ($criteria->socialPlatforms as $platform => $choice) {
            $path = 'social_profiles->'.$platform;

            match ($choice) {
                'has' => $query->whereNotNull($path),
                'missing' => $query->whereNull($path),
                default => null,
            };
        }
    }

    /**
     * See ContactAvailability's own docblock for why CONTACT_FORM is
     * approximated as "contact_page_url is not null", and why this is
     * a radio-style single choice rather than several independent
     * checkbox clauses. Unchanged from Phase C6 — this is the filter
     * that was already wired end-to-end before the rest of this class
     * existed.
     */
    private function applyContactAvailability(Builder $query, ?ContactAvailability $availability): void
    {
        match ($availability) {
            null => null,
            ContactAvailability::EMAIL => $query->whereNotNull('email'),
            ContactAvailability::PHONE => $query->whereNotNull('phone'),
            ContactAvailability::CONTACT_FORM => $query->whereNotNull('contact_page_url'),
            ContactAvailability::NONE => $query
                ->whereNull('email')
                ->whereNull('phone')
                ->whereNull('contact_page_url'),
        };
    }

    /**
     * See DiscoverySortOption's own docblock for why RELEVANCE and
     * RECENTLY_DISCOVERED produce identical ordering today, and for why
     * both are still offered as separate dropdown options. TRAFFIC
     * sorts the raw estimated_traffic_range string column
     * alphabetically ('100k_500k' before 'under_1k', etc.) rather than
     * by real traffic magnitude — a semantically-correct ordering would
     * need a CASE/FIELD expression mapping each bucket to a rank, which
     * isn't worth building yet for a column no current job populates
     * (see DiscoveryFilterCriteria's own docblock on
     * $trafficRanges/estimated_traffic_range).
     */
    private function applySort(Builder $query, ?DiscoverySortOption $sort): void
    {
        match ($sort) {
            null, DiscoverySortOption::RELEVANCE, DiscoverySortOption::RECENTLY_DISCOVERED => $query->orderByDesc('discovered_at'),
            DiscoverySortOption::OPPORTUNITY => $query->orderByDesc('opportunity_score'),
            DiscoverySortOption::SEO => $query->orderByDesc('seo_score'),
            DiscoverySortOption::PERFORMANCE => $query->orderByDesc('performance_score'),
            DiscoverySortOption::DOMAIN_AGE => $query->orderByDesc('domain_age_days'),
            DiscoverySortOption::TRAFFIC => $query->orderByDesc('estimated_traffic_range'),
        };
    }

    /**
     * Wraps every BooleanQueryTerm in a single nested WHERE group
     * (`$query->where(function ($group) { ... })`) applied strictly in
     * the order BooleanQueryParser produced them — AND/OR/NOT inside
     * that closure only relate the boolean-query terms to EACH OTHER;
     * the closure as a whole is then ANDed onto the rest of the query's
     * filters. This nesting is required, not cosmetic: calling
     * orWhere()/whereNot() directly on the outer $query (no wrapping
     * closure) would OR/negate against every OTHER filter already
     * applied above (industry, location, technology, ...), not just
     * the other boolean-query terms — a real correctness bug a flatter
     * implementation would have. See BooleanQueryParser's own docblock
     * for why this has no operator precedence/parentheses beyond that
     * single flat group — the query built here is exactly as literal as
     * that left-to-right parse, nothing cleverer layered on top of it.
     *
     * @param array<int, BooleanQueryTerm> $terms
     */
    private function applyBooleanQuery(Builder $query, array $terms): void
    {
        if ($terms === []) {
            return;
        }

        $query->where(function (Builder $group) use ($terms): void {
            foreach ($terms as $term) {
                if ($term->operator === BooleanQueryOperator::NOT) {
                    $group->whereNot(fn (Builder $sub) => $this->matchTermAcrossColumns($sub, $term->term));

                    continue;
                }

                if ($term->operator === BooleanQueryOperator::OR) {
                    $group->orWhere(fn (Builder $sub) => $this->matchTermAcrossColumns($sub, $term->term));

                    continue;
                }

                $group->where(fn (Builder $sub) => $this->matchTermAcrossColumns($sub, $term->term));
            }
        });
    }

    /**
     * A single term matches if it LIKE-appears in ANY of
     * BOOLEAN_SEARCHABLE_COLUMNS — e.g. "WordPress" could be found in
     * the cms column just as easily as in a hypothetical business name
     * containing the word, so every candidate column is checked rather
     * than guessing which one field the user meant.
     */
    private function matchTermAcrossColumns(Builder $query, string $term): void
    {
        foreach (self::BOOLEAN_SEARCHABLE_COLUMNS as $column) {
            $query->orWhere($column, 'like', '%'.$term.'%');
        }
    }
}