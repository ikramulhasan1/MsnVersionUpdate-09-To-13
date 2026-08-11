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
                <p class="text-secondary small mt-2 mb-0">
                    Full report in minutes. No signup required.
                </p>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
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
