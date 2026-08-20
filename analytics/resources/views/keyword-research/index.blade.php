@extends('layouts.app')

@section('title', 'Keyword Research — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Keyword Research</h1>
        <p class="text-secondary mb-4">Look up volume, difficulty, intent, and SERP data for one keyword.</p>

        <form method="GET" action="{{ route('keyword-research.show') }}" class="card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" class="form-control" id="keyword" name="keyword"
                            value="{{ old('keyword', $keyword) }}" placeholder="e.g. project management software"
                            required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country">
                            @foreach (['United States', 'United Kingdom', 'Bangladesh', 'India', 'Canada', 'Australia'] as $option)
                                <option value="{{ $option }}" @selected(old('country', $country ?? 'United States') === $option)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="language" class="form-label">Language</label>
                        <select class="form-select" id="language" name="language">
                            @foreach (['English', 'Bengali'] as $option)
                                <option value="{{ $option }}" @selected(old('language', $language ?? 'English') === $option)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Research</button>
                    </div>
                </div>
            </div>
        </form>

        @if ($result !== null)
            @if ($result['all_failed'])
                <div class="alert alert-warning">
                    We couldn't load any data for "<strong>{{ $keyword }}</strong>" — no API provider is
                    active for this yet.
                    @can('view-admin-panel')
                        <a href="{{ route('admin.api-providers.index') }}">Set one up in API Providers</a>.
                    @else
                        Please ask an Admin to configure one under API Providers.
                    @endcan
                </div>
            @else
                {{-- Core metrics --}}
                <div class="d-flex justify-content-end mb-2">
                    {{--
                        Phase O5 — calls the SAME shared modal/JS both
                        this page and Keyword Magic Tool use (see
                        public/js/keyword-lists.js's own docblock).
                    --}}
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="KeywordLists.open({
                            keyword: @json($keyword),
                            volume: @json($result['volume']),
                            difficulty: @json($result['difficulty']),
                            cpc: @json($result['cpc']),
                        })">
                        + Add to List
                    </button>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Search Volume</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ $result['volume'] !== null ? number_format($result['volume']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Keyword Difficulty</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ $result['difficulty'] !== null ? $result['difficulty'] . '%' : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">CPC</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ $result['cpc'] !== null ? '$' . number_format($result['cpc'], 2) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Competitive Density</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ $result['competitive_density'] !== null ? $result['competitive_density'] . '/100' : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Intent --}}
                @if ($result['intent'] !== null)
                    <p class="mb-4">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis fs-6">
                            {{ ucfirst($result['intent']) }} Intent
                        </span>
                    </p>
                @endif

                {{-- Trend chart --}}
                @if ($result['trend'] !== null && count($result['trend']) > 0)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">12-Month Search Volume Trend</h2>
                            <canvas id="trendChart" height="80"></canvas>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary small mb-4">
                        Search volume trend is temporarily unavailable for this keyword.
                    </div>
                @endif

                <div class="row g-4 mb-4">
                    {{-- SERP features --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">SERP Features</h2>
                                @if ($result['serp_features'] !== null)
                                    @forelse ($result['serp_features'] as $feature)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis me-1 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $feature)) }}
                                        </span>
                                    @empty
                                        <p class="text-secondary small mb-0">No special SERP features detected.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- People Also Ask --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">People Also Ask</h2>
                                @if ($result['questions'] !== null)
                                    @forelse ($result['questions'] as $question)
                                        <p class="small mb-2">{{ $question }}</p>
                                    @empty
                                        <p class="text-secondary small mb-0">No related questions found.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top ranking pages --}}
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-3">Top 10 Ranking Pages</h2>
                        @if ($result['top_results'] !== null)
                            @forelse ($result['top_results'] as $index => $page)
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <span class="text-secondary small">{{ $index + 1 }}.</span>
                                    <div>
                                        <a href="{{ $page['url'] }}" target="_blank" rel="noopener" class="small">
                                            {{ $page['title'] ?? $page['domain'] }}
                                        </a>
                                        <p class="text-secondary small mb-0">{{ $page['domain'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">No ranking data found.</p>
                            @endforelse
                        @else
                            <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                        @endif
                    </div>
                </div>

                {{-- Related keyword suggestions --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-semibold mb-0">Related Keywords</h2>
                            {{--
                                PRODUCTION BUG AVOIDED — Keyword Magic
                                Tool (Phase O4) doesn't exist as a route
                                yet; calling route('keyword-magic-tool.index')
                                unconditionally would throw a real
                                RouteNotFoundException and crash this
                                ENTIRE page the moment this section
                                rendered, not just show a broken link.
                                Route::has() guards against that —
                                remove this @if once Phase O4 actually
                                registers that route name.
                            --}}
                            @if (\Illuminate\Support\Facades\Route::has('keyword-magic-tool.show'))
                                <a href="{{ route('keyword-magic-tool.show', ['seed' => $keyword, 'country' => $country, 'language' => $language]) }}" class="small">
                                    See more in Keyword Magic Tool &rarr;
                                </a>
                            @endif
                        </div>
                        @if ($result['related_keywords'] !== null)
                            @forelse ($result['related_keywords'] as $suggestion)
                                <div class="d-flex justify-content-between border-bottom py-2 small">
                                    <span>{{ $suggestion['keyword'] }}</span>
                                    <span class="text-secondary">
                                        {{ $suggestion['volume'] !== null ? number_format($suggestion['volume']) : '—' }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">No related keywords found.</p>
                            @endforelse
                        @else
                            <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </section>

    @if ($result !== null && ($result['trend'] ?? null) !== null && count($result['trend']) > 0)
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
            <script>
                (function () {
                    const ctx = document.getElementById('trendChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json(array_column($result['trend'], 'month')),
                            datasets: [{
                                label: 'Search Volume',
                                data: @json(array_column($result['trend'], 'volume')),
                                borderColor: '#9c7a3c',
                                backgroundColor: 'rgba(156, 122, 60, 0.1)',
                                fill: true,
                                tension: 0.3,
                            }],
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } },
                        },
                    });
                })();
            </script>
        @endpush
    @endif

    {{-- Phase O5 (Keyword List/Project Management) — the shared "Add
         to List" modal + JS this page's own button above uses. --}}
    @include('keyword-lists._add-to-list-modal')
    @push('scripts')
        <script src="{{ asset('js/keyword-lists.js') }}"></script>
    @endpush
@endsection