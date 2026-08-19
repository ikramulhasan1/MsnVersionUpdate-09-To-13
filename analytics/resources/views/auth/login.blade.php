@extends('layouts.guest')

@section('title', 'Log In — Website Audit & Analysis Platform')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Welcome back</h1>
    <p class="text-secondary mb-4">Log in to run audits, manage discovery, and view your reports.</p>

    @if (session('status'))
        <div class="alert alert-info small">{{ session('status') }}</div>
    @endif

    <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary auth-google-btn mb-2">
        <img src="https://www.google.com/favicon.ico" alt="" aria-hidden="true">
        Continue with Google
    </a>

    <div class="auth-divider">or log in with email</div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot
                        password?</a>
                @endif
            </div>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Log In</button>
    </form>

    <p class="auth-footer-link">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </p>
@endsection