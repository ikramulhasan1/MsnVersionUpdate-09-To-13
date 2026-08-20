<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Enums\AuditMode;
use App\Audit\Enums\AuditStatus;
use App\Audit\Export\Support\AnalysisResultsToDashboardCategories;
use App\Audit\Services\BulkAuditBatchService;
use App\Discovery\Analytics\SearchAnalyticsService;
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
use App\Discovery\Export\DiscoveredWebsitesToExportRows;
use App\Discovery\Export\DiscoveryResultsExport;
use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Jobs\DiscoverWebsitesJob;
use App\Discovery\Normalization\DomainNormalizer;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Discovery\Taxonomy\IndustryTaxonomyService;
use App\Discovery\Taxonomy\IssueFilterOptions;
use App\Discovery\Taxonomy\TechnologyFilterOptions;
use App\Http\Requests\SaveDiscoverySearchRequest;
use App\Http\Requests\SearchDiscoveryRequest;
use App\Models\Audit;
use App\Models\DiscoveredWebsite;
use App\Models\DiscoverySearch;
use App\Models\DiscoveryWatchlistItem;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\Response;

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
 * controller) / G1 (Watchlist page — watchlist()) / H1 (Bulk Audit —
 * bulkAudit(), reusing the existing single-audit pipeline in a loop,
 * not a new audit engine) / H2 (Export — export(), one row-mapping
 * pipeline shared across Excel/CSV/PDF/JSON; Excel/PDF are METHOD-
 * injected there, not constructor-injected here — see that method's
 * own docblock for the real production incident that reasoning fixes)
 * / I1 (Search Analytics mini-dashboard — analytics computed by
 * SearchAnalyticsService, not this controller) / J1 (discover() —
 * dispatches App\Discovery\Jobs\DiscoverWebsitesJob and redirects
 * immediately; see that job's own docblock for why an earlier
 * fastcgi_finish_request()-based approach was replaced with a real
 * queued job).
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
    /**
     * See bulkAudit()'s own docblock for why this is capped so low —
     * this app runs every audit's full pipeline synchronously, in the
     * same request, with no queue worker.
     */
    private const int MAX_BULK_AUDIT = 100;

    public function __construct(
        private readonly IndustryTaxonomyService $industryTaxonomy,
        private readonly GeoLookupServiceInterface $geoLookup,
        private readonly TechnologyFilterOptions $technologyFilterOptions,
        private readonly IssueFilterOptions $issueFilterOptions,
        private readonly WebsiteSearchService $websiteSearchService,
        private readonly SearchAnalyticsService $searchAnalyticsService,
    ) {}

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
     *
     * $analytics (Phase I1) is the "Search Analytics" mini-dashboard
     * data — see SearchAnalyticsService's own docblock for exactly how
     * each figure is computed and why three of its four are a capped
     * sample rather than exact for a very large result set.
     */
    public function index(Request $request): View
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($request->query());

        return view('discovery.index', [
            'websites' => $this->websiteSearchService->paginate($criteria),
            'filters' => $request->query(),
            'industries' => $this->industryTaxonomy->industries(),
            // Feature request — see WebsiteSearchService::countsByIndustry()/
            // countsByCountry()'s own docblocks: lets the Industry/Country
            // dropdowns show a real "(N)" count per option, and gray out
            // options with no matching data at all, rather than a person
            // discovering that only after selecting one and searching.
            'industryCounts' => $this->websiteSearchService->countsByIndustry(),
            'countries' => $this->geoLookup->countries(),
            'countryCounts' => $this->websiteSearchService->countsByCountry(),
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
            'analytics' => $this->searchAnalyticsService->analyze($criteria),
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

    /**
     * Backs the search panel's "Discover More" button (added alongside
     * "Save this search" — see search-panel.blade.php's own docblock
     * for both) — the first time this module ever actually goes and
     * finds NEW candidate websites rather than only searching whatever
     * discovered_websites already happens to contain. Every source
     * listed in config('discovery.sources') (today: YelpBusinessSource,
     * InternalCrawlSource) runs against the SAME filters just
     * submitted, via DiscoveryIngestionService (called from inside
     * App\Discovery\Jobs\DiscoverWebsitesJob, not this method directly
     * — see that job's own docblock for why) — see that service's own
     * docblock for exactly how a candidate becomes a real, deduplicated
     * DiscoveredWebsite row.
     *
     * Validated the same way search() itself is (SearchDiscoveryRequest)
     * before redirecting back to the results for those same filters —
     * so a "Discover More" click behaves like an ordinary search
     * submission from the user's point of view, just with an extra
     * "go look for brand new candidates first" step before the results
     * render.
     *
     * PRODUCTION INCIDENT HISTORY — read before changing this method:
     * this action originally ran discoverAndIngest() directly, in this
     * same request, and hit a real "504 Gateway Time-out" once two
     * external APIs' worth of search + detail calls exceeded the
     * gateway's own timeout. A first fix tried fastcgi_finish_request()
     * (the same technique AuditController::store() already uses
     * successfully for its own long-running work) to flush the
     * redirect early and keep working in the background — but the SAME
     * 504 recurred, because that function only helps when PHP is
     * actually running under the FPM SAPI and every layer between the
     * browser and PHP agrees "PHP says it's done" means "the request is
     * finished"; on this app's specific host (nginx in front of what
     * other evidence — an "x-lscache" response header seen during this
     * same incident — suggests is a LiteSpeed backend), that
     * assumption didn't hold.
     *
     * The actual fix: this method now does no external API work at
     * all — it only dispatches DiscoverWebsitesJob (queued, not run
     * inline) and redirects immediately. See that job's own docblock
     * for exactly how it gets processed without a persistent queue
     * worker (a scheduled `queue:work --stop-when-empty`, added to
     * routes/console.php, piggybacking on the SAME cron this module's
     * own Phase F4 scheduled-search feature already requires). The
     * trade-off communicated in the status message below: a "Discover
     * More" click waits for the next scheduler tick before the job
     * even starts (up to ~60s), then the same 10-30s of actual API
     * calls on top of that — noticeably slower than the
     * fastcgi_finish_request() attempt's near-instant background start
     * would have been if it had worked, but reliable regardless of
     * which SAPI or proxy layers this app happens to be deployed
     * behind.
     */
    public function discover(SearchDiscoveryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($validated);

        DiscoverWebsitesJob::dispatch($criteria, auth()->id());

        return redirect()
            ->route('discovery.index', $validated)
            ->with('status', 'Discovery has been queued — it can take a minute or two to '
                .'actually run (it waits for a scheduled background check, not this page '
                .'load). Refresh this page again in a couple of minutes to see any new '
                .'results.');
    }

    /**
     * Phase M1 (Full Audit Report tab) — looks up whether a COMPLETED
     * Audit already exists for this SAME website, using the exact same
     * DomainNormalizer::hash() matching
     * App\Audit\Jobs\AssembleAnalysisResultsJob::syncToDiscoveredWebsite()
     * already uses for the reverse direction (Audit → DiscoveredWebsite
     * sync) — so "the same website" means the same thing in both
     * places. When one exists, builds the exact same
     * $categories/$overallScore/$prospectQualification/$outreachDraft
     * shape AuditController::show() already builds for the single-
     * audit result page, and hands it to
     * audit/partials/full-report.blade.php — the SAME partial that
     * page itself now includes (see that partial's own docblock) —
     * via discovery/show.blade.php's own "Full Audit Report" tab. When
     * none exists, $fullAudit stays null and that tab shows an
     * "Audit Now" prompt instead.
     */
    public function show(
        DiscoveredWebsite $website,
        AuditCacheServiceInterface $cache,
        AnalysisResultsToDashboardCategories $dashboardCategoryMapper,
    ): View {
        // PRODUCTION INCIDENT — see
        // PRODUCTION INCIDENT (Website Discovery per-user privacy) —
        // see App\Discovery\Search\WebsiteSearchService::applyOwnershipVisibility()'s
        // own docblock for the full "why" this applies to EVERY row,
        // and for why an Admin's own bypass is "own or unowned",
        // never "any row regardless of owner" — this same check here
        // covers the direct /discovery/{uuid} link (a notification, a
        // bookmark) that the listing page's own filter alone
        // wouldn't.
        $user = auth()->user();
        $isOwner = $user !== null && $website->user_id === $user->id;
        $isAdminAndUnowned = $user !== null && $user->isAdmin() && $website->user_id === null;

        abort_unless($isOwner || $isAdminAndUnowned, 403);

        $normalizer = new DomainNormalizer();
        $targetHash = $normalizer->hash($website->url);

        // PRODUCTION INCIDENT — read before comparing against
        // Audit::$url_hash directly again: that column is NOT the same
        // hash DomainNormalizer::hash() produces. It's set by
        // App\Models\Audit::booted() as a plain md5($audit->url) — no
        // scheme/www/trailing-slash normalization at all — for a
        // completely different purpose (AuditRepository's own exact-
        // duplicate-request lookup optimization, see that column's own
        // docblock on the model). The EXACT SAME literal URL string
        // (confirmed directly against production data: DiscoveredWebsite
        // and a real completed Audit both storing the identical
        // "http://www.tavernacapranica.it") produced two DIFFERENT
        // hashes here, because DomainNormalizer::hash() normalizes
        // before hashing and Audit's own md5() doesn't — so a WHERE
        // clause comparing the two was guaranteed to never match,
        // regardless of how genuinely identical the underlying website
        // was. The one place this SAME comparison IS valid is
        // App\Audit\Jobs\AssembleAnalysisResultsJob::syncToDiscoveredWebsite(),
        // which correctly compares DomainNormalizer::hash($audit->url)
        // against DiscoveredWebsite::$url_hash — that column genuinely
        // IS populated via DomainNormalizer (see that model's own
        // booted() hook), unlike Audit's.
        //
        // The fix: narrow candidates via a cheap LIKE on the domain
        // first (bounds this to a handful of rows, no new index
        // needed), then compare DomainNormalizer::hash() computed
        // fresh on BOTH sides in PHP — never against Audit's own
        // url_hash column at all.
        $fullAudit = Audit::query()
            ->where('status', AuditStatus::COMPLETED->value)
            ->where('url', 'like', '%'.$website->domain.'%')
            ->orderByDesc('updated_at')
            ->get()
            ->first(static fn (Audit $audit): bool => $normalizer->hash($audit->url) === $targetHash);

        $fullReportData = null;

        if ($fullAudit !== null) {
            $results = $cache->getAnalysisResults($fullAudit->uuid);

            if ($results !== null) {
                $categories = $dashboardCategoryMapper->categories($results);

                $scoredCategories = array_filter($categories, static fn (array $category): bool => $category['score'] !== null);
                $overallScore = $scoredCategories === []
                    ? null
                    : (int) round(array_sum(array_column($scoredCategories, 'score')) / count($scoredCategories));

                $fullReportData = [
                    'audit' => $fullAudit,
                    'categories' => $categories,
                    'overallScore' => $overallScore,
                    'prospectQualification' => $results->prospectQualification,
                    'outreachDraft' => $results->outreachDraft,
                    'generatedAt' => $fullAudit->updated_at?->format('M j, Y \a\t g:i A') ?? 'just now',
                ];
            }
        }

        return view('discovery.show', [
            'website' => $website,
            // Phase N4 — scoped to auth()->id() specifically, not
            // ->watchlistItem()->exists() alone: see
            // App\Discovery\Search\WebsiteSearchService::query()'s own
            // identical constraint comment for why an unscoped check
            // would show this as "watched" even when a DIFFERENT user
            // was the one who watched it.
            'isWatched' => $website->watchlistItem()->where('user_id', auth()->id())->exists(),
            'fullAudit' => $fullAudit,
            'fullReportData' => $fullReportData,
        ]);
    }

    /**
     * Phase N4 — updateOrCreate()'s own unique key is now
     * (discovered_website_id, user_id), matching
     * discovery_watchlist's own new composite unique constraint (see
     * that migration's own docblock) — "watch" on a site THIS user
     * already watches is still a harmless no-op update (e.g. of
     * ->notes, if that's ever exposed on this form later), but a
     * DIFFERENT user watching the SAME site now correctly creates
     * their own separate row instead of colliding with the first
     * person's.
     */
    public function watch(DiscoveredWebsite $website): RedirectResponse
    {
        DiscoveryWatchlistItem::query()->updateOrCreate([
            'discovered_website_id' => $website->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Added to your watchlist.');
    }

    public function unwatch(DiscoveredWebsite $website): RedirectResponse
    {
        $website->watchlistItem()->where('user_id', auth()->id())->delete();

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Removed from your watchlist.');
    }

    /**
     * Backs the Watchlist page (Phase G1) — every DiscoveryWatchlistItem
     * belonging to the CURRENT user (Phase N4 — see
     * database/migrations/2026_08_19_000005_add_user_id_to_discovery_watchlist_table.php's
     * own docblock for why this scoping exists now at all), newest
     * first, eager-loading discoveredWebsite so
     * discovery/watchlist.blade.php can reuse result-card.blade.php per
     * item without an N+1 query (the same eager-loading reasoning
     * WebsiteSearchService::query() already applies for the same
     * relation on the main results grid).
     */
    /**
     * Backs the Watchlist page (Phase G1) — every DiscoveryWatchlistItem
     * belonging to the CURRENT user (Phase N4 — see
     * database/migrations/2026_08_19_000005_add_user_id_to_discovery_watchlist_table.php's
     * own docblock for why this scoping exists now at all), newest
     * first, eager-loading discoveredWebsite so
     * discovery/watchlist.blade.php can reuse result-card.blade.php per
     * item without an N+1 query (the same eager-loading reasoning
     * WebsiteSearchService::query() already applies for the same
     * relation on the main results grid).
     *
     * PRODUCTION GAP CLOSED, THEN NARROWED — see
     * App\Http\Controllers\DashboardController's own identical
     * comment: an Admin sees their OWN watchlist items plus genuinely
     * ownerless legacy rows (user_id = NULL, from before this column
     * existed) — never a DIFFERENT real user's own watchlist. An
     * earlier version of this fix widened Admin's scope to literally
     * every user's own data, which showed other people's private
     * watchlists to an Admin — narrower than that was the actual ask.
     */
    public function watchlist(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        return view('discovery.watchlist', [
            'items' => DiscoveryWatchlistItem::query()
                ->where(function ($query) use ($user, $isAdmin): void {
                    $query->where('user_id', $user->id);

                    if ($isAdmin) {
                        $query->orWhereNull('user_id');
                    }
                })
                ->with('discoveredWebsite')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Backs the Results section's "Bulk Audit Selected" floating bar
     * (Phase H1). Phase K3 rewrote this to go through the exact same
     * App\Audit\Services\BulkAuditBatchService the dedicated
     * /bulk-audits/create form (BulkAuditController) uses — one
     * App\Models\BulkAuditBatch, one Audit per selected website, every
     * one of them dispatched onto the 'audit-bulk' queue — rather than
     * running each selected website's audit synchronously, one after
     * another, in this same request, which is what this method used to
     * do (see self::MAX_BULK_AUDIT's own docblock for why that no
     * longer needs to bound this request's own worst-case wait time).
     *
     * Mode defaults to QUICK (see public/js/discovery-bulk-audit.js's
     * own floating-bar <select> for how a person can choose FULL
     * instead before submitting) — Discovery's own result cards are
     * primarily a lead-scanning workflow (see G3's own Opportunity
     * scoring), where a fast, homepage-only signal across many
     * candidates at once is usually more useful than a slow, deep
     * report on a handful.
     */
    public function bulkAudit(Request $request, BulkAuditBatchService $bulkAuditBatchService): RedirectResponse
    {
        $uuids = array_values(array_unique(array_filter(
            (array) $request->input('bulk_audit', []),
            static fn (mixed $value): bool => is_string($value) && $value !== '',
        )));

        if ($uuids === []) {
            return redirect()
                ->route('discovery.index')
                ->with('status', 'Select at least one website to audit.');
        }

        $uuids = array_slice($uuids, 0, self::MAX_BULK_AUDIT);

        $urls = DiscoveredWebsite::query()->whereIn('uuid', $uuids)->pluck('url')->all();

        $mode = AuditMode::tryFrom((string) $request->input('mode', '')) ?? AuditMode::QUICK;

        $batch = $bulkAuditBatchService->createBatch($urls, $mode);

        return redirect()
            ->route('bulk-audits.show', $batch)
            ->with('status', sprintf(
                '%d website(s) queued for %s.',
                $batch->total_count,
                $mode->label(),
            ));
    }

    /**
     * Backs the export links (Phase H2) — ?format=excel|csv|pdf|json,
     * defaulting to excel. Every format reads from the exact same
     * DiscoveredWebsitesToExportRows mapping (see that class's own
     * docblock for why Opportunity Score is live-computed rather than
     * read off the never-populated DiscoveredWebsite::$opportunity_score
     * column), so all four always show identical columns/values for the
     * same filtered result set — only the file format differs.
     *
     * Uses the SAME DiscoveryFilterCriteria the current List/Map View
     * results were built from (read straight off the query string, so
     * an export link built from the current page's own URL exports
     * exactly what's on screen), unpaginated (capped at 1000, the same
     * "cap rather than truly unbounded" reasoning mapData() already
     * applies for its own unpaginated fetch) rather than one page at a
     * time — exporting only the current page of 20 would defeat the
     * point of a bulk export.
     *
     * Excel/PDF are METHOD-injected here, not constructor-injected —
     * this was a real production bug, not a style choice: constructor
     * injection means Laravel's container must build EVERY dependency
     * on EVERY call to ANY method on this controller, so a
     * barryvdh/laravel-dompdf PDF instance failing to resolve on one
     * particular host (its own ServiceProvider throwing "Cannot resolve
     * public path", seen on a shared-hosting environment where
     * public_path() doesn't resolve the way that package expects) broke
     * the entire Discovery module — index(), watch(), search(), all of
     * it — not just this one export() action that actually needs PDF.
     * Method injection means only a request that actually reaches
     * export() ever asks the container to build these two.
     */
    public function export(Request $request, Excel $excel): Response|JsonResponse
    {
        $format = strtolower((string) $request->query('format', 'excel'));
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($request->query());

        $websites = $this->websiteSearchService->search($criteria, 1000);
        $rows = (new DiscoveredWebsitesToExportRows)->map($websites);

        return match ($format) {
            'json' => response()->json(['websites' => $rows]),
            'csv' => $excel->download(new DiscoveryResultsExport($rows), 'discovered-websites.csv'),
            'pdf' => app(PDF::class)
                ->loadView('discovery.pdf.export', ['rows' => $rows])
                ->setPaper('a4', 'landscape')
                ->download('discovered-websites.pdf'),
            default => $excel->download(new DiscoveryResultsExport($rows), 'discovered-websites.xlsx'),
        };
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
}