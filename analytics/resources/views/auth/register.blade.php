@extends('layouts.guest')

@section('title', 'Sign Up — Website Audit & Analysis Platform')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Create your account</h1>
    <p class="text-secondary mb-4">Start auditing websites in minutes.</p>

    <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary auth-google-btn mb-2">
        <img src="https://www.google.com/favicon.ico" alt="" aria-hidden="true">
        Continue with Google
    </a>

    <div class="auth-divider">or sign up with email</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Account</button>
    </form>

    <p class="auth-footer-link">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </p>
@endsection