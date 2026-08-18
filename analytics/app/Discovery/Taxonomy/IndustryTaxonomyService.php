<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

use App\Models\DiscoveredWebsite;
use Illuminate\Support\Facades\Cache;

/**
 * PRODUCTION INCIDENT — read before reverting to config('discovery.industries'):
 * this class originally read a fixed, hand-curated 21-industry taxonomy
 * from config/discovery.php. That worked fine as a UI placeholder before
 * any real discovery source existed, but once App\Discovery\Sources\YelpBusinessSource
 * started actually populating discovered_websites.industry with Yelp's
 * OWN raw category text (e.g. "Men's Clothing", "Italian Restaurant")
 * — which essentially never matches any of the 21 curated top-level
 * names ("Retail & E-commerce", "Restaurant & Food Service", ...) — the
 * Industry filter became close to useless in practice: picking a
 * curated name from the dropdown and searching would very often return
 * zero results even when plenty of matching sites existed, because
 * their own `industry` column simply never said that.
 *
 * The fix: this class now reads DISTINCT real values straight out of
 * discovered_websites itself, so the dropdown can only ever offer
 * values that actually exist to filter on. config('discovery.industries')
 * is no longer read by this class at all (see config/discovery.php's
 * own docblock, which still documents the array for historical
 * context/possible future re-use, e.g. as a seed list for a future
 * industry-classifier, but it's no longer this module's source of
 * truth for what the Industry/Sub-Niche dropdowns show).
 *
 * Sub-niche follows the exact same "distinct real values" approach,
 * scoped to whichever industry was asked for — see subNiches()'s own
 * docblock for why sub_niche in particular was almost entirely empty
 * before this fix, independent of the dropdown-mismatch problem above.
 *
 * Cached briefly (see self::CACHE_TTL_SECONDS) rather than querying
 * DISTINCT on every single search-panel render — the underlying data
 * only actually changes when a new site is discovered/enriched, not on
 * every page load, and a short TTL keeps a newly-discovered industry
 * showing up in the dropdown within a minute or two without needing an
 * explicit cache-bust anywhere.
 */
final class IndustryTaxonomyService
{
    private const int CACHE_TTL_SECONDS = 300;

    /**
     * Every distinct, non-null industry value actually present on
     * discovered_websites, alphabetically — not config-driven anymore,
     * see this class's own docblock for why.
     *
     * @return array<int, string>
     */
    public function industries(): array
    {
        return Cache::remember('discovery-taxonomy:industries', self::CACHE_TTL_SECONDS, static function (): array {
            return DiscoveredWebsite::query()
                ->whereNotNull('industry')
                ->where('industry', '!=', '')
                ->distinct()
                ->orderBy('industry')
                ->pluck('industry')
                ->all();
        });
    }

    /**
     * Every distinct, non-null sub_niche value actually present on
     * discovered_websites FOR $industry specifically, alphabetically.
     *
     * Returns an empty array for most industries today, honestly — not
     * a bug, a direct consequence of real data: only
     * App\Discovery\Sources\YelpBusinessSource populates sub_niche (as
     * of the same fix this class's own docblock describes), and only
     * from a business's own SECOND Yelp category, which many Yelp
     * listings simply don't have. As more sites are discovered with a
     * real second category, more industries will show real sub-niches
     * here automatically — no code change needed when that happens,
     * since this reads live data rather than a fixed list.
     *
     * @return array<int, string>
     */
    public function subNiches(string $industry): array
    {
        $cacheKey = 'discovery-taxonomy:sub-niches:' . md5($industry);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, static function () use ($industry): array {
            return DiscoveredWebsite::query()
                ->where('industry', $industry)
                ->whereNotNull('sub_niche')
                ->where('sub_niche', '!=', '')
                ->distinct()
                ->orderBy('sub_niche')
                ->pluck('sub_niche')
                ->all();
        });
    }

    /**
     * Whether $industry is one of the real, currently-discovered
     * industry values — kept for backward compatibility with any
     * existing caller (e.g. validating an enrichment result before it's
     * stored/searched on), now checking against real data instead of
     * the old fixed taxonomy.
     */
    public function hasIndustry(string $industry): bool
    {
        return in_array($industry, $this->industries(), true);
    }

    /**
     * The full Industry => [Sub-Niche, ...] structure, built from the
     * same real, distinct data industries()/subNiches() each read —
     * for a caller that needs the whole structure at once (e.g.
     * rendering a grouped <select>/filter UI) rather than one
     * industry's sub-niches at a time.
     *
     * @return array<string, array<int, string>>
     */
    public function all(): array
    {
        $industries = $this->industries();

        $result = [];

        foreach ($industries as $industry) {
            $result[$industry] = $this->subNiches($industry);
        }

        return $result;
    }
}