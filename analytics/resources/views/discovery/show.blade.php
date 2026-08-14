{{--
    Website Discovery — Phase A3 (routes + navbar).

    A minimal detail page: identity, the Watch/Unwatch toggle (fully
    working — see App\Http\Controllers\DiscoveryController::watch()/
    unwatch()), and a placeholder for the technology/scoring/contact
    detail and audit/export/lead-scoring actions a later phase will add.

    Expects:
      $website     App\Models\DiscoveredWebsite
      $isWatched   bool
--}}
@extends('layouts.app')

@section('title', $website->domain)

@section('content')
    <section class="result-header">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <p class="text-secondary small mb-1">Discovered Website</p>
                    <h1 class="h3 mb-0">{{ $website->domain }}</h1>
                    <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none">
                        {{ $website->url }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if ($isWatched)
                        <form method="POST" action="{{ route('discovery.unwatch', $website) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                Remove from Watchlist
                            </button>
                        </form>
                    @else
                        <a href="{{ route('discovery.watch', $website) }}" class="btn btn-outline-secondary btn-sm">
                            Add to Watchlist
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container dashboard-section">
        <div class="card">
            <div class="card-body p-4">
                <p class="text-secondary mb-0">
                    Technology stack, scores, contact info, and audit/export/lead-scoring actions for this site
                    are coming in a later phase of this module.
                </p>
            </div>
        </div>
    </section>
@endsection
