@extends('layouts.app')

@section('title', 'Backlink Analysis — Website Audit & Analysis Platform')

@section('content')
    <section class="container-fluid py-4">
        <h1 class="h4 fw-semibold mb-1">Backlink Analysis</h1>
        <p class="text-secondary mb-4">See who links to a domain, and how.</p>

        <form method="GET" action="{{ route('backlink-analysis.show') }}" class="card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-9">
                        <label for="domain" class="form-label">Domain or URL</label>
                        <input type="text" class="form-control" id="domain" name="domain"
                            value="{{ old('domain', $domain) }}" placeholder="e.g. example.com" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Analyze</button>
                    </div>
                </div>
            </div>
        </form>

        @if ($result !== null)
            @if ($result['all_failed'])
                <div class="alert alert-warning">
                    We couldn't load any data for "<strong>{{ $domain }}</strong>" — no API provider is
                    active for this yet.
                    @can('view-admin-panel')
                        <a href="{{ route('admin.api-providers.index') }}">Set one up in API Providers</a>.
                    @else
                        Please ask an Admin to configure one under API Providers.
                    @endcan
                </div>
            @else
                {{-- Summary metrics --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Total Backlinks</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['summary']['total_backlinks'] ?? null) !== null ? number_format($result['summary']['total_backlinks']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Referring Domains</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['summary']['referring_domains'] ?? null) !== null ? number_format($result['summary']['referring_domains']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Domain Rank</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['summary']['domain_rank'] ?? null) !== null ? $result['summary']['domain_rank'] : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Dofollow / Nofollow</p>
                                <p class="h4 fw-bold mb-0">
                                    @if (($result['summary']['dofollow_percent'] ?? null) !== null)
                                        {{ $result['summary']['dofollow_percent'] }}% / {{ round(100 - $result['summary']['dofollow_percent'], 1) }}%
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($result['summary'] === null)
                    <div class="alert alert-secondary small mb-4">Backlink summary is temporarily unavailable.</div>
                @endif

                <div class="row g-4 mb-4">
                    {{-- Referring Domains --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">Referring Domains</h2>
                                @if ($result['referring_domains'] !== null)
                                    @forelse ($result['referring_domains'] as $rd)
                                        <div class="d-flex justify-content-between border-bottom py-2 small">
                                            <span>{{ $rd['domain'] }}</span>
                                            <span class="text-secondary">
                                                {{ $rd['backlinks'] }} link(s)
                                                @if ($rd['domain_rank'] !== null)
                                                    &middot; Rank {{ $rd['domain_rank'] }}
                                                @endif
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-0">No referring domains found.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Anchor Text Distribution --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">Anchor Text Distribution</h2>
                                @if ($result['anchor_text'] !== null)
                                    @forelse ($result['anchor_text'] as $anchor)
                                        <div class="d-flex justify-content-between border-bottom py-2 small">
                                            <span>{{ $anchor['anchor_text'] !== '' ? $anchor['anchor_text'] : '(empty anchor)' }}</span>
                                            <span class="text-secondary">{{ $anchor['count'] }}</span>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-0">No anchor text data found.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Backlinks --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h2 class="h6 fw-semibold mb-0">Top Backlinks</h2>
                    @if ($result['backlinks'] !== null && count($result['backlinks']) > 0)
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="ba-export">
                            Export to CSV
                        </button>
                    @endif
                </div>

                <div class="card">
                    @if ($result['backlinks'] !== null)
                        @if (count($result['backlinks']) > 0)
                            <div id="ba-app" data-backlinks="{{ json_encode($result['backlinks']) }}" data-domain="{{ $domain }}">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead>
                                            <tr>
                                                <th>Source URL</th>
                                                <th>Anchor Text</th>
                                                <th>Type</th>
                                                <th>First Seen</th>
                                                <th>Source Rank</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ba-tbody"></tbody>
                                    </table>
                                </div>
                                <div class="card-body p-3 d-flex justify-content-center gap-2" id="ba-pagination"></div>
                            </div>
                        @else
                            <div class="card-body p-4 text-center text-secondary">No backlinks found.</div>
                        @endif
                    @else
                        <div class="card-body p-4 text-center text-secondary">Temporarily unavailable.</div>
                    @endif
                </div>
            @endif
        @endif
    </section>

    @if ($result !== null && $result['backlinks'] !== null && count($result['backlinks']) > 0)
        @push('scripts')
            <script>
                (function () {
                    const app = document.getElementById('ba-app');
                    const backlinks = JSON.parse(app.dataset.backlinks);
                    const domain = app.dataset.domain;
                    const PAGE_SIZE = 25;
                    let currentPage = 1;

                    function linkTypeBadge(type) {
                        const cls = type === 'dofollow' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis';
                        return '<span class="badge ' + cls + '">' + type + '</span>';
                    }

                    function render() {
                        const totalPages = Math.max(1, Math.ceil(backlinks.length / PAGE_SIZE));
                        currentPage = Math.min(currentPage, totalPages);
                        const pageItems = backlinks.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

                        const tbody = document.getElementById('ba-tbody');
                        tbody.innerHTML = pageItems.map(function (bl) {
                            return '<tr>' +
                                '<td class="text-truncate" style="max-width: 320px;"><a href="' + bl.source_url + '" target="_blank" rel="noopener">' + bl.source_url + '</a></td>' +
                                '<td>' + (bl.anchor_text || '<span class="text-secondary">—</span>') + '</td>' +
                                '<td>' + linkTypeBadge(bl.link_type) + '</td>' +
                                '<td>' + (bl.first_seen || '—') + '</td>' +
                                '<td>' + (bl.source_domain_rank !== null ? bl.source_domain_rank : '—') + '</td>' +
                                '</tr>';
                        }).join('');

                        renderPagination(totalPages);
                    }

                    function renderPagination(totalPages) {
                        const container = document.getElementById('ba-pagination');
                        let html = '';
                        for (let p = 1; p <= totalPages; p++) {
                            html += '<button type="button" class="btn btn-sm ' +
                                (p === currentPage ? 'btn-primary' : 'btn-outline-secondary') +
                                '" data-page="' + p + '">' + p + '</button>';
                        }
                        container.innerHTML = html;
                        container.querySelectorAll('button').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                currentPage = parseInt(btn.dataset.page, 10);
                                render();
                            });
                        });
                    }

                    const exportButton = document.getElementById('ba-export');
                    if (exportButton) {
                        exportButton.addEventListener('click', function () {
                            let csv = 'Source URL,Anchor Text,Link Type,First Seen,Source Domain Rank\n';
                            backlinks.forEach(function (bl) {
                                csv += '"' + bl.source_url.replace(/"/g, '""') + '",' +
                                    '"' + (bl.anchor_text || '').replace(/"/g, '""') + '",' +
                                    bl.link_type + ',' +
                                    (bl.first_seen || '') + ',' +
                                    (bl.source_domain_rank ?? '') + '\n';
                            });
                            const blob = new Blob([csv], { type: 'text/csv' });
                            const link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = 'backlinks-' + domain.replace(/\./g, '-') + '.csv';
                            link.click();
                        });
                    }

                    render();
                })();
            </script>
        @endpush
    @endif
@endsection