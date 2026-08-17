{{--
    Phase K3 (Bulk Audit) — a deliberately minimal placeholder.
    BulkAuditController::show()'s own docblock explains why: Phase K5
    replaces this file's content with the real results dashboard
    (per-audit scores, a live-polling progress bar, export) — this
    phase only needs somewhere real for store() to redirect a person to
    after a bulk submission, rather than a 404.

    Expects:
      $batch  App\Models\BulkAuditBatch
--}}
@extends('layouts.app')

@section('title', $batch->name ?? 'Bulk Audit Batch')

@section('content')
    <section class="container dashboard-section">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="mb-4">
            <p class="text-secondary small mb-1">Website Audit</p>
            <h1 class="h3 mb-0">{{ $batch->name ?? 'Bulk Audit Batch' }}</h1>
            <p class="text-secondary mt-2 mb-0">
                {{ $batch->total_count }} website(s) &mdash; {{ $batch->mode->label() }}
            </p>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <p class="mb-3">
                    <span class="badge {{ $batch->status->badgeClass() }}">{{ $batch->status->label() }}</span>
                    <span class="text-secondary small ms-2">
                        {{ $batch->completed_count + $batch->failed_count }} of {{ $batch->total_count }} finished
                        ({{ $batch->progressPercent() }}%)
                    </span>
                </p>

                <p class="text-secondary small mb-0">
                    The full results dashboard for this batch (per-website scores, live progress, export) is
                    coming in a later phase — for now, each website's own audit can be found from
                    <a href="{{ route('home') }}">the main Audit page</a> once it finishes.
                </p>
            </div>
        </div>
    </section>
@endsection