@extends('layouts.app')

@section('title', 'DataForSEO Quick Setup — Admin')

@section('content')
    <section class="container py-4" style="max-width: 640px;">
        <p class="text-secondary small mb-1">Admin</p>
        <h1 class="h4 fw-semibold mb-1">DataForSEO Quick Setup</h1>
        <p class="text-secondary mb-4">
            Enter your DataForSEO account credentials once — this creates (or updates) all three
            DataForSEO providers (Keywords Data, Labs, Backlinks) at the same time, since they all
            share the same login/password.
        </p>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if (session('test_error'))
            <div class="alert alert-danger small">{{ session('test_error') }}</div>
        @endif

        @if ($alreadySetUp)
            <div class="alert alert-info small">
                All three DataForSEO providers are already set up via Quick Setup. Submitting below
                will update their credentials (e.g. after rotating your DataForSEO password) —
                nothing else about them changes.
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3">What this enables</h2>
                <ul class="small text-secondary mb-0">
                    <li>Keyword Research &amp; Keyword Magic Tool — volume, CPC, difficulty, intent, related keywords, trend, SERP data</li>
                    <li>Competitor Analysis — domain overview, organic competitors, ranking keywords, top pages</li>
                    <li>Backlink Analysis — backlink summary, backlink list, referring domains, anchor text distribution</li>
                </ul>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.api-providers.quick-setup-dataforseo.store') }}" class="card">
            @csrf
            <div class="card-body p-4">
                <div class="mb-3">
                    <label for="login" class="form-label">DataForSEO API Login (email)</label>
                    <input type="text" class="form-control @error('login') is-invalid @enderror" id="login"
                        name="login" required>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">DataForSEO API Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" autocomplete="off" required>
                    <div class="form-text">From your DataForSEO dashboard — not your account login password.</div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $alreadySetUp ? 'Update All Three' : 'Set Up All Three' }}
                </button>
                <a href="{{ route('admin.api-providers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>

        <p class="text-secondary small mt-4">
            Need finer control (e.g. only some capabilities active, or a different DataForSEO account
            per product)? Use the normal
            <a href="{{ route('admin.api-providers.create') }}">New Provider</a> form instead — this
            page is only a shortcut for the common "enable everything with one account" case.
        </p>
    </section>
@endsection