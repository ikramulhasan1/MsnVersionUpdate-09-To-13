<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Website Audit & Analysis Platform')</title>

    {{-- Applied before first paint to avoid a light/dark flash on load --}}
    <script>
        (function() {
            var saved = localStorage.getItem('audit-theme');
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{--
        Cache-busted via a ?v= query string built from the file's own
        last-modified time (falls back to time() if the file somehow
        isn't readable, e.g. an unusual deployment layout — never a
        broken <link>) — every deploy that edits app.css automatically
        changes this URL, so browsers fetch the fresh file immediately
        instead of serving a stale cached copy until the next hard
        refresh. This was very likely the cause of the "watermark
        renders huge and fully opaque" symptom reported after the PDF/
        dashboard polish pass: the HTML (dashboard.blade.php) picked up
        the new watermark markup right away, but a browser that had
        already cached the previous app.css kept using it — without the
        watermark's own CSS rule (position/size/opacity), the SVG falls
        back to normal inline flow at its default size and full opacity,
        which matches exactly what was reported.
    --}}
    <link rel="stylesheet"
        href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg app-navbar d-print-none">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">
                <span class="brand-mark">AI</span> Website Audit
            </a>

            <a class="app-navbar-link ms-3 {{ request()->routeIs('discovery.*') ? 'active' : '' }}"
                href="{{ route('discovery.index') }}">
                <span class="brand-mark">WD</span> Website Discovery
            </a>

            {{-- Phase K3/K5 (Bulk Audit) — the only navbar entry point into
                 /bulk-audits/create; before this, that page (and the whole
                 bulk-audit feature) had no link ANYWHERE pointing to it —
                 Discovery's own "Bulk Audit Selected" floating bar is a
                 separate, contextual entry point (only ever visible once
                 some result cards are already selected there), not a
                 substitute for a real, always-visible way in. --}}
            <a class="app-navbar-link ms-3 {{ request()->routeIs('bulk-audits.*') ? 'active' : '' }}"
                href="{{ route('bulk-audits.create') }}">
                <span class="brand-mark">BA</span> Bulk Audit
            </a>

            <button type="button" id="theme-toggle" class="btn btn-sm theme-toggle ms-auto"
                aria-label="Toggle dark mode" aria-pressed="false">
                <svg class="theme-toggle-icon theme-toggle-icon-light" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path
                        d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41">
                    </path>
                </svg>
                <svg class="theme-toggle-icon theme-toggle-icon-dark" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="app-footer text-center text-secondary py-4 d-print-none">
        <div class="container small">
            &copy; {{ date('Y') }} Website Audit &amp; Analysis Platform.
        </div>
    </footer>

    <div id="loading-overlay" class="loading-overlay d-none d-print-none">
        <div class="text-center">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
            <p class="mt-3 mb-0 fw-medium">Analyzing website&hellip;</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    @stack('scripts')
</body>

</html>
