@extends('layouts.app')

@section('title', 'Competitor Analysis — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Competitor Analysis</h1>
        <p class="text-secondary mb-4">See a domain's traffic, competitors, and top-ranking content.</p>

        <form method="GET" action="{{ route('competitor-analysis.show') }}" class="card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain"
                            value="{{ old('domain', $domain) }}" placeholder="e.g. example.com" required>
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
                {{-- Domain Overview --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Organic Traffic (est.)</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['overview']['organic_traffic'] ?? null) !== null ? number_format($result['overview']['organic_traffic']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Organic Keywords</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['overview']['organic_keywords'] ?? null) !== null ? number_format($result['overview']['organic_keywords']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Domain Rank</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['overview']['domain_rank'] ?? null) !== null ? $result['overview']['domain_rank'] : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <div class="card-body p-3 text-center">
                                <p class="text-secondary small mb-1">Paid Keywords</p>
                                <p class="h4 fw-bold mb-0">
                                    {{ ($result['overview']['paid_keywords'] ?? null) !== null ? number_format($result['overview']['paid_keywords']) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($result['overview'] === null)
                    <div class="alert alert-secondary small mb-4">Domain overview is temporarily unavailable.</div>
                @endif

                <div class="row g-4 mb-4">
                    {{-- Top Organic Competitors --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">Top Organic Competitors</h2>
                                @if ($result['competitors'] !== null)
                                    @forelse ($result['competitors'] as $competitor)
                                        <div class="d-flex justify-content-between border-bottom py-2 small">
                                            <span>{{ $competitor['domain'] }}</span>
                                            <span class="text-secondary">
                                                {{ $competitor['common_keywords'] !== null ? number_format($competitor['common_keywords']) . ' common' : '—' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-0">No competitor data found.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Top Pages --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6 fw-semibold mb-3">Top Pages</h2>
                                @if ($result['top_pages'] !== null)
                                    @forelse ($result['top_pages'] as $page)
                                        <div class="d-flex justify-content-between border-bottom py-2 small">
                                            <a href="{{ $page['url'] }}" target="_blank" rel="noopener" class="text-truncate" style="max-width: 70%;">
                                                {{ $page['url'] }}
                                            </a>
                                            <span class="text-secondary">
                                                {{ $page['estimated_traffic'] !== null ? number_format($page['estimated_traffic']) : '—' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-0">No page data found.</p>
                                    @endforelse
                                @else
                                    <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Ranking Keywords --}}
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-3">Top Ranking Keywords</h2>
                        @if ($result['ranking_keywords'] !== null)
                            @if (count($result['ranking_keywords']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Keyword</th>
                                                <th>Position</th>
                                                <th>Volume</th>
                                                <th>URL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($result['ranking_keywords'] as $row)
                                                <tr>
                                                    <td>{{ $row['keyword'] }}</td>
                                                    <td>{{ $row['position'] ?? '—' }}</td>
                                                    <td>{{ $row['volume'] !== null ? number_format($row['volume']) : '—' }}</td>
                                                    <td class="small text-truncate" style="max-width: 300px;">
                                                        <a href="{{ $row['url'] }}" target="_blank" rel="noopener">{{ $row['url'] }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-secondary small mb-0">No ranking keyword data found.</p>
                            @endif
                        @else
                            <p class="text-secondary small mb-0">Temporarily unavailable.</p>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </section>
@endsection