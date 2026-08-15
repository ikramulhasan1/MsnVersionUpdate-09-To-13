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
    public/js/discovery-map.js's own docblock.

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
--}}
@extends('layouts.app')

@section('title', 'Website Discovery')

@section('content')
    <section class="container dashboard-section">
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
            Results (Phase D3/D4) — a responsive card grid (row-cols-1 row-cols-lg-3) with a
            "N Websites Found" summary + Sort By dropdown above it, plus Bootstrap-styled
            pagination links (Paginator::useBootstrapFive(), see AppServiceProvider::boot()).
            isWatched is read straight off the eager-loaded watchlistItem relation (see
            App\Discovery\Search\WebsiteSearchService::query()) rather than queried per card.
            The section label follows dashboard.blade.php's own .report-eyebrow convention
            (see resources/views/audit/partials/dashboard.blade.php's "01 / Overall Score" etc.)
            for a consistent numbered-section feel across the app; "02" continues straight on
            from the Search panel's own "01" above.
        --}}
        <section id="discovery-results" data-compare-url="{{ route('discovery.compare') }}">
            <span class="report-eyebrow">02 / Results</span>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <p class="mb-0 fw-medium">
                    {{ number_format($websites->total()) }}
                    {{ \Illuminate\Support\Str::plural('Website', $websites->total()) }} Found
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

                    <div class="d-flex align-items-center gap-2">
                        <label for="discovery-sort" class="form-label small fw-medium mb-0">Sort By</label>
                        <select class="form-select form-select-sm" id="discovery-sort" style="width: auto;">
                            @foreach ($sortOptions as $option)
                                <option value="{{ $option->value }}" @selected(($filters['sort'] ?? '') === $option->value)>
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
    <script src="{{ asset('js/discovery-search-panel.js') }}"></script>
    <script src="{{ asset('js/discovery-compare.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/discovery-map.js') }}"></script>
@endpush
