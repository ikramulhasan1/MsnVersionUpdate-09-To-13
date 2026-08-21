@extends('layouts.app')

@section('title', 'Technical SEO Audit — ' . $scan->domain)

@section('content')
    <section class="container py-4">
        <p class="text-secondary small mb-1">
            <a href="{{ route('technical-seo.index') }}">&larr; All Scans</a>
        </p>
        <h1 class="h4 fw-semibold mb-4">Technical SEO Audit — {{ $scan->domain }}</h1>

        @if (! $scan->status->isFinished())
            {{--
                Phase R2 — same "poll a lightweight status endpoint,
                reload when finished" principle this app's own existing
                Audit progress page already established (see
                public/js/technical-seo-progress.js's own docblock).
            --}}
            <div class="card text-center" id="tsa-progress-card" data-progress-url="{{ route('technical-seo.progress', $scan) }}">
                <div class="card-body py-5">
                    <div class="spinner-border text-secondary mb-3" role="status"></div>
                    <h2 class="h5" id="tsa-progress-label">{{ $scan->status->label() }}&hellip;</h2>
                    <p class="text-secondary small mb-0">This page will update automatically — no need to refresh.</p>
                </div>
            </div>

            @push('scripts')
                <script src="{{ asset('js/technical-seo-progress.js') }}"></script>
            @endpush
        @elseif ($scan->status->value === 'failed')
            <div class="alert alert-danger">
                This scan failed: {{ $scan->error_message ?? 'Unknown error.' }}
            </div>
        @else
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('technical-seo.export-csv', $scan) }}" class="btn btn-sm btn-outline-secondary">
                    Export Issues CSV
                </a>
            </div>

            {{-- Health Score --}}
            <div class="card mb-4">
                <div class="card-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle border border-3 mb-2"
                        style="width: 120px; height: 120px;">
                        <div>
                            <p class="h2 fw-bold mb-0">{{ $scan->health_score }}</p>
                            <p class="text-secondary small mb-0">/ 100</p>
                        </div>
                    </div>
                    <p class="h4 fw-semibold mb-0">Grade {{ $scan->health_grade }}</p>
                    <p class="text-secondary small mb-0">{{ $result->pagesCrawled }} pages crawled</p>
                </div>
            </div>

            {{-- Score Trend --}}
            @if ($trend->count() > 1)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-3">Score Trend</h2>
                        <canvas id="tsa-trend-chart" height="80"></canvas>
                    </div>
                </div>
            @endif

            <div class="row g-4 mb-4">
                {{-- Robots.txt / Sitemap --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Robots.txt &amp; Sitemap</h2>
                            <p class="small mb-1">
                                Robots.txt:
                                <span class="badge {{ $result->robotsTxt['exists'] ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $result->robotsTxt['exists'] ? 'Found' : 'Not found' }}
                                </span>
                                @if ($result->robotsTxt['blocks_critical_pages'])
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Blocks entire site</span>
                                @endif
                            </p>
                            <p class="small mb-0">
                                Sitemap:
                                <span class="badge {{ $result->sitemap['exists'] ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $result->sitemap['exists'] ? 'Found' : 'Not found' }}
                                </span>
                                @if ($result->sitemap['exists'])
                                    ({{ $result->sitemap['url_count'] }} URLs vs {{ $result->sitemap['crawled_page_count'] }} crawled)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Broken Links --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Broken Links ({{ $result->brokenLinks['count'] }})</h2>
                            @forelse (array_slice($result->brokenLinks['links'], 0, 5) as $link)
                                <p class="small mb-1 text-truncate">{{ $link['url'] }} ({{ $link['status_code'] ?? '—' }})</p>
                            @empty
                                <p class="text-secondary small mb-0">No broken links found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Redirects --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Redirects</h2>
                            <p class="small mb-1">{{ count($result->redirects['chains']) }} multi-hop chain(s)</p>
                            <p class="small mb-0 {{ count($result->redirects['loops']) > 0 ? 'text-danger' : '' }}">
                                {{ count($result->redirects['loops']) }} loop(s)
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Indexability --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Indexability</h2>
                            <p class="small mb-1">{{ count($result->indexability['noindex_pages']) }} noindex page(s)</p>
                            <p class="small mb-0">{{ count($result->indexability['canonical_mismatches']) }} canonical mismatch(es)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Core Web Vitals --}}
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-3">
                        Core Web Vitals
                        <span class="text-secondary small fw-normal">
                            (site average: {{ $result->coreWebVitals['average_score'] ?? '—' }}/100)
                        </span>
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr><th>Page</th><th>Score</th><th>LCP</th><th>CLS</th></tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($result->coreWebVitals['pages'], 0, 10) as $page)
                                    <tr>
                                        <td class="small text-truncate" style="max-width: 300px;">{{ $page['url'] }}</td>
                                        <td>{{ $page['score'] ?? '—' }}</td>
                                        <td class="small">{{ $page['lcp']['value'] ?? 'Unknown' }}</td>
                                        <td class="small">{{ $page['cls']['value'] ?? 'Unknown' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Mobile & Security --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Mobile &amp; Security</h2>
                            <p class="small mb-1">{{ count($result->mobileFriendliness['pages_missing_viewport']) }} page(s) missing viewport tag</p>
                            <p class="small mb-1">{{ count($result->security['mixed_content_pages']) }} page(s) with mixed content</p>
                            <p class="small mb-0">SSL expires: {{ $result->security['certificate_expires_at'] ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Crawl Depth --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Crawl Depth &amp; Structured Data</h2>
                            <p class="small mb-1">{{ count($result->crawlDepth['orphan_pages']) }} orphan page(s)</p>
                            <p class="small mb-0">
                                Schema types: {{ count($result->structuredData['type_counts']) > 0 ? implode(', ', array_keys($result->structuredData['type_counts'])) : 'None found' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI Priority Fix List --}}
            @if ($aiResult !== null && isset($aiResult->recommendations['issue_priority']))
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-3">Priority Fix List</h2>
                        @forelse ($aiResult->recommendations['issue_priority']['items'] as $item)
                            <div class="d-flex gap-3 border-bottom py-2">
                                <span class="badge {{ match($item['severity'] ?? 'notice') {
                                    'critical' => 'bg-danger-subtle text-danger-emphasis',
                                    'warning' => 'bg-warning-subtle text-warning-emphasis',
                                    default => 'bg-secondary-subtle text-secondary-emphasis',
                                } }}">
                                    {{ ucfirst($item['severity'] ?? 'notice') }}
                                </span>
                                <div>
                                    <p class="mb-0 small">{{ $item['message'] ?? '' }}</p>
                                    @if (! empty($item['recommendation']))
                                        <p class="mb-0 small text-secondary">{{ $item['recommendation'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0">No issues found.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if ($trend->count() > 1)
                @push('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
                    <script>
                        (function () {
                            const ctx = document.getElementById('tsa-trend-chart');
                            if (!ctx) return;

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: @json($trend->pluck('created_at')->map(fn ($d) => $d->format('M j'))),
                                    datasets: [{
                                        label: 'Health Score',
                                        data: @json($trend->pluck('health_score')),
                                        borderColor: '#9c7a3c',
                                        backgroundColor: 'rgba(156, 122, 60, 0.1)',
                                        fill: true,
                                        tension: 0.3,
                                    }],
                                },
                                options: {
                                    plugins: { legend: { display: false } },
                                    scales: { y: { beginAtZero: true, max: 100 } },
                                },
                            });
                        })();
                    </script>
                @endpush
            @endif
        @endif
    </section>
@endsection