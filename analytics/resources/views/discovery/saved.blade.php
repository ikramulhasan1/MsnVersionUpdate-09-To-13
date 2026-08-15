{{--
    Website Discovery — Phase F3 (Saved Search) / F4 (Scheduled Search + New Website
    Detection).

    Every saved search (App\Models\DiscoverySearch, built in Phase A1/A2
    and finally wired into a real feature here), name + one-click
    "Run Search" link + "Delete". Running a saved search is nothing
    more than a link to discovery.index with that search's own stored
    $filters array as the query string — the exact same URL shape a
    manual search or a "Save this search" submission already produces,
    so a saved search re-runs identically to how it was originally run.

    Phase F4 adds an Enable/Disable Auto-Refresh toggle (sets
    is_scheduled — without it, no saved search could ever actually be
    scheduled) and, once App\Discovery\Jobs\RunScheduledDiscoverySearchJob
    has run at least once for a search, a "N New Websites Found" badge
    reading straight off that search's own new_results_count column —
    computed by that job, not recalculated here on page load.

    Expects:
      $searches   \Illuminate\Database\Eloquent\Collection<int, App\Models\DiscoverySearch>
--}}
@extends('layouts.app')

@section('title', 'Saved Searches')

@section('content')
    <section class="container dashboard-section">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-secondary small mb-1">Discover</p>
                <h1 class="h3 mb-0">Saved Searches</h1>
            </div>
            <a href="{{ route('discovery.index') }}" class="btn btn-outline-secondary btn-sm">
                Back to Search
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($searches->isEmpty())
            <div class="discovery-placeholder">
                <p class="mb-0">
                    No saved searches yet. Use "Save this search" on the search panel to create one.
                </p>
            </div>
        @else
            <div class="card">
                <ul class="list-group list-group-flush">
                    @foreach ($searches as $search)
                        <li class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <p class="fw-medium mb-0">{{ $search->name }}</p>
                                    @if ($search->is_scheduled)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            Auto-refresh on
                                        </span>
                                    @endif
                                    @if ($search->new_results_count !== null && $search->new_results_count > 0)
                                        <span class="badge bg-primary-subtle text-primary-emphasis">
                                            {{ $search->new_results_count }}
                                            {{ \Illuminate\Support\Str::plural('New Website', $search->new_results_count) }}
                                            Found
                                        </span>
                                    @endif
                                </div>
                                <p class="text-secondary small mb-0">
                                    Saved {{ $search->created_at?->diffForHumans() }}
                                    @if ($search->last_run_at)
                                        &middot; Last checked {{ $search->last_run_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('discovery.index', $search->filters) }}" class="btn btn-sm btn-primary">
                                    Run Search
                                </a>
                                <form method="POST" action="{{ route('discovery.searches.toggle-schedule', $search) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        {{ $search->is_scheduled ? 'Disable' : 'Enable' }} Auto-Refresh
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('discovery.searches.destroy', $search) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
@endsection
