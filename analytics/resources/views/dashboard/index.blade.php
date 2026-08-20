@extends('layouts.app')

@section('title', 'Dashboard — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        {{--
            Phase N1.5 — flashed by
            App\Http\Middleware\EnsurePlanAllowsFeature or
            App\Http\Controllers\AuditController::store()'s own
            PlanLimitExceededException catch, whenever a blocked action
            redirected here instead of completing.
        --}}
        @if (session('plan_limit_message'))
            <div class="alert alert-warning small mb-4">{{ session('plan_limit_message') }}</div>
        @endif

        <h1 class="h4 fw-semibold mb-1">Welcome back, {{ auth()->user()->name }}</h1>

        @if ($isAdmin)
            <div class="alert alert-info small mb-4">
                You're viewing as Admin — the numbers and audits below include every user's data
                (including older audits from before per-account ownership existed), not just your own.
            </div>
        @endif
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

                {{--
                    Phase N1.5 (Free Trial) — real plan/trial data
                    (see App\Http\Controllers\DashboardController::index()'s
                    own docblock). Phase N5 (Dynamic Pricing/Subscription)
                    — "Upgrade Now" below now links to the real
                    subscription.upgrade page.
                --}}
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-semibold mb-2">Subscription</h2>

                        @if ($plan === null)
                            <p class="text-secondary small mb-0">No plan assigned yet.</p>
                        @else
                            <p class="mb-1">
                                <span class="fw-medium">{{ $plan->name }}</span>
                                @if ($onTrial)
                                    <span class="badge bg-warning-subtle text-warning-emphasis ms-1">
                                        Trial &mdash; ends {{ auth()->user()->trial_ends_at->diffForHumans() }}
                                    </span>
                                @elseif ($trialExpired)
                                    <span class="badge bg-danger-subtle text-danger-emphasis ms-1">
                                        Trial ended
                                    </span>
                                @endif
                            </p>

                            @if ($trialExpired)
                                <p class="text-secondary small mb-3">
                                    Your free trial has ended. Upgrade to keep running audits, use Bulk
                                    Audit, and export your reports.
                                </p>
                                <a href="{{ route('subscription.upgrade') }}" class="btn btn-primary btn-sm">
                                    Upgrade Now
                                </a>
                            @elseif ($onTrial)
                                <p class="text-secondary small mb-0">
                                    {{ $plan->dailyAuditLimit() !== null ? $plan->dailyAuditLimit() . ' audit(s)/day, ' : '' }}
                                    {{ $plan->allowsFeature('run-bulk-audit') ? '' : 'no Bulk Audit, ' }}
                                    {{ $plan->allowsFeature('export-data') ? '' : 'no exports' }}
                                    &mdash; during your trial.
                                </p>
                            @else
                                <p class="text-secondary small mb-0">{{ $plan->priceLabel() }}</p>
                            @endif
                        @endif
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
                            {{-- Phase N5 — always available here, not
                                 only when the trial has expired (the
                                 Subscription card's own conditional CTA
                                 above only shows then) — a real
                                 subscriber wanting to change plans
                                 shouldn't need to wait for their trial
                                 to lapse first. --}}
                            <a href="{{ route('subscription.upgrade') }}" class="btn btn-outline-secondary btn-sm">
                                Upgrade Subscription
                            </a>
                            {{-- Phase N6 (Multiple Payment Methods) — this
                                 phase's own explicit "Billing History
                                 page" requirement, linked from here. --}}
                            <a href="{{ route('billing.history') }}" class="btn btn-outline-secondary btn-sm">
                                Billing History
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