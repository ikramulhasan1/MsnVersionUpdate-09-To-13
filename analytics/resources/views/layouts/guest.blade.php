<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Website Audit & Analysis Platform')</title>

    {{-- Same pre-first-paint theme script as layouts/app.blade.php — an
         auth page switching from light to dark right after load would
         be just as jarring here as anywhere else in this app. --}}
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
    <link rel="stylesheet"
        href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    @stack('styles')
</head>

<body class="auth-body">
    <div class="auth-page d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="auth-card-wrapper w-100 px-3">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <span class="brand-mark">AI</span>
                    <span class="fw-semibold fs-4 ms-2 auth-brand-text">Website Audit</span>
                </a>
            </div>

            <div class="card auth-card mx-auto">
                <div class="card-body p-4 p-md-5">
                    @yield('content')
                </div>
            </div>

            <p class="text-center text-secondary small mt-4 mb-0">
                &copy; {{ date('Y') }} Website Audit &amp; Analysis Platform.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    @stack('scripts')
</body>

</html>