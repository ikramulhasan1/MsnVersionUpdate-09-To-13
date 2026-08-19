@extends('layouts.guest')

@section('title', 'Forgot Password — Website Audit & Analysis Platform')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Forgot your password?</h1>
    <p class="text-secondary mb-4">
        No problem. Enter your email and we'll send you a password reset link.
    </p>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Email Password Reset Link</button>
    </form>

    <p class="auth-footer-link">
        <a href="{{ route('login') }}">Back to log in</a>
    </p>
@endsection