<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Discovery\Enums\BusinessSize;
use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\DiscoverySortOption;
use App\Discovery\Enums\LastUpdatedRange;
use App\Discovery\Enums\OpportunityFilter;
use App\Discovery\Enums\ServerSoftware;
use App\Discovery\Enums\SocialPlatform;
use App\Discovery\Enums\TrafficRange;
use App\Discovery\Enums\WebsiteConnectivityStatus;
use App\Discovery\Enums\WebsiteType;
use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Discovery\Taxonomy\IndustryTaxonomyService;
use App\Discovery\Taxonomy\IssueFilterOptions;
use App\Discovery\Taxonomy\TechnologyFilterOptions;
use App\Http\Requests\SaveDiscoverySearchRequest;
use App\Http\Requests\SearchDiscoveryRequest;
use App\Models\DiscoveredWebsite;
use App\Models\DiscoverySearch;
use App\Models\DiscoveryWatchlistItem;use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Website Discovery — Phase A3 (routes + navbar) / A4 (page shell) /
 * B3 (search form UI) / C1 (Website Status & Type filters) / C2
 * (Technology filters) / C3 (Website Quality score sliders + specific
 * issue filters) / C4 (Opportunity filters) / C5 (Age, Business Size,
 * Traffic & Social filters) / C6 (Contact Availability — the first
 * filter wired to a real query) / D1 (WebsiteSearchService now applies
 * most Advanced Filters groups — see DiscoveryFilterCriteria's own
 * docblock for exactly which ones, and which are still deliberately
 * unrepresented) / D2 (search() now validates via
 * SearchDiscoveryRequest, and index() returns paginated results) / F3
 * (Saved Searches — searches()/storeSearch()/destroySearch()) / F4
 * (Scheduled Search — toggleScheduledSearch(); the actual scheduled
 * run + "N New Websites Found" count come from
 * App\Discovery\Jobs\RunScheduledDiscoverySearchJob, not this
 * controller) / G1 (Watchlist page — watchlist()).
 *
 * Wires up the module's routes (see routes/web.php) with real,
 * working controller actions and view/route-model-binding plumbing,
 * matching AuditController's own conventions (final class, uuid route-
 * model binding via DiscoveredWebsite::getRouteKeyName()). Industry/
 * Niche + most Advanced Filters are now real, applied filters (Phase
 * D1/D2) — see index()'s own docblock for the handful still
 * deliberately unrepresented and why.
 *
 * IndustryTaxonomyService/GeoLookupServiceInterface are injected here
 * (not resolved inside a view) purely to fetch the Industry list and
 * Country list up front for discovery/partials/search-panel.blade.php's
 * initial render — subNiches()/regions()/cities() below are the JSON
 * endpoints that same panel's JS (public/js/discovery-search-panel.js)
 * calls to cascade Sub-Niche/Region/City once a prior field is chosen,
 * following the same fetch()-based pattern public/js/audit-progress.js
 * already established for this app's vanilla-JS (no framework) rule.
 */
final class DiscoveryController extends Controller
{
    public function __construct(
        private readonly IndustryTaxonomyService $industryTaxonomy,
        private readonly GeoLookupServiceInterface $geoLookup,
        private readonly TechnologyFilterOptions $technologyFilterOptions,
        private readonly IssueFilterOptions $issueFilterOptions,
        private readonly WebsiteSearchService $websiteSearchService,
    ) {
    }

    /**
     * $industries/$countries are the two top-level dropdowns the
     * search panel can render server-side on first load; Sub-Niche/
     * Region/City start empty and are filled in by the panel's own JS
     * once Industry/Country/Region are chosen (see subNiches()/
     * regions()/cities() below) — cascading options for a value that
     * hasn't been picked yet would either be wrong (Sub-Niche for no
     * Industry) or unrealistically large (every city in the world), so
     * neither is fetched up front.
     *
     * $websiteStatuses/$websiteTypes (Phase C1) are the fixed enum
     * vocabularies behind the Advanced Filters panel's Website
     * Status/Website Type checkboxes — every case, always, since
     * neither list depends on any other field's value the way Sub-
     * Niche/Region/City do.
     *
     * $technologyGroups (Phase C2) is CMS/Framework/E-commerce
     * Platform/CDN, grouped live from TechnologyDetector's own
     * detection vocabulary via TechnologyFilterOptions (see that
     * class's own docblock for why); $serverSoftware is the Server
     * group's separately curated list, since TechnologyDetector has no
     * enumerated server-software vocabulary of its own to reuse.
     *
     * $seoIssues/$securityIssues (Phase C3) back the Advanced Filters
     * panel's "Specific Issues" checkboxes for the SEO/Security score-
     * range sliders, reused live from SeoAnalyzerService::ISSUE_LABELS
     * and SecurityAnalyzer::CHECK_NAMES via IssueFilterOptions — see
     * that class's own docblock. Performance/Accessibility get score
     * sliders too but no specific-issue checkboxes: neither analyzer
     * exposes a comparable public issue-code vocabulary yet.
     *
     * $opportunityFilters (Phase C4) is the "Opportunity" checkbox
     * group's fixed vocabulary — every case, always, same as
     * $websiteStatuses/$websiteTypes above. See
     * App\Discovery\Enums\OpportunityFilter's own docblock for why no
     * query logic reads this yet either.
     *
     * $businessSizes/$lastUpdatedRanges/$trafficRanges/$socialPlatforms
     * (Phase C5) back the remaining Advanced Filters groups — Business
     * Size (App\Discovery\Enums\BusinessSize, already established in
     * Phase A2 and finally wired into the filter UI here), Last
     * Updated, Est. Traffic, and Social Media presence. Domain Age and
     * Employee Estimate need no case list of their own — both are
     * plain numeric dual-range sliders, same technique as the Website
     * Quality score sliders (Phase C3).
     *
     * $contactAvailabilityOptions (Phase C6) backs the "Contact
     * Availability" radio group — the first Advanced Filters group
     * that is actually applied to $websites, via
     * DiscoveryFilterCriteria/WebsiteSearchService below, rather than
     * only submitting and round-tripping through the URL — see
     * DiscoveryFilterCriteria's own docblock for why this one filter
     * is wired end-to-end while every other group still isn't.
     *
     * $websites (Phase D2) is now a real LengthAwarePaginator, not a
     * flat Collection — see WebsiteSearchService::paginate().
     *
     * $sortOptions (Phase D4) backs the Results section's "Sort By"
     * dropdown — genuinely applied via WebsiteSearchService::query()'s
     * own applySort(), not UI-only, since every column it sorts by
     * already exists.
     */
    public function index(Request $request): View
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($request->query());

        return view('discovery.index', [
            'websites' => $this->websiteSearchService->paginate($criteria),
            'filters' => $request->query(),
            'industries' => $this->industryTaxonomy->industries(),
            'countries' => $this->geoLookup->countries(),
            'websiteStatuses' => WebsiteConnectivityStatus::cases(),
            'websiteTypes' => WebsiteType::cases(),
            'technologyGroups' => $this->technologyFilterOptions->all(),
            'serverSoftware' => ServerSoftware::cases(),
            'seoIssues' => $this->issueFilterOptions->seoIssues(),
            'securityIssues' => $this->issueFilterOptions->securityIssues(),
            'opportunityFilters' => OpportunityFilter::cases(),
            'businessSizes' => BusinessSize::cases(),
            'lastUpdatedRanges' => LastUpdatedRange::cases(),
            'trafficRanges' => TrafficRange::cases(),
            'socialPlatforms' => SocialPlatform::cases(),
            'contactAvailabilityOptions' => ContactAvailability::cases(),
            'sortOptions' => DiscoverySortOption::cases(),
        ]);
    }

    /**
     * Validates the submitted search form via SearchDiscoveryRequest
     * (Phase D2) — every enum-backed field checked against its real
     * enum's own cases (Rule::enum(), see that request class's own
     * docblock), every array/range field shape-checked — before
     * redirecting the now-validated values onto the index page's query
     * string. Redirecting with ->validated() rather than
     * ->except('_token') means only fields that actually passed
     * validation ever become part of the URL, so index()'s own
     * DiscoveryFilterCriteria::fromRequestFilters() never has to guess
     * whether a query-string value it's reading is trustworthy.
     *
     * Still a real, submittable, round-tripping endpoint (a
     * bookmarkable/shareable index URL, browser back/forward works,
     * the submitted values repopulate the form) — see this class's own
     * docblock for why redirecting to index() rather than rendering
     * results directly from here is the deliberate choice.
     */
    public function search(SearchDiscoveryRequest $request): RedirectResponse
    {
        return redirect()->route('discovery.index', $request->validated());
    }

    public function show(DiscoveredWebsite $website): View
    {
        return view('discovery.show', [
            'website' => $website,
            'isWatched' => $website->watchlistItem()->exists(),
        ]);
    }

    /**
     * updateOrCreate() rather than create(): discovery_watchlist's own
     * discovered_website_id column is unique (see database/migrations/
     * 2026_08_14_000002_create_discovery_watchlist_table.php's
     * docblock — a site can only be on the watchlist once), so
     * "watch" on an already-watched site is a harmless no-op update
     * rather than a duplicate-key error.
     */
    public function watch(DiscoveredWebsite $website): RedirectResponse
    {
        DiscoveryWatchlistItem::query()->updateOrCreate(
            ['discovered_website_id' => $website->id],
        );

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Added to your watchlist.');
    }

    

    /**
     * Backs the Watchlist page (Phase G1) — every DiscoveryWatchlistItem,
     * newest first, eager-loading discoveredWebsite so
     * discovery/watchlist.blade.php can reuse result-card.blade.php per
     * item without an N+1 query (the same eager-loading reasoning
     * WebsiteSearchService::query() already applies for the same
     * relation on the main results grid).
     */
    public function watchlist(): View
    {
        return view('discovery.watchlist', [
            'items' => DiscoveryWatchlistItem::query()->with('discoveredWebsite')->latest()->get(),
        ]);
    }

    /**
     * Backs the floating "Compare (N)" button's link
     * (public/js/discovery-compare.js, Phase E2) — reads ?websites[]=...
     * uuids, validates 2-5 (the same bounds that JS already enforces
     * client-side before ever letting the button appear/navigate, so
     * this is defensive backend validation for a malformed/hand-edited
     * URL, not the primary UX guard), and hands the matching
     * DiscoveredWebsite rows to discovery/compare.blade.php in the same
     * order they were selected (whereIn() alone doesn't guarantee
     * that — a comparison table where columns silently reorder between
     * visits would be confusing).
     */
    public function compare(Request $request): View|RedirectResponse
    {
        $uuids = array_values(array_unique(array_filter(
            (array) $request->query('websites', []),
            static fn (mixed $value): bool => is_string($value) && $value !== '',
        )));

        if (count($uuids) < 2 || count($uuids) > 5) {
            return redirect()
                ->route('discovery.index')
                ->with('status', 'Select 2 to 5 websites to compare.');
        }

        $websites = DiscoveredWebsite::query()->whereIn('uuid', $uuids)->get();

        $ordered = collect($uuids)
            ->map(static fn (string $uuid) => $websites->firstWhere('uuid', $uuid))
            ->filter()
            ->values();

        if ($ordered->count() < 2) {
            return redirect()
                ->route('discovery.index')
                ->with('status', 'Select 2 to 5 websites to compare.');
        }

        return view('discovery.compare', [
            'websites' => $ordered,
        ]);
    }

    /**
     * Backs the Map View's marker layer (public/js/discovery-map.js,
     * Phase E3) — the same DiscoveryFilterCriteria the current List
     * View results were built from (read straight off the current
     * query string, so switching views never shows a different result
     * set than what's on screen), but UNPAGINATED (via
     * WebsiteSearchService::search(), capped at 500) rather than one
     * page at a time: a map is only useful if it shows the full
     * geographic spread of matching results, not just the current
     * page's 20. Only sites with both latitude and longitude are
     * included — plotting a marker needs both, and neither is
     * populated by any current enrichment job yet (see
     * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own docblock),
     * so this may return an empty list until a future phase adds that.
     */
    public function mapData(Request $request): JsonResponse
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($request->query());

        $points = $this->websiteSearchService->search($criteria, 500)
            ->filter(static fn (DiscoveredWebsite $website): bool => $website->latitude !== null
                && $website->longitude !== null)
            ->map(static fn (DiscoveredWebsite $website): array => [
                'uuid' => $website->uuid,
                'name' => $website->business_name,
                'domain' => $website->domain,
                'lat' => (float) $website->latitude,
                'lng' => (float) $website->longitude,
                'seo_score' => $website->seo_score,
                'performance_score' => $website->performance_score,
                'security_score' => $website->security_score,
                'accessibility_score' => $website->accessibility_score,
                'show_url' => route('discovery.show', $website),
            ])
            ->values();

        return response()->json(['websites' => $points]);
    }

    /**
     * Backs the "Saved Searches" page (Phase F3) — every saved search,
     * newest first. No per-user scoping (see DiscoverySearch's own
     * docblock, and database/migrations/2026_08_14_000001_create_discovery_searches_table.php's
     * — user_id is nullable, and nothing in this module currently
     * requires authentication), so this lists every saved search
     * regardless of who saved it.
     */
    public function searches(): View
    {
        return view('discovery.saved', [
            'searches' => DiscoverySearch::query()->latest()->get(),
        ]);
    }

    /**
     * Backs the search panel's "Save this search" button (Phase F3) —
     * validated via SaveDiscoverySearchRequest (the same filter rules
     * SearchDiscoveryRequest already enforces, plus a required `name`
     * — see that request class's own docblock), then redirects back to
     * the results for those same filters (rather than to the Saved
     * Searches list) so saving a search doesn't interrupt whatever the
     * user was doing.
     *
     * uuid is generated here explicitly rather than by the model — see
     * DiscoverySearch's own docblock: it deliberately has no booted()
     * hook of its own, mirroring how Audit's own uuid is set by
     * AuditService::create() rather than the Audit model itself.
     */
    public function storeSearch(SaveDiscoverySearchRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $name = $validated['name'];
        unset($validated['name']);

        DiscoverySearch::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'filters' => $validated,
        ]);

        return redirect()
            ->route('discovery.index', $validated)
            ->with('status', 'Search saved as "'.$name.'".');
    }

    /**
     * Backs the "Delete" button on the Saved Searches page.
     */
    public function destroySearch(DiscoverySearch $search): RedirectResponse
    {
        $search->delete();

        return redirect()
            ->route('discovery.searches.index')
            ->with('status', 'Saved search removed.');
    }

    /**
     * Backs the Saved Searches page's "Enable/Disable Auto-Refresh"
     * button (Phase F4) — without this, is_scheduled could never become
     * true for any saved search, and RunScheduledDiscoverySearchJob
     * would have nothing to run against. A plain toggle rather than a
     * richer schedule-configuration UI (custom frequency, etc.): every
     * scheduled search currently shares the same hourly cadence (see
     * routes/console.php), so "on or off" is the only real choice to
     * expose yet.
     */
    public function toggleScheduledSearch(DiscoverySearch $search): RedirectResponse
    {
        $search->update(['is_scheduled' => ! $search->is_scheduled]);

        return redirect()
            ->route('discovery.searches.index')
            ->with('status', $search->is_scheduled
                ? 'Auto-refresh enabled for "'.$search->name.'".'
                : 'Auto-refresh disabled for "'.$search->name.'".');
    }

    /**
     * Backs the search panel's Sub-Niche dropdown: given ?industry=,
     * returns every sub-niche IndustryTaxonomyService knows for it
     * (empty for an unrecognized industry — see that service's own
     * docblock, never an error response).
     */
    public function subNiches(Request $request): JsonResponse
    {
        $industry = (string) $request->query('industry', '');

        return response()->json([
            'sub_niches' => $industry === '' ? [] : $this->industryTaxonomy->subNiches($industry),
        ]);
    }

    /**
     * Backs the search panel's Region dropdown: given ?country= (an
     * ISO 3166-1 alpha-2 code), returns every region
     * GeoLookupServiceInterface knows for it (empty for a country this
     * module has no region data for yet — see that interface's own
     * docblock).
     */
    public function regions(Request $request): JsonResponse
    {
        $country = (string) $request->query('country', '');

        return response()->json([
            'regions' => $country === '' ? [] : $this->geoLookup->regionsFor($country),
        ]);
    }

    /**
     * Backs the search panel's City dropdown: given ?country= and
     * optionally ?region=, returns every city GeoLookupServiceInterface
     * knows for that scope. Always returns an empty list today — see
     * GeoLookupServiceInterface::citiesFor()'s own docblock for why —
     * but is wired up now so the panel's cascading City field, and this
     * endpoint it calls, don't need to change when a future phase swaps
     * in a real geocoding-backed implementation.
     */
    public function cities(Request $request): JsonResponse
    {
        $country = (string) $request->query('country', '');
        $region = $request->query('region');

        return response()->json([
            'cities' => $country === '' ? [] : $this->geoLookup->citiesFor(
                $country,
                is_string($region) && $region !== '' ? $region : null,
            ),
        ]);
    }

    public function unwatch(DiscoveredWebsite $website): RedirectResponse
    {
        $website->watchlistItem()->delete();

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Removed from your watchlist.');
    }

}