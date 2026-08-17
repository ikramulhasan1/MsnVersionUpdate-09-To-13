{{--
    Website Discovery — Phase A4 (page shell) / B3 (search form UI).

    "Search panel" is a real, working section — see
    discovery/partials/search-panel.blade.php — and most of its filters
    (see DiscoveryFilterCriteria's own docblock for the exact list) now
    really do narrow $websites via WebsiteSearchService (Phase D1/D2).
    "Results" (Phase D3/D4) renders result-card.blade.php in a
    responsive grid (row-cols-1 row-cols-lg-3), with a "N Websites
    Found" summary and a genuinely-wired Sort By dropdown above it, plus
    Bootstrap-styled pagination links. Each card's "Compare" checkbox
    (Phase E2) is wired up by public/js/discovery-compare.js — see that
    file's own docblock. A "List View | Map View" button group (Phase
    E3) toggles between this grid and a Leaflet map plotting every
    filtered result with a latitude/longitude — see
    public/js/discovery-map.js's own docblock. An Export dropdown
    (Phase H2) links to DiscoveryController::export() with the current
    filters plus &format=excel|csv|json, so an export always matches
    whatever's currently on screen. A "Search Analytics" mini-dashboard
    (Phase I1) sits between Search and Results — see
    App\Discovery\Analytics\SearchAnalyticsService's own docblock. Each
    card's separate "Audit" checkbox (Phase H1, extended in Phase K3)
    is wired up by public/js/discovery-bulk-audit.js, submitting the
    hidden #discovery-bulk-audit-form to
    DiscoveryController::bulkAudit() — which now dispatches through
    App\Audit\Services\BulkAuditBatchService (a real, dedicated-queue
    batch, capped at 100 selections) rather than running every selected
    website's audit synchronously in this same request — see that
    controller method's own docblock.

    Expects:
      $websites         \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\DiscoveredWebsite> —
                        from WebsiteSearchService::paginate() (Phase D2)
      $filters          array<string, mixed> — whatever query params search() redirected back with
      $industries       array<int, string> — from IndustryTaxonomyService::industries()
      $countries        array<int, array{code: string, name: string}> — from GeoLookupServiceInterface::countries()
      $websiteStatuses  array<int, App\Discovery\Enums\WebsiteConnectivityStatus> — from ::cases() (Phase C1)
      $websiteTypes     array<int, App\Discovery\Enums\WebsiteType> — from ::cases() (Phase C1)
      $technologyGroups array<string, array<int, array{slug: string, name: string}>> — from TechnologyFilterOptions::all() (Phase C2)
      $serverSoftware   array<int, App\Discovery\Enums\ServerSoftware> — from ::cases() (Phase C2)
      $seoIssues        array<int, array{code: string, label: string}> — from IssueFilterOptions::seoIssues() (Phase C3)
      $securityIssues   array<int, array{code: string, label: string}> — from IssueFilterOptions::securityIssues() (Phase C3)
      $opportunityFilters array<int, App\Discovery\Enums\OpportunityFilter> — from ::cases() (Phase C4)
      $businessSizes    array<int, App\Discovery\Enums\BusinessSize> — from ::cases() (Phase C5)
      $lastUpdatedRanges array<int, App\Discovery\Enums\LastUpdatedRange> — from ::cases() (Phase C5)
      $trafficRanges    array<int, App\Discovery\Enums\TrafficRange> — from ::cases() (Phase C5)
      $socialPlatforms  array<int, App\Discovery\Enums\SocialPlatform> — from ::cases() (Phase C5)
      $contactAvailabilityOptions array<int, App\Discovery\Enums\ContactAvailability> — from ::cases() (Phase C6)
      $sortOptions      array<int, App\Discovery\Enums\DiscoverySortOption> — from ::cases() (Phase D4)
      $analytics        App\Discovery\Analytics\DTO\DiscoverySearchAnalytics — from
                        SearchAnalyticsService::analyze() (Phase I1)
--}}
@extends('layouts.app')

@section('title', 'Website Discovery')

@section('content')
    <section class="container dashboard-section">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-secondary small mb-1">Discover</p>
                <h1 class="h3 mb-0">Website Discovery</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('discovery.watchlist') }}" class="btn btn-outline-secondary btn-sm">
                    Watchlist
                </a>
                <a href="{{ route('discovery.searches.index') }}" class="btn btn-outline-secondary btn-sm">
                    Saved Searches
                </a>
            </div>
        </div>

        {{-- Hidden, server-rendered form (Phase H1) — carries a real @csrf token;
             public/js/discovery-bulk-audit.js populates it with bulk_audit[] hidden
             inputs from the current selection and submits it, rather than building
             a form (and a CSRF token) from scratch in JS. --}}
        <form method="POST" action="{{ route('discovery.bulk-audit') }}" id="discovery-bulk-audit-form"
            class="d-none">
            @csrf
        </form>
        <section class="mb-4" id="discovery-search-panel">
            <span class="report-eyebrow">01 / Search</span>
            @include('discovery.partials.search-panel', [
                'industries' => $industries,
                'countries' => $countries,
                'filters' => $filters,
                'websiteStatuses' => $websiteStatuses,
                'websiteTypes' => $websiteTypes,
                'technologyGroups' => $technologyGroups,
                'serverSoftware' => $serverSoftware,
                'seoIssues' => $seoIssues,
                'securityIssues' => $securityIssues,
                'opportunityFilters' => $opportunityFilters,
                'businessSizes' => $businessSizes,
                'lastUpdatedRanges' => $lastUpdatedRanges,
                'trafficRanges' => $trafficRanges,
                'socialPlatforms' => $socialPlatforms,
                'contactAvailabilityOptions' => $contactAvailabilityOptions,
            ])
        </section>

        {{--
            Search Analytics mini-dashboard (Phase I1) — three stat cards plus a Technology
            (CMS) breakdown pie chart (Chart.js, public/js/dashboard-charts.js's own
            initDiscoveryTechnologyChart() — extended, not duplicated, from that file's
            existing audit-dashboard chart initializers), following .chart-card's own
            styling from resources/views/audit/partials/dashboard-components.blade.php.
            See App\Discovery\Analytics\SearchAnalyticsService's own docblock for exactly
            how each figure is computed, and why three of the four are a capped sample
            rather than exact for a very large result set.
        --}}
        <section class="mb-4" id="discovery-analytics">
            <span class="report-eyebrow">02 / Search Analytics</span>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body p-4 text-center">
                            <p class="text-secondary small mb-1">Total Websites Found</p>
                            <p class="h3 mb-0">{{ number_format($analytics->totalCount) }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body p-4 text-center">
                            <p class="text-secondary small mb-1">Poor SEO (score &lt; 50)</p>
                            <p class="h3 mb-0" style="color: var(--audit-danger);">
                                {{ number_format($analytics->poorSeoCount) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body p-4 text-center">
                            <p class="text-secondary small mb-1">High Opportunity</p>
                            <p class="h3 mb-0" style="color: var(--audit-primary);">
                                {{ number_format($analytics->highOpportunityCount) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($analytics->technologyBreakdown !== [])
                <div class="card chart-card">
                    <div class="card-body p-4">
                        <h2 class="h6 mb-3">Technology Breakdown (CMS)</h2>
                        <div class="chart-wrap">
                            <canvas id="discoveryTechnologyChart"
                                data-labels="{{ json_encode(array_keys($analytics->technologyBreakdown)) }}"
                                data-counts="{{ json_encode(array_values($analytics->technologyBreakdown)) }}"
                                aria-label="Pie chart of detected CMS across the current search results"
                                role="img"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            @if ($analytics->isSampled())
                <p class="text-secondary small mt-2 mb-0">
                    Technology breakdown and the Poor SEO/High Opportunity counts above are based on a sample
                    of the first {{ number_format($analytics->sampledCount) }} matching results (out of
                    {{ number_format($analytics->totalCount) }} total).
                </p>
            @endif
        </section>

        {{--
            Results (Phase D3/D4) — a responsive card grid (row-cols-1 row-cols-lg-3) with a
            "N Websites Found" summary + Sort By dropdown above it, plus Bootstrap-styled
            pagination links (Paginator::useBootstrapFive(), see AppServiceProvider::boot()).
            isWatched is read straight off the eager-loaded watchlistItem relation (see
            App\Discovery\Search\WebsiteSearchService::query()) rather than queried per card.
            The section label follows dashboard.blade.php's own .report-eyebrow convention
            (see resources/views/audit/partials/dashboard.blade.php's "01 / Overall Score" etc.)
            for a consistent numbered-section feel across the app; "03" continues straight on
            from Search ("01") and Search Analytics ("02") above.
        --}}
        <section id="discovery-results" data-compare-url="{{ route('discovery.compare') }}">
            <span class="report-eyebrow">03 / Results</span>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <p class="mb-0 fw-medium">
                    {{ number_format($websites->total()) }} {{ \Illuminate\Support\Str::plural('Website', $websites->total()) }} Found
                </p>

                <div class="d-flex align-items-center gap-3">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Results view">
                        <button type="button" class="btn btn-outline-secondary active" id="discovery-view-list">
                            List View
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="discovery-view-map">
                            Map View
                        </button>
                    </div>

                    {{-- Each link carries the current filters (Phase H2) — built from the
                         same $filters array already used to repopulate the search panel and
                         Sort By dropdown, so an export always matches what's on screen.

                         PDF is deliberately NOT listed here — a real, unresolved production
                         issue on this app's specific host: barryvdh/laravel-dompdf's own
                         ServiceProvider throws "Cannot resolve public path" when actually
                         building a PDF (DiscoveryController::export()'s own docblock has the
                         full incident history). Excel/CSV/JSON never touch that package at
                         all, so all three work regardless; PDF specifically doesn't yet.
                         The route (discovery.export?format=pdf) and the controller's own
                         'pdf' match arm are both left in place — re-adding this link is a
                         one-line change once the underlying public_path() issue is fixed at
                         the hosting/environment level, not a code change. --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('discovery.export', array_merge($filters, ['format' => 'excel'])) }}">
                                    Excel (.xlsx)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('discovery.export', array_merge($filters, ['format' => 'csv'])) }}">
                                    CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('discovery.export', array_merge($filters, ['format' => 'json'])) }}">
                                    JSON
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label for="discovery-sort" class="form-label small fw-medium mb-0">Sort By</label>
                        <select class="form-select form-select-sm" id="discovery-sort" style="width: auto;">
                            @foreach ($sortOptions as $option)
                                <option value="{{ $option->value }}"
                                    @selected(($filters['sort'] ?? '') === $option->value)>
                                    {{ $option->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div id="discovery-results-list">
                @if ($websites->isEmpty())
                    <div class="discovery-placeholder">
                        <p class="mb-0">
                            {{ $filters === [] ? 'No discovered websites yet.' : 'No discovered websites match your filters.' }}
                        </p>
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-lg-3 g-3">
                        @foreach ($websites as $website)
                            <div class="col">
                                @include('discovery.partials.result-card', [
                                    'website' => $website,
                                    'isWatched' => $website->watchlistItem !== null,
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $websites->links() }}
                    </div>
                @endif
            </div>

            {{--
                Map View (Phase E3) — hidden until toggled; see public/js/discovery-map.js's own
                docblock for why its markers come from a separate, unpaginated mapData() fetch
                rather than this page's own paginated $websites.
            --}}
            <div id="discovery-map-section" class="d-none" data-map-data-url="{{ route('discovery.map-data') }}">
                <div id="discovery-map" style="height: 500px; border-radius: 0.85rem; overflow: hidden;"></div>
            </div>
        </section>
    </section>
@endsection

@push('styles')
    {{-- Leaflet (Phase E3) — this app has no JS bundler, so a CDN <link>/<script>
         pair, scoped to just this page via @push, matches how Bootstrap/Chart.js
         are already loaded elsewhere in this app rather than site-wide bloat. --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    {{--
        Cache-busted with ?v={filemtime}, the same pattern layouts/app.blade.php's own
        app.css link already uses — plain asset() alone (as these five previously were)
        let LiteSpeed Cache/the browser keep serving an old cached copy of a JS file
        after it changed on disk, which is exactly what silently broke the Sub-Niche/
        Region/City cascade and "Save this search" in production once before: the fix
        was deployed, but nothing ever told the cache the file was different now.
    --}}
    <script src="{{ asset('js/discovery-search-panel.js') }}?v={{ file_exists(public_path('js/discovery-search-panel.js')) ? filemtime(public_path('js/discovery-search-panel.js')) : time() }}"></script>
    <script src="{{ asset('js/discovery-compare.js') }}?v={{ file_exists(public_path('js/discovery-compare.js')) ? filemtime(public_path('js/discovery-compare.js')) : time() }}"></script>
    <script src="{{ asset('js/discovery-bulk-audit.js') }}?v={{ file_exists(public_path('js/discovery-bulk-audit.js')) ? filemtime(public_path('js/discovery-bulk-audit.js')) : time() }}"></script>
    {{-- Chart.js + dashboard-charts.js (Phase I1) — the exact same CDN version and
         local file the audit result page already loads for its own charts; see that
         file's own docblock for how it now serves both pages via per-chart canvas
         guards. --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/dashboard-charts.js') }}?v={{ file_exists(public_path('js/dashboard-charts.js')) ? filemtime(public_path('js/dashboard-charts.js')) : time() }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/discovery-map.js') }}?v={{ file_exists(public_path('js/discovery-map.js')) ? filemtime(public_path('js/discovery-map.js')) : time() }}"></script>
@endpush