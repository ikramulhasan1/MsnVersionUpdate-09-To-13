{{--
    Website Discovery — Phase E2 (comparison feature).

    A metric-by-website table for the 2-5 sites selected via each
    result-card.blade.php's "Compare" checkbox (public/js/discovery-compare.js)
    — one row per metric (SEO/Performance/Security/Accessibility/Mobile),
    one column per selected site, with the best (highest) score in each
    row highlighted via .compare-best. Mobile is included even though no
    current job populates discovered_websites.mobile_score (see
    App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own docblock) — its
    row just shows "—" for every site until that changes, honestly
    rather than hiding the row and silently deviating from the metric
    list this phase asked for.

    Expects:
      $websites   \Illuminate\Support\Collection<int, App\Models\DiscoveredWebsite> — 2 to 5,
                  in the order they were selected (see
                  App\Http\Controllers\DiscoveryController::compare())
--}}
@extends('layouts.app')

@section('title', 'Compare Websites')

@php
    $metrics = [
        'seo_score' => 'SEO',
        'performance_score' => 'Performance',
        'security_score' => 'Security',
        'accessibility_score' => 'Accessibility',
        'mobile_score' => 'Mobile',
    ];
@endphp

@section('content')
    <section class="container dashboard-section">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-secondary small mb-1">Discover</p>
                <h1 class="h3 mb-0">Compare Websites</h1>
            </div>
            <a href="{{ route('discovery.index') }}" class="btn btn-outline-secondary btn-sm">
                Back to Results
            </a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-secondary small text-uppercase">Metric</th>
                            @foreach ($websites as $website)
                                <th scope="col">
                                    <a href="{{ route('discovery.show', $website) }}" class="text-decoration-none">
                                        {{ $website->business_name ?? $website->domain }}
                                    </a>
                                    <p class="text-secondary small mb-0 fw-normal">{{ $website->domain }}</p>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($metrics as $column => $label)
                            @php
                                $bestScore = $websites->pluck($column)->filter(fn($score) => $score !== null)->max();
                            @endphp
                            <tr>
                                <th scope="row" class="text-secondary small fw-medium">{{ $label }}</th>
                                @foreach ($websites as $website)
                                    @php $score = $website->{$column}; @endphp
                                    <td class="{{ $score !== null && $score === $bestScore ? 'compare-best' : '' }}">
                                        {{ $score ?? '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
