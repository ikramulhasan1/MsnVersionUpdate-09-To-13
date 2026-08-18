@extends('layouts.app')

@section('title', 'Audit Result — ' . display_host($audit->url))

@section('content')
    <section class="result-header">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <p class="text-secondary small mb-1">Audit for</p>
                    <h1 class="h3 mb-0">{{ display_host($audit->url) }}</h1>
                    <a href="{{ $audit->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none">
                        {{ $audit->url }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ audit_status_badge_class($audit->status) }} fs-6 px-3 py-2">
                        {{ audit_status_label($audit->status) }}
                    </span>
                    {{--
                        PRODUCTION INCIDENT (Phase M3) — this "Print Report"
                        button only ever called the BROWSER's own
                        window.print() on the LIVE page (its real Chart.js
                        canvases included) — never
                        route('audits.export', $audit), the actual
                        dompdf-rendered PDF export
                        (App\Audit\Export\Pdf\AuditPdfExportService, whose own
                        Charts section — resources/views/audit/pdf/partials/charts.blade.php
                        — was ALREADY built as dompdf-safe HTML/CSS bars
                        specifically to avoid a JS-canvas-in-PDF problem) —
                        that route existed and worked, it was simply never
                        linked from anywhere in this page. Browser print-to-PDF
                        is well known to render <canvas> content unreliably
                        (often blank, or cut off mid-page depending on when
                        print fires relative to Chart.js finishing its own
                        draw), which is almost certainly why charts (and,
                        depending on where a page break fell, sometimes score
                        figures) were reported missing/wrong specifically in
                        "the PDF" — the PDF being downloaded was never
                        actually this app's own dompdf export at all.
                        window.print() is left in place as a quick,
                        no-download option for someone who just wants to
                        print/preview the page as-is — but "Download PDF
                        Report" (this new link) is now the one real way to
                        get the actual, chart-safe, correctly-scored file.
                    --}}
                    <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" onclick="window.print()">
                        Print Report
                    </button>
                    @if ($audit->status === \App\Audit\Enums\AuditStatus::COMPLETED)
                        <a href="{{ route('audits.export', $audit) }}"
                            class="btn btn-outline-secondary btn-sm d-print-none">
                            Download PDF Report
                        </a>
                        <a href="{{ route('audits.export.excel', $audit) }}"
                            class="btn btn-outline-secondary btn-sm d-print-none">
                            Download Excel Report
                        </a>
                    @else
                        <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" disabled
                            title="Available once the audit finishes">
                            Download PDF Report
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" disabled
                            title="Available once the audit finishes">
                            Download Excel Report
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container dashboard-section">
        @if (!$audit->status->isFinished())
            <div class="card audit-pending-card mx-auto text-center" id="audit-progress-card"
                data-audit-progress-url="{{ route('audits.progress', $audit) }}"
                data-audit-show-url="{{ route('audits.show', $audit) }}">
                <div class="card-body py-5">
                    <div class="progress mb-3" role="progressbar" aria-label="Audit progress" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100" style="height: 0.75rem;">
                        <div id="audit-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            style="width: 0%"></div>
                    </div>
                    <p class="fw-semibold mb-1"><span id="audit-progress-percent">0</span>%</p>
                    <h2 class="h5" id="audit-progress-label">
                        {{ audit_status_label($audit->status) }}&hellip;
                    </h2>
                    {{-- ETA — see public/js/audit-progress.js's own docblock for how
                         this is estimated (elapsed-time / percent-so-far, extrapolated
                         to 100%) and why it ticks down every second on its own rather
                         than only updating when a real poll response arrives. --}}
                    <p class="text-secondary small fw-medium mb-2" id="audit-progress-eta">
                        Estimating time remaining&hellip;
                    </p>
                    <p class="text-secondary mb-0">
                        This usually takes a few seconds. The page updates automatically &mdash; no need to refresh.
                    </p>
                    <p class="text-warning small mt-3 mb-0 d-none" id="audit-progress-stall-hint">
                        This is taking much longer than usual. It may be stuck &mdash; you're welcome to keep
                        waiting, or check back later.
                    </p>
                </div>
            </div>


            @push('scripts')
                <script src="{{ asset('js/audit-progress.js') }}"></script>
            @endpush
        @elseif ($audit->status === \App\Audit\Enums\AuditStatus::FAILED)
            <div class="card mx-auto text-center">
                <div class="card-body py-5">
                    <h2 class="h5">This audit couldn't be completed</h2>
                    <p class="text-secondary mb-0">Something went wrong while analyzing this website.</p>
                </div>
            </div>
        @else
            {{-- Phase M1 — see audit/partials/full-report.blade.php's own
                 docblock for why this used to be built inline here and is
                 now a shared partial (also included by
                 discovery/show.blade.php's own "Full Audit Report" tab for
                 whichever completed Audit matches a DiscoveredWebsite's
                 own URL). --}}
            @include('audit.partials.full-report', [
                'audit' => $audit,
                'categories' => $categories,
                'overallScore' => $overallScore,
                'prospectQualification' => $prospectQualification,
                'outreachDraft' => $outreachDraft,
                'generatedAt' => $audit->updated_at?->format('M j, Y \a\t g:i A') ?? 'just now',
            ])
        @endif
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
    @if ($outreachDraft)
        <script src="{{ asset('js/outreach-copy.js') }}"></script>
    @endif
@endpush