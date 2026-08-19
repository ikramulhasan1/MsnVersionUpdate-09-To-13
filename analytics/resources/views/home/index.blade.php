@extends('layouts.app')

@section('title', 'Website Audit & Analysis Platform — Know What\'s Holding Your Website Back')

@section('content')
    {{--
        Phase N1.5 (Homepage + Quick Audit Hero) — this whole file
        replaces the old single-form home page. 'home' stays the one
        deliberately public route in this app (see routes/web.php's own
        docblock) — every section below is reachable with no login at
        all, by design, since this page's whole job is to convince an
        anonymous visitor to sign up.

        PRODUCTION GAP CLOSED — read before making the Hero form
        unconditionally the public Quick-only one again: the OLD home
        page served DOUBLE duty pre-Phase-N1 — it was both the public
        marketing entry point AND the actual audit-submission form a
        LOGGED-IN person used too (with its own Full/Quick mode radio,
        posting to audits.store). Replacing it outright with only the
        public Quick-Audit-only Hero removed the ONLY UI path to
        submitting a Full Audit at all — audits.store itself still
        existed and worked, nothing pointed a logged-in person's
        browser at it anymore. The fix: the Hero below is conditional
        on auth()->check() — a logged-in person sees the ORIGINAL
        Full/Quick form (posting to audits.store, respecting their own
        plan's daily limit via App\Audit\Services\AuditService::submit()),
        an anonymous visitor sees the public Quick-only Hero (posting to
        quick-audit) this phase actually added.
    --}}

    @auth
        {{-- ==================== HERO (logged-in: full form) ==================== --}}
        <section class="hero">
            <div class="container text-center">
                <h1 class="hero-title">Know exactly what's holding your website back.</h1>
                <p class="hero-subtitle">
                    Enter a URL and get a full technical, SEO, performance, security and UX audit
                    &mdash; automatically.
                </p>

                @if (session('plan_limit_message'))
                    <div class="alert alert-warning text-start mx-auto audit-form-width" role="alert">
                        {{ session('plan_limit_message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger text-start mx-auto audit-form-width" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="audit-form" action="{{ route('audits.store') }}" method="POST"
                    class="audit-form mx-auto audit-form-width">
                    @csrf

                    <div class="d-flex justify-content-center gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" id="audit-mode-full"
                                value="full" @checked(old('mode', 'full') === 'full')>
                            <label class="form-check-label" for="audit-mode-full"
                                title="{{ \App\Audit\Enums\AuditMode::FULL->description() }}">
                                Full Audit
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" id="audit-mode-quick"
                                value="quick" @checked(old('mode') === 'quick')>
                            <label class="form-check-label" for="audit-mode-quick"
                                title="{{ \App\Audit\Enums\AuditMode::QUICK->description() }}">
                                Quick Scan
                            </label>
                        </div>
                    </div>

                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text">https://</span>
                        <input type="text" name="url" id="url" class="form-control" placeholder="example.com"
                            value="{{ old('url') }}" required autofocus>
                        <button class="btn btn-primary px-4" type="submit" id="audit-submit">
                            Analyze
                        </button>
                    </div>
                    <p class="text-secondary small mt-2 mb-0" id="audit-mode-hint">
                        {{ old('mode') === 'quick' ? \App\Audit\Enums\AuditMode::QUICK->description() : \App\Audit\Enums\AuditMode::FULL->description() }}
                    </p>
                </form>

                <p class="text-secondary small mt-3 mb-0">
                    Need to audit multiple websites at once?
                    <a href="{{ route('bulk-audits.create') }}">Try Bulk Audit</a>.
                </p>
            </div>
        </section>
    @else
        {{-- ==================== HERO (logged-out: public Quick Audit) ==================== --}}
        <section class="hero-n15">
            <div class="container text-center">
                <span class="hero-n15-eyebrow">Free instant website audit</span>
                <h1 class="hero-n15-title">
                    Know exactly what's holding<br class="d-none d-md-block"> your website back.
                </h1>
                <p class="hero-n15-subtitle">
                    Enter any URL and get a real technical, SEO, performance, security, and accessibility
                    audit — automatically, in seconds.
                </p>

                @if (session('plan_limit_message'))
                    <div class="alert alert-warning text-start mx-auto audit-form-width" role="alert">
                        {{ session('plan_limit_message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger text-start mx-auto audit-form-width" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{--
                    Phase N1.5 — posts to quick-audit (public, no login
                    required — see App\Http\Controllers\AuditController::quickAudit()'s
                    own docblock), NOT audits.store. No mode selector at
                    all: this Hero always runs AuditMode::QUICK, by this
                    phase's own explicit design.
                --}}
                <form id="quick-audit-form" action="{{ route('quick-audit') }}" method="POST"
                    class="audit-form mx-auto audit-form-width">
                    @csrf

                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text">https://</span>
                        <input type="text" name="url" id="url" class="form-control" placeholder="example.com"
                            value="{{ old('url') }}" required autofocus>
                        <button class="btn btn-primary px-4" type="submit" id="quick-audit-submit">
                            Run Quick Audit
                        </button>
                    </div>
                    <p class="text-secondary small mt-2 mb-0">
                        Free, no credit card. Sign up to see your full results.
                    </p>
                </form>
            </div>
        </section>
    @endauth

    {{-- ============================== FEATURES ============================== --}}
    <section class="n15-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="n15-section-title">Everything you need in one audit</h2>
                <p class="n15-section-subtitle">
                    Six categories of real, actionable findings — not a black-box score.
                </p>
            </div>

            <div class="row g-4">
                @foreach ([
                    ['label' => 'SEO', 'desc' => 'Titles, meta descriptions, headings, schema, and indexability issues that cost you rankings.', 'icon' => '<path d="M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM21 21l-4.35-4.35" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Performance', 'desc' => 'Real Core Web Vitals and page-speed diagnostics from Google PageSpeed Insights.', 'icon' => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Security', 'desc' => 'SSL, security headers, and common vulnerabilities checked automatically.', 'icon' => '<path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Accessibility', 'desc' => 'Alt text, contrast, ARIA, and keyboard-navigation issues real visitors hit.', 'icon' => '<circle cx="12" cy="5" r="2"/><path d="M12 7v6m0 0l-4 8m4-8l4 8m-8-4h8" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Business Intelligence', 'desc' => 'Technology stack, contact details, and business signals detected on the site.', 'icon' => '<path d="M3 3v18h18M9 17V9m4 8V5m4 12v-6" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Website Discovery', 'desc' => 'Find and compare websites by industry and location — not just audit one at a time.', 'icon' => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3" stroke-linecap="round" stroke-linejoin="round"/>'],
                ] as $feature)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="n15-feature-card">
                            <svg class="n15-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                {!! $feature['icon'] !!}
                            </svg>
                            <h3 class="n15-feature-title">{{ $feature['label'] }}</h3>
                            <p class="n15-feature-desc">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== HOW IT WORKS ============================== --}}
    <section class="n15-section n15-section-alt">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="n15-section-title">How it works</h2>
                <p class="n15-section-subtitle">From URL to actionable report in three steps.</p>
            </div>

            <div class="row g-4 text-center">
                @foreach ([
                    ['step' => '1', 'title' => 'Enter a URL', 'desc' => 'Paste any website address into the box above — no setup, no install.'],
                    ['step' => '2', 'title' => 'We analyze it', 'desc' => 'Our pipeline crawls the site and runs real SEO, performance, security, and accessibility checks.'],
                    ['step' => '3', 'title' => 'Get your report', 'desc' => 'See scores, specific issues, and prioritized recommendations — ready to act on or share.'],
                ] as $step)
                    <div class="col-12 col-md-4">
                        <div class="n15-step-number">{{ $step['step'] }}</div>
                        <h3 class="n15-feature-title">{{ $step['title'] }}</h3>
                        <p class="n15-feature-desc">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== SOCIAL PROOF ============================== --}}
    {{--
        Phase N1.5 — a deliberate PLACEHOLDER, not fabricated
        testimonials: inventing fake customer quotes/names would be
        genuinely dishonest marketing copy on a real, live site. This
        renders honest, factual stat-style copy for now; replace the
        three items below with real customer testimonials once this
        app actually has some, without needing to touch this section's
        own layout/CSS.
    --}}
    <section class="n15-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="n15-section-title">Built for real websites</h2>
            </div>

            <div class="row g-4 text-center">
                @foreach ([
                    ['stat' => 'Multi-page', 'label' => 'crawl depth, not just a homepage snapshot'],
                    ['stat' => '6 categories', 'label' => 'SEO, Performance, Security, Accessibility, Business Intelligence, Discovery'],
                    ['stat' => 'PDF & Excel', 'label' => 'export a full report to share with your team or client'],
                ] as $item)
                    <div class="col-12 col-md-4">
                        <p class="n15-stat">{{ $item['stat'] }}</p>
                        <p class="text-secondary small">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== PRICING PREVIEW ============================== --}}
    <section class="n15-section n15-section-alt">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="n15-section-title">Simple pricing</h2>
                <p class="n15-section-subtitle">
                    Every new account starts with a free 3-day trial — no credit card required.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                @forelse ($plans as $plan)
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="n15-pricing-card">
                            <h3 class="n15-feature-title">{{ $plan->name }}</h3>
                            <p class="n15-pricing-price">{{ $plan->priceLabel() }}</p>
                            <p class="text-secondary small mb-3">{{ $plan->description }}</p>
                            <ul class="n15-pricing-list">
                                <li>{{ $plan->allowsFeature('run-audit') ? 'Website Audit' : 'No Website Audit' }}</li>
                                <li>
                                    {{ $plan->allowsFeature('run-bulk-audit') ? 'Bulk Audit' : 'No Bulk Audit' }}
                                </li>
                                <li>{{ $plan->allowsFeature('export-data') ? 'PDF/Excel export' : 'No export' }}</li>
                                <li>
                                    {{ $plan->dailyAuditLimit() !== null ? $plan->dailyAuditLimit() . ' audits/day' : 'Unlimited audits' }}
                                </li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100">
                                Start Free Trial
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary text-center">Pricing plans are being finalized — check back soon.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Phase N1.5 — handles BOTH possible Hero forms (only one is
        // ever actually in the DOM for a given page load, per the
        // logged-in/logged-out check above): #audit-form (logged-in,
        // Full/Quick mode) or #quick-audit-form (logged-out, Quick-only,
        // public).
        const heroForm = document.getElementById('audit-form') || document.getElementById('quick-audit-form');

        heroForm?.addEventListener('submit', function (event) {
            const urlField = document.getElementById('url');
            let value = urlField.value.trim();

            // Allow users to type "example.com" and normalize to a full URL
            // before the native form submission fires.
            if (value && !/^https?:\/\//i.test(value)) {
                urlField.value = 'https://' + value;
            }

            AuditApp.showLoadingOverlay();
        });

        // Phase K1 (Quick Scan Mode) — only relevant for the logged-in
        // #audit-form (its own Full/Quick radio doesn't exist at all in
        // the logged-out Hero), so this simply does nothing when those
        // radios aren't present.
        const AUDIT_MODE_HINTS = {
            full: 'Crawls multiple pages and includes real PageSpeed Insights data. Takes longer, most complete.',
            quick: 'Homepage only, no PageSpeed Insights call. Much faster, less depth.',
        };

        document.querySelectorAll('input[name="mode"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const hint = document.getElementById('audit-mode-hint');
                if (hint && AUDIT_MODE_HINTS[radio.value]) {
                    hint.textContent = AUDIT_MODE_HINTS[radio.value];
                }
            });
        });
    </script>
@endpush