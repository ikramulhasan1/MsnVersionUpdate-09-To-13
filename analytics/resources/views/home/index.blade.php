@extends('layouts.app')

@section('title', 'Website Audit & Analysis Platform')

@section('content')
    <section class="hero">
        <div class="container text-center">
            <h1 class="hero-title">Know exactly what's holding your website back.</h1>
            <p class="hero-subtitle">
                Enter a URL and get a full technical, SEO, performance, security and UX audit &mdash; automatically.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger text-start mx-auto audit-form-width" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                id="audit-form"
                action="{{ route('audits.store') }}"
                method="POST"
                class="audit-form mx-auto audit-form-width"
            >
                @csrf

                {{--
                    Phase K1 (Quick Scan Mode) — App\Audit\Enums\AuditMode's
                    own docblock has the full picture of what each option
                    actually changes. Full Audit is old('mode') !== 'quick' by
                    default (i.e. selected whenever nothing else was already
                    chosen on a validation-error redisplay), matching this
                    app's existing behavior for every audit submitted before
                    this phase existed.
                --}}
                <div class="d-flex justify-content-center gap-4 mb-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="mode"
                            id="audit-mode-full"
                            value="full"
                            @checked(old('mode', 'full') === 'full')
                        >
                        <label class="form-check-label" for="audit-mode-full" title="{{ \App\Audit\Enums\AuditMode::FULL->description() }}">
                            Full Audit
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="mode"
                            id="audit-mode-quick"
                            value="quick"
                            @checked(old('mode') === 'quick')
                        >
                        <label class="form-check-label" for="audit-mode-quick" title="{{ \App\Audit\Enums\AuditMode::QUICK->description() }}">
                            Quick Scan
                        </label>
                    </div>
                </div>

                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text">https://</span>
                    <input
                        type="text"
                        name="url"
                        id="url"
                        class="form-control"
                        placeholder="example.com"
                        value="{{ old('url') }}"
                        required
                        autofocus
                    >
                    <button class="btn btn-primary px-4" type="submit" id="audit-submit">
                        Analyze
                    </button>
                </div>
                <p class="text-secondary small mt-2 mb-0" id="audit-mode-hint">
                    {{ old('mode') === 'quick' ? \App\Audit\Enums\AuditMode::QUICK->description() : \App\Audit\Enums\AuditMode::FULL->description() }}
                </p>
            </form>

            {{-- Phase K5 — a second, more contextual entry point into
                 /bulk-audits/create alongside the navbar link
                 (resources/views/layouts/app.blade.php) — right where a
                 person auditing ONE site here would naturally wonder
                 whether auditing several at once is possible too. --}}
            <p class="text-secondary small mt-3 mb-0">
                Need to audit multiple websites at once?
                <a href="{{ route('bulk-audits.create') }}">Try Bulk Audit</a>.
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Phase K1 (Quick Scan Mode) — keeps the hint paragraph under the
        // URL field in sync with whichever radio is currently selected,
        // without a page reload. The descriptions themselves stay
        // server-rendered (App\Audit\Enums\AuditMode::description()) on
        // first load/validation-error redisplay; this only needs to swap
        // between the same two fixed strings afterward, so they're
        // duplicated here in plain JS rather than round-tripping to the
        // server for text that never changes at runtime.
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

        document.getElementById('audit-form')?.addEventListener('submit', function (event) {
            const urlField = document.getElementById('url');
            let value = urlField.value.trim();

            // Allow users to type "example.com" and normalize to a full URL
            // before the native form submission fires.
            if (value && !/^https?:\/\//i.test(value)) {
                urlField.value = 'https://' + value;
            }

            AuditApp.showLoadingOverlay();
        });
    </script>
@endpush