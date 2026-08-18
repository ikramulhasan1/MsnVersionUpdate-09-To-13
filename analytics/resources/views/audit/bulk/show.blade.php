{{--
    Phase K5 (Bulk Audit) — the real results dashboard, replacing the
    K3 placeholder. Two states in one page:

      - While the batch is still processing (status !== COMPLETED): a
        progress bar + "X of Y finished" summary, kept live by
        public/js/bulk-audit-progress.js polling
        GET bulk-audits.progress roughly every few seconds — mirrors
        AuditController::progress()/public/js/audit-progress.js's own
        single-audit polling pattern, rolled up to a whole batch.
      - Once finished: the results table itself — Website, Status,
        SEO/Performance/Security/Accessibility score+grade, a "View
        Full Report" link per audit. Sortable column headers (client-
        side, no server round-trip — see this file's own @push('scripts')
        block) and an Export dropdown (Excel/CSV/JSON) alongside it.

    The table is ALWAYS rendered (not only once finished) — a row for
    an audit that hasn't completed yet simply shows its own current
    status (Queued/Fetching/Crawling/Analyzing/Failed) with every score
    column dashed out, so a person watching a large batch can already
    see which specific websites are done vs still in progress, not
    just an aggregate percentage.

    Expects:
      $batch  App\Models\BulkAuditBatch (with audits eager-loaded)
      $rows   Collection<int, App\Audit\Export\DTO\BulkAuditExportRow>
--}}
@extends('layouts.app')

@section('title', $batch->name ?? 'Bulk Audit Batch')

@section('content')
    <section class="container dashboard-section" id="bulk-audit-page"
        data-bulk-audit-progress-url="{{ route('bulk-audits.progress', $batch) }}"
        data-bulk-audit-finished="{{ $batch->status->value === 'completed' ? '1' : '0' }}">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <p class="text-secondary small mb-1">Website Audit</p>
                <h1 class="h3 mb-0">{{ $batch->name ?? 'Bulk Audit Batch' }}</h1>
                <p class="text-secondary mt-2 mb-0">
                    {{ $batch->total_count }} website(s) &mdash; {{ $batch->mode->label() }}
                </p>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('bulk-audits.export', ['bulkAuditBatch' => $batch, 'format' => 'excel']) }}">
                            Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('bulk-audits.export', ['bulkAuditBatch' => $batch, 'format' => 'csv']) }}">
                            CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('bulk-audits.export', ['bulkAuditBatch' => $batch, 'format' => 'json']) }}">
                            JSON
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Progress card — hidden via JS once the batch is finished
             (see public/js/bulk-audit-progress.js); starts visible only
             when the batch isn't COMPLETED yet, so a page load AFTER
             everything's already done never flashes it at all. --}}
        <div class="card mb-4" id="bulk-audit-progress-card"
            style="{{ $batch->status->value === 'completed' ? 'display: none;' : '' }}">
            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium" id="bulk-audit-progress-label">
                        {{ $batch->status->label() }}
                    </span>
                    <span class="text-secondary small" id="bulk-audit-progress-count">
                        {{ $batch->completed_count + $batch->failed_count }} of {{ $batch->total_count }} finished
                    </span>
                </div>
                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                    aria-valuenow="{{ $batch->progressPercent() }}">
                    <div class="progress-bar" id="bulk-audit-progress-bar"
                        style="width: {{ $batch->progressPercent() }}%;"></div>
                </div>
                {{-- ETA — see public/js/bulk-audit-progress.js's own docblock for how
                     this is estimated (average time per finished audit so far,
                     extrapolated across however many are still left) and why it ticks
                     down every second on its own rather than only updating on poll. --}}
                <p class="text-secondary small fw-medium mt-2 mb-0" id="bulk-audit-progress-eta">
                    Estimating time remaining&hellip;
                </p>
                <p class="text-secondary small mt-1 mb-0">
                    This page updates automatically — no need to refresh.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="bulk-audit-results-table">
                        <thead>
                            <tr>
                                <th scope="col" data-sort-key="url">Website</th>
                                <th scope="col" data-sort-key="status">Status</th>
                                <th scope="col" data-sort-key="seo" class="text-end">SEO</th>
                                <th scope="col" data-sort-key="performance" class="text-end">Performance</th>
                                <th scope="col" data-sort-key="security" class="text-end">Security</th>
                                <th scope="col" data-sort-key="accessibility" class="text-end">Accessibility</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="bulk-audit-results-tbody">
                            @foreach ($batch->audits as $audit)
                                @php
                                    $row = $rows->first(fn ($r) => $r->url === $audit->url);
                                @endphp
                                <tr data-url="{{ $audit->url }}" data-status="{{ $audit->status->value }}"
                                    data-seo="{{ $row->seoScore ?? -1 }}"
                                    data-performance="{{ $row->performanceScore ?? -1 }}"
                                    data-security="{{ $row->securityScore ?? -1 }}"
                                    data-accessibility="{{ $row->accessibilityScore ?? -1 }}">
                                    <td>
                                        <a href="{{ $audit->url }}" target="_blank" rel="noopener noreferrer"
                                            class="text-decoration-none">
                                            {{ $audit->url }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($audit->status->value === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif ($audit->status->value === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $audit->status->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row?->seoScore !== null)
                                            <span style="color: {{ audit_score_color_var($row->seoScore) }};">
                                                {{ $row->seoScore }}
                                            </span>
                                        @else
                                            <span class="text-secondary">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row?->performanceScore !== null)
                                            <span style="color: {{ audit_score_color_var($row->performanceScore) }};">
                                                {{ $row->performanceScore }} ({{ $row->performanceGrade }})
                                            </span>
                                        @else
                                            <span class="text-secondary">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row?->securityScore !== null)
                                            <span style="color: {{ audit_score_color_var($row->securityScore) }};">
                                                {{ $row->securityScore }} ({{ $row->securityGrade }})
                                            </span>
                                        @else
                                            <span class="text-secondary">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row?->accessibilityScore !== null)
                                            <span style="color: {{ audit_score_color_var($row->accessibilityScore) }};">
                                                {{ $row->accessibilityScore }} ({{ $row->accessibilityGrade }})
                                            </span>
                                        @else
                                            <span class="text-secondary">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($audit->status->value === 'completed')
                                            <a href="{{ route('audits.show', $audit) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                View Full Report
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/bulk-audit-progress.js') }}?v={{ file_exists(public_path('js/bulk-audit-progress.js')) ? filemtime(public_path('js/bulk-audit-progress.js')) : time() }}"></script>
    <script>
        // Phase K5 — client-side column sort, no server round-trip: every
        // score/status a column could sort by is already sitting in this
        // row's own data-* attributes (see the <tr> markup above), so
        // re-ordering rows in place is enough; nothing here ever
        // re-fetches or re-renders a row's own content.
        (function () {
            'use strict';

            var table = document.getElementById('bulk-audit-results-table');
            var tbody = document.getElementById('bulk-audit-results-tbody');

            if (!table || !tbody) {
                return;
            }

            var sortState = { key: null, direction: 1 };

            table.querySelectorAll('th[data-sort-key]').forEach(function (header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function () {
                    var key = header.getAttribute('data-sort-key');

                    sortState.direction = sortState.key === key ? sortState.direction * -1 : 1;
                    sortState.key = key;

                    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

                    rows.sort(function (a, b) {
                        var aValue = a.getAttribute('data-' + key);
                        var bValue = b.getAttribute('data-' + key);

                        // url/status are text; seo/performance/security/accessibility
                        // are numeric (-1 standing in for "no score yet", which
                        // naturally sorts lowest — exactly where a still-processing
                        // or failed audit belongs when sorting by score).
                        if (key === 'url' || key === 'status') {
                            return aValue.localeCompare(bValue) * sortState.direction;
                        }

                        return (parseFloat(aValue) - parseFloat(bValue)) * sortState.direction;
                    });

                    rows.forEach(function (row) {
                        tbody.appendChild(row);
                    });
                });
            });
        })();
    </script>
@endpush