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
                    <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" onclick="window.print()">
                        Print Report
                    </button>
                    <a href="{{ route('audits.export.excel', $audit) }}"
                        class="btn btn-outline-secondary btn-sm d-print-none">
                        Download Excel Report
                    </a>
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
                <script src="{{ asset('../analytics/public/js/audit-progress.js') }}"></script>
            @endpush
        @elseif ($audit->status === \App\Audit\Enums\AuditStatus::FAILED)
            <div class="card mx-auto text-center">
                <div class="card-body py-5">
                    <h2 class="h5">This audit couldn't be completed</h2>
                    <p class="text-secondary mb-0">Something went wrong while analyzing this website.</p>
                </div>
            </div>
        @else
            @include('audit.partials.dashboard', [
                'audit' => $audit,
                'overallScore' => $overallScore,
                'categories' => $categories,
                'generatedAt' => $audit->updated_at?->format('M j, Y \a\t g:i A') ?? 'just now',
            ])

            @include('audit.partials.dashboard-components', [
                'categories' => $categories,
            ])

            {{--
                Group U: Prospect Qualification and Outreach Draft get
                their own dedicated blocks here rather than only living
                inside the generic "Lead Intelligence" category card
                above — a qualification breakdown and a draft email body
                don't fit that card's generic checks/recommendations
                shape. Each block is entirely optional: it renders only
                when that part of the pipeline has actually produced a
                result, never a fabricated placeholder.
            --}}
            @if ($prospectQualification)
                @php $qualificationVariant = audit_score_variant($prospectQualification->score); @endphp
                <section class="mt-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h2 class="h5 mb-0">Prospect Qualification</h2>
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->priority() }} priority
                                    </span>
                                    @if ($prospectQualification->grade)
                                        <span
                                            class="grade-pill bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                            {{ $prospectQualification->grade }}
                                        </span>
                                    @endif
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->score }}/100
                                    </span>
                                </div>
                            </div>

                            <p class="text-secondary">{{ $prospectQualification->summary }}</p>

                            <ul class="list-group list-group-flush mb-0">
                                @foreach ($prospectQualification->breakdown as $bucket => $points)
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <span class="fw-medium">{{ ucwords(str_replace('_', ' ', $bucket)) }}</span>
                                        <span class="text-secondary">{{ $points }} pts</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            @endif

            @if ($outreachDraft)
                <section class="mt-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h2 class="h5 mb-0">Outreach Draft</h2>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    Draft — for human review, never sent automatically
                                </span>
                            </div>

                            <p class="fw-medium mb-1">Subject</p>
                            <p class="mb-3">{{ $outreachDraft->subject }}</p>

                            <p class="fw-medium mb-1">Body</p>
                            <pre class="outreach-draft-body mb-3" style="white-space: pre-wrap; word-break: break-word;">{{ $outreachDraft->body }}</pre>

                            @if (!empty($outreachDraft->basedOnIssues))
                                <p class="text-secondary small mb-1">Based on:</p>
                                <ul class="mb-0 ps-3">
                                    @foreach ($outreachDraft->basedOnIssues as $issue)
                                        <li class="small mb-1">{{ $issue }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        @endif
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="{{ asset('../analytics/public/js/dashboard-charts.js') }}"></script>
@endpush
