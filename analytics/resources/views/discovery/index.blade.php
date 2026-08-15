{{--
    Website Discovery — Phase A4 (page shell) / B3 (search form UI).

    "Search panel" is now a real, working section — see
    discovery/partials/search-panel.blade.php — with Industry/Sub-Niche
    and Country/Region/City cascading dropdowns plus a UI-only Radius
    field. "Results" is still an empty placeholder for a later prompt
    to fill in, since actually applying $filters to a query is deferred
    (see App\Http\Controllers\DiscoveryController's own docblock).

    Expects:
      $websites         \Illuminate\Database\Eloquent\Collection<int, \App\Models\DiscoveredWebsite>
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
        </div>

        <section class="mb-4" id="discovery-search-panel">
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
            Results — the discovered-sites list/table UI itself still lands here in a later prompt, but
            $websites is now a real, filtered query result (Contact Availability — see
            App\Discovery\Search\WebsiteSearchService), not just an unfiltered listing.
        --}}
        <section id="discovery-results">
            <div class="discovery-placeholder">
                <p class="mb-0">Results — coming in a later prompt.</p>
            </div>
        </section>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/discovery-search-panel.js') }}"></script>
@endpush
