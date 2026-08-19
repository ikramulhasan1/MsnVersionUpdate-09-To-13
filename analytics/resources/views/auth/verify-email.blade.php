@extends('layouts.guest')

@section('title', 'Verify Email — Website Audit & Analysis Platform')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Verify your email</h1>
    <p class="text-secondary mb-4">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link
        we just emailed to you. If you didn't receive it, we can send another.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success small">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">
            @csrf
            <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">Log Out</button>
        </form>
    </div>
@endsection