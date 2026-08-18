<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

use App\Models\DiscoveredWebsite;

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
 * PRODUCTION INCIDENT, ROUND TWO — a first version of this fix wrapped
 * both queries in Cache::remember() with a 5-minute TTL, reasoning that
 * DISTINCT-querying on every search-panel render was wasteful since the
 * underlying data rarely changes between page loads. In practice this
 * caused real, confusing staleness instead: a person discovering new
 * websites (via "Discover More", a bulk audit sync, ...) and then
 * immediately checking the Industry dropdown would see it NOT yet
 * reflect what had just been added, for up to 5 minutes, with no
 * visible reason why — indistinguishable from the dropdown being
 * broken again. Given this app's actual traffic/data volume, a plain
 * DISTINCT query on an indexed column (industry/sub_niche are both
 * indexed — see database/migrations/2026_08_14_000000_create_discovered_websites_table.php)
 * is cheap enough not to need caching at all; correctness (the
 * dropdown always matching what's actually in the table, right now)
 * matters far more here than shaving a few milliseconds off a
 * low-traffic page's own query time. No caching layer at all now —
 * every call is a direct, live query.
 */
final class IndustryTaxonomyService
{
    /**
     * Every distinct, non-null industry value actually present on
     * discovered_websites, alphabetically — not config-driven, and not
     * cached, see this class's own docblock for why.
     *
     * @return array<int, string>
     */
    public function industries(): array
    {
        return DiscoveredWebsite::query()
            ->whereNotNull('industry')
            ->where('industry', '!=', '')
            ->distinct()
            ->orderBy('industry')
            ->pluck('industry')
            ->all();
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
        return DiscoveredWebsite::query()
            ->where('industry', $industry)
            ->whereNotNull('sub_niche')
            ->where('sub_niche', '!=', '')
            ->distinct()
            ->orderBy('sub_niche')
            ->pluck('sub_niche')
            ->all();
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