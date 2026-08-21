@extends('layouts.app')

@section('title', 'Technical SEO Audit — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Technical SEO Audit</h1>
        <p class="text-secondary mb-4">Full-site crawl for crawlability, indexability, and Core Web Vitals. Takes a few minutes.</p>

        <form method="POST" action="{{ route('technical-seo.store') }}" class="card mb-4">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-9">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain"
                            placeholder="e.g. example.com" required>
                        <div class="form-text">Crawls up to 50 pages — this runs in the background and may take a few minutes.</div>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Start Scan</button>
                    </div>
                </div>
            </div>
        </form>

        <h2 class="h6 fw-semibold mb-3">Your Scans</h2>

        @if ($scans->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">No scans yet.</div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>Status</th>
                                <th>Health Score</th>
                                <th>Started</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scans as $scan)
                                <tr>
                                    <td>{{ $scan->domain }}</td>
                                    <td><span class="badge {{ $scan->status->badgeClass() }}">{{ $scan->status->label() }}</span></td>
                                    <td>{{ $scan->health_score !== null ? $scan->health_score . ' (' . $scan->health_grade . ')' : '—' }}</td>
                                    <td class="small text-secondary">{{ $scan->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('technical-seo.show', $scan) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection