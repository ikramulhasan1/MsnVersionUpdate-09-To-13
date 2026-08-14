{{--
    Website Discovery — Phase A4 (page shell).

    Deliberately just two empty section placeholders — "Search panel"
    and "Results" — for later prompts to fill in incrementally (the
    Industry/Niche + advanced filter form in one, the results
    list/table in another), rather than the working-but-unfiltered
    form+list Phase A3 originally built here. $websites/$filters are
    still passed in by App\Http\Controllers\DiscoveryController
    unchanged, ready for the "Results" section to start using as soon
    as a later prompt replaces its placeholder with real markup — no
    controller change needed when that happens.

    Expects:
      $websites   \Illuminate\Database\Eloquent\Collection<int, \App\Models\DiscoveredWebsite>
      $filters    array<string, mixed> — whatever query params search() redirected back with
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

        {{-- Search panel — Industry/Niche + advanced filters land here in a later prompt. --}}
        <section class="mb-4" id="discovery-search-panel">
            <div class="discovery-placeholder">
                <p class="mb-0">Search panel — coming in a later prompt.</p>
            </div>
        </section>

        {{-- Results — the discovered-sites list/table lands here in a later prompt. --}}
        <section id="discovery-results">
            <div class="discovery-placeholder">
                <p class="mb-0">Results — coming in a later prompt.</p>
            </div>
        </section>
    </section>
@endsection
