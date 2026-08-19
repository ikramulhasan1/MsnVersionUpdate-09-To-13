@extends('layouts.app')

@section('title', 'Upgrade Subscription — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Upgrade Subscription</h1>
        <p class="text-secondary mb-4">
            @if ($currentPlan !== null)
                You're currently on <strong>{{ $currentPlan->name }}</strong>.
            @else
                You don't have a plan assigned yet.
            @endif
        </p>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if ($pendingRequests->isNotEmpty())
            <div class="alert alert-info small">
                You have a pending request to upgrade to
                @foreach ($pendingRequests as $request)
                    <strong>{{ $request->plan->name }}</strong>{{ ! $loop->last ? ', ' : '' }}
                @endforeach
                — we'll be in touch shortly.
            </div>
        @endif

        @if ($plans->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    No other plans are available to upgrade to right now.
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($plans as $plan)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="n15-pricing-card">
                            <h2 class="n15-feature-title">{{ $plan->name }}</h2>
                            <p class="n15-pricing-price">{{ $plan->priceLabel() }}</p>
                            <p class="text-secondary small mb-3">{{ $plan->description }}</p>
                            <ul class="n15-pricing-list">
                                <li>{{ $plan->allowsFeature('run-audit') ? 'Website Audit' : 'No Website Audit' }}</li>
                                <li>{{ $plan->allowsFeature('run-bulk-audit') ? 'Bulk Audit' : 'No Bulk Audit' }}</li>
                                <li>{{ $plan->allowsFeature('export-data') ? 'PDF/Excel export' : 'No export' }}</li>
                                <li>
                                    {{ $plan->dailyAuditLimit() !== null ? $plan->dailyAuditLimit() . ' audits/day' : 'Unlimited audits' }}
                                </li>
                            </ul>

                            @php
                                $alreadyRequested = $pendingRequests->contains(fn ($r) => $r->plan_id === $plan->id);
                            @endphp

                            {{--
                                Phase N6 (Multiple Payment Methods) —
                                real checkout, now that it exists,
                                replaces "Request This Plan" as the
                                PRIMARY action; the request-based flow
                                (Phase N5, fulfilled manually by an
                                Admin) stays available as a smaller
                                secondary option below it — useful for
                                someone who'd rather not pay online
                                immediately, or wants to arrange custom
                                terms directly.
                            --}}
                            <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100 mb-2">
                                Checkout
                            </a>

                            <form method="POST" action="{{ route('subscription.request-upgrade') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100"
                                    @disabled($alreadyRequested)>
                                    {{ $alreadyRequested ? 'Requested — awaiting review' : 'Or request this plan without paying online' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-secondary small mt-4">
                Checkout accepts card payments via Stripe, or bKash/Nagad/local card via SSLCommerz where
                available for a plan.
            </p>
        @endif
    </section>
@endsection