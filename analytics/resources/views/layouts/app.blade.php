<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Phase N2 — public/js/notifications.js reads this directly
         (rather than relying solely on finding some other @csrf-rendered
         form's own hidden input already on the page, which isn't
         guaranteed on every page) to submit its own mark-as-read form
         POSTs. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    {{--
        Phase N2 (Sidebar Navigation) — replaces the old single
        top-navbar layout. app-sidebar (see that partial's own
        docblock) is the primary navigation now; app-topbar keeps only
        the sidebar-collapse toggle, theme toggle, and user menu — the
        same three things this app's OLD navbar's own right-hand side
        already had, just relocated rather than redesigned.
    --}}
    @include('layouts.partials.sidebar')

    <div class="app-main-wrapper">
        <div class="app-topbar d-print-none">
            <button type="button" id="app-sidebar-toggle" class="btn btn-sm app-sidebar-toggle-btn"
                aria-label="Toggle sidebar" aria-expanded="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="ms-auto d-flex align-items-center">
                <button type="button" id="theme-toggle" class="btn btn-sm theme-toggle"
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

                {{-- Phase N1 (Authentication Foundation) — see this
                     block's own original comment (unchanged by Phase N2,
                     just relocated from the old navbar into this new top
                     bar): @auth rather than Auth::check() so a
                     logged-out visitor on the public homepage sees
                     Login/Sign Up links instead of a broken avatar. --}}
                @auth
                    <div class="dropdown ms-3">
                        <button class="btn btn-sm app-user-menu-toggle dropdown-toggle d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="app-user-avatar"
                                    width="24" height="24">
                            @else
                                <span class="app-user-avatar app-user-avatar-initials">{{ auth()->user()->initials() }}</span>
                            @endif
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary ms-3">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary ms-2">Sign Up</a>
                @endauth
            </div>
        </div>

        <main>
            @yield('content')
        </main>

        <footer class="app-footer text-center text-secondary py-4 d-print-none">
            <div class="container small">
                &copy; {{ date('Y') }} Website Audit &amp; Analysis Platform.
            </div>
        </footer>
    </div>

    <div id="loading-overlay" class="loading-overlay d-none d-print-none">
        <div class="text-center">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
            <p class="mt-3 mb-0 fw-medium">Analyzing website&hellip;</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    @auth
        {{-- Only loaded for a logged-in visitor — the bell dropdown this
             script drives (see resources/views/layouts/partials/sidebar.blade.php's
             own docblock) doesn't exist in the DOM at all for a
             logged-out one, so there's nothing for it to do. --}}
        <script src="{{ asset('js/notifications.js') }}"
            data-recent-url="{{ route('notifications.recent') }}"></script>
    @endauth
    @stack('scripts')
</body>

</html>