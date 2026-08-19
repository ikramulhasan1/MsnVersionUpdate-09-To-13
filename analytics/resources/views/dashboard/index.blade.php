@extends('layouts.app')

@section('title', 'Dashboard — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-secondary mb-4">Here's what's happening with your account.</p>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-1">Total Audits Run</p>
                        <p class="h3 fw-bold mb-0">{{ $auditCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-1">Websites Watched</p>
                        <p class="h3 fw-bold mb-0">{{ $watchlistCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-1">Unread Notifications</p>
                        <p class="h3 fw-bold mb-0">{{ $unreadNotificationCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-semibold mb-0">Recent Audits</h2>
                            @if ($auditCount > 0)
                                <span class="text-secondary small">{{ $auditCount }} total</span>
                            @endif
                        </div>

                        @forelse ($recentAudits as $audit)
                            <a href="{{ route('audits.show', $audit) }}"
                                class="d-flex align-items-center justify-content-between py-2 text-decoration-none border-bottom">
                                <span class="text-truncate me-2">{{ $audit->url }}</span>
                                <span class="badge {{ audit_status_badge_class($audit->status) }} flex-shrink-0">
                                    {{ audit_status_label($audit->status) }}
                                </span>
                            </a>
                        @empty
                            <p class="text-secondary small mb-0">You haven't run any audits yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Phase N5 (Dynamic Pricing/Subscription) placeholder — see
                     App\Http\Controllers\DashboardController::index()'s own
                     docblock on $subscription for exactly what changes when
                     that phase lands. --}}
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-2">Subscription</h2>
                        <p class="mb-1">
                            <span class="fw-medium">{{ $subscription['plan'] }}</span>
                        </p>
                        <p class="text-secondary small mb-0">
                            Subscription plans are coming soon — every feature is currently available to
                            every account at no charge.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card mb-3">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-3">Quick Actions</h2>
                        <div class="d-grid gap-2">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm">Run a New Audit</a>
                            <a href="{{ route('discovery.index') }}" class="btn btn-outline-secondary btn-sm">
                                Browse Website Discovery
                            </a>
                            <a href="{{ route('bulk-audits.create') }}" class="btn btn-outline-secondary btn-sm">
                                Start a Bulk Audit
                            </a>
                            <a href="{{ route('discovery.watchlist') }}" class="btn btn-outline-secondary btn-sm">
                                View My Watchlist
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-semibold mb-0">Recent Notifications</h2>
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none">
                                View all
                            </a>
                        </div>

                        @forelse ($recentNotifications as $notification)
                            <div class="py-2 border-bottom {{ $notification->read_at === null ? 'app-notification-item unread' : '' }}">
                                <p class="fw-medium small mb-0">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="text-secondary small mb-0">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-secondary mb-0" style="font-size: 0.75rem;">
                                    {{ $notification->created_at?->diffForHumans() }}
                                </p>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection