{{--
    Website Discovery — Phase G1 (Watchlist).

    Every DiscoveryWatchlistItem (App\Models\DiscoveryWatchlistItem —
    built in Phase A1/A2, finally wired into a real page here), each
    rendered via result-card.blade.php with isWatched hardcoded true
    (every site on this page is, by definition, on the watchlist) —
    reusing that existing card rather than building a separate
    watchlist-specific one gets the same score mini-rings, technology
    pills, Opportunity indicator, and action buttons (View Website,
    Audit Website, View Details, the ⭐ Save/Unwatch toggle, Compare)
    "each with its latest score" without any duplicated markup: every
    score shown is already discovered_websites' own current column
    value — there is no separate historical/versioned score anywhere
    in this schema, so "latest" and "current" are the same thing here.

    Expects:
      $items   \Illuminate\Database\Eloquent\Collection<int, App\Models\DiscoveryWatchlistItem>
               — each with its discoveredWebsite relation eager-loaded
--}}
@extends('layouts.app')

@section('title', 'Watchlist')

@section('content')
    <section class="container dashboard-section">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-secondary small mb-1">Discover</p>
                <h1 class="h3 mb-0">Watchlist</h1>
            </div>
            <a href="{{ route('discovery.index') }}" class="btn btn-outline-secondary btn-sm">
                Back to Search
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="discovery-placeholder">
                <p class="mb-0">
                    No saved websites yet. Use the ⭐ Save button on a result card or a website's detail page to
                    add one.
                </p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-lg-3 g-3">
                @foreach ($items as $item)
                    @continue (! $item->discoveredWebsite)
                    <div class="col">
                        @include('discovery.partials.result-card', [
                            'website' => $item->discoveredWebsite,
                            'isWatched' => true,
                        ])
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
