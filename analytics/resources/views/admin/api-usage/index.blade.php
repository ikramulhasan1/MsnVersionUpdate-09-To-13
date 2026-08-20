@extends('layouts.app')

@section('title', 'API Usage — Admin')

@section('content')
    <section class="container py-4">
        <p class="text-secondary small mb-1">Admin</p>
        <h1 class="h4 fw-semibold mb-4">API Usage &amp; Cost</h1>

        @include('admin.partials.nav')

        <p class="text-secondary small mb-4">
            Figures below are ESTIMATES based on each provider's published pricing at request
            time — not a real invoice. Check your provider's own billing dashboard for actual
            charges.
        </p>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-1">Estimated Cost — Today</p>
                        <p class="h3 fw-bold mb-0">${{ number_format($todayTotal, 4) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-1">Estimated Cost — This Month</p>
                        <p class="h3 fw-bold mb-0">${{ number_format($monthTotal, 4) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3">This Month — By Provider</h2>

                @if ($byProvider->isEmpty())
                    <p class="text-secondary small mb-0">No API usage recorded yet this month.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Calls</th>
                                    <th>Keywords</th>
                                    <th>Estimated Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($byProvider as $providerName => $stats)
                                    <tr>
                                        <td>{{ $providerName }}</td>
                                        <td>{{ $stats['calls'] }}</td>
                                        <td>{{ $stats['keywords'] }}</td>
                                        <td>${{ number_format($stats['cost'], 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3">Recent Calls</h2>

                @if ($recentLogs->isEmpty())
                    <p class="text-secondary small mb-0">No API calls logged yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Provider</th>
                                    <th>Capability</th>
                                    <th>Keywords</th>
                                    <th>Est. Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentLogs as $log)
                                    <tr>
                                        <td class="small">{{ $log->created_at->diffForHumans() }}</td>
                                        <td class="small">{{ $log->provider?->name ?? 'Deleted provider' }}</td>
                                        <td class="small">{{ \App\Enums\KeywordCapability::from($log->capability)->label() }}</td>
                                        <td>{{ $log->keyword_count }}</td>
                                        <td>${{ number_format($log->estimated_cost_usd, 6) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection