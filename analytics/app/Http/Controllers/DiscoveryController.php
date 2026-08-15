<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Discovery\Enums\BusinessSize;
use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\LastUpdatedRange;
use App\Discovery\Enums\OpportunityFilter;
use App\Discovery\Enums\ServerSoftware;
use App\Discovery\Enums\SocialPlatform;
use App\Discovery\Enums\TrafficRange;
use App\Discovery\Enums\WebsiteConnectivityStatus;
use App\Discovery\Enums\WebsiteType;
use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Search\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Discovery\Taxonomy\IndustryTaxonomyService;
use App\Discovery\Taxonomy\IssueFilterOptions;
use App\Discovery\Taxonomy\TechnologyFilterOptions;
use App\Models\DiscoveredWebsite;
use App\Models\DiscoveryWatchlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Website Discovery — Phase A3 (routes + navbar) / A4 (page shell) /
 * B3 (search form UI) / C1 (Website Status & Type filters) / C2
 * (Technology filters) / C3 (Website Quality score sliders + specific
 * issue filters) / C4 (Opportunity filters) / C5 (Age, Business Size,
 * Traffic & Social filters) / C6 (Contact Availability — the first
 * filter actually wired to a real query).
 *
 * Wires up the module's routes (see routes/web.php) with real,
 * working controller actions and view/route-model-binding plumbing,
 * matching AuditController's own conventions (final class, uuid route-
 * model binding via DiscoveredWebsite::getRouteKeyName()). The actual
 * Industry/Niche + advanced-filter *query* logic (applying a submitted
 * filter to $websites) is intentionally NOT implemented here — that's
 * a later phase's work; see index()'s own docblock.
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
     */
    public function index(Request $request): View
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($request->query());

        return view('discovery.index', [
            'websites' => $this->websiteSearchService->search($criteria),
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
        ]);
    }

    /**
     * Round-trips whatever filter fields were submitted back onto the
     * index page as query parameters, so the search form behaves like
     * a real search bar (a bookmarkable/shareable URL, browser back/
     * forward works, the submitted values repopulate the form) even
     * before index() actually applies them to a query — see this
     * class's own docblock for why that part is deferred to a later
     * phase.
     */
    public function search(Request $request): RedirectResponse
    {
        return redirect()->route('discovery.index', $request->except('_token'));
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

    public function unwatch(DiscoveredWebsite $website): RedirectResponse
    {
        $website->watchlistItem()->delete();

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Removed from your watchlist.');
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
}