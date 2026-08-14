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
                @php
                    $qualificationVariant = audit_score_variant($prospectQualification->score);
                    $bucketMaxPoints = [
                        'website_issues' => 60,
                        'business_signals' => 25,
                        'technology_upgrade_opportunities' => 15,
                    ];
                @endphp
                <section class="mt-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h2 class="h5 mb-0">Prospect Qualification</h2>
                                <div class="d-flex align-items-center gap-3">
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->priority() }} priority
                                    </span>
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->score }}/100
                                    </span>
                                    @if ($prospectQualification->grade)
                                        <span class="grade-seal"
                                            style="color: {{ audit_score_color_var($prospectQualification->score) }};">
                                            {{ $prospectQualification->grade }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-secondary">{{ $prospectQualification->summary }}</p>

                            <ul class="list-group list-group-flush mb-0">
                                @foreach ($prospectQualification->breakdown as $bucket => $points)
                                    @php
                                        $bucketMax = $bucketMaxPoints[$bucket] ?? max($points, 1);
                                        $bucketPct = $bucketMax > 0 ? min(100, ($points / $bucketMax) * 100) : 0;
                                        $bucketLabel = ucwords(str_replace('_', ' ', $bucket));
                                    @endphp
                                    <li class="list-group-item d-flex flex-column gap-1 px-0 py-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-medium small">{{ $bucketLabel }}</span>
                                            <span class="text-secondary small font-mono">{{ $points }}
                                                / {{ $bucketMax }} pts</span>
                                        </div>
                                        <div class="progress progress-thin" role="progressbar"
                                            aria-label="{{ $bucketLabel }} points" aria-valuenow="{{ $points }}"
                                            aria-valuemin="0" aria-valuemax="{{ $bucketMax }}">
                                            <div class="progress-bar bg-{{ $qualificationVariant }}"
                                                style="width: {{ $bucketPct }}%"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            @endif

            @if ($prospectQualification)
                @php
                    $qualificationVariant = audit_score_variant($prospectQualification->score);
                    $bucketMaxPoints = [
                        'website_issues' => 60,
                        'business_signals' => 25,
                        'technology_upgrade_opportunities' => 15,
                    ];
                @endphp
                <section class="mt-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h2 class="h5 mb-0">Prospect Qualification</h2>
                                <div class="d-flex align-items-center gap-3">
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->priority() }} priority
                                    </span>
                                    <span
                                        class="badge bg-{{ $qualificationVariant }}-subtle text-{{ $qualificationVariant }}-emphasis">
                                        {{ $prospectQualification->score }}/100
                                    </span>
                                    @if ($prospectQualification->grade)
                                        <span class="grade-seal"
                                            style="color: {{ audit_score_color_var($prospectQualification->score) }};">
                                            {{ $prospectQualification->grade }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-secondary">{{ $prospectQualification->summary }}</p>

                            <ul class="list-group list-group-flush mb-0">
                                @foreach ($prospectQualification->breakdown as $bucket => $points)
                                    @php
                                        $bucketMax = $bucketMaxPoints[$bucket] ?? max($points, 1);
                                        $bucketPct = $bucketMax > 0 ? min(100, ($points / $bucketMax) * 100) : 0;
                                        $bucketLabel = ucwords(str_replace('_', ' ', $bucket));
                                    @endphp
                                    <li class="list-group-item d-flex flex-column gap-1 px-0 py-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-medium small">{{ $bucketLabel }}</span>
                                            <span class="text-secondary small font-mono">{{ $points }}
                                                / {{ $bucketMax }} pts</span>
                                        </div>
                                        <div class="progress progress-thin" role="progressbar"
                                            aria-label="{{ $bucketLabel }} points" aria-valuenow="{{ $points }}"
                                            aria-valuemin="0" aria-valuemax="{{ $bucketMax }}">
                                            <div class="progress-bar bg-{{ $qualificationVariant }}"
                                                style="width: {{ $bucketPct }}%"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            @endif

            @if ($outreachDraft)
                <section class="mt-4">
                    <div class="card outreach-card">
                        <div
                            class="outreach-card-header d-flex flex-wrap align-items-center justify-content-between gap-2 px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <h2 class="h5 mb-0">Outreach Draft</h2>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    Draft — for human review, never sent automatically
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary outreach-copy-btn"
                                data-outreach-subject="{{ $outreachDraft->subject }}"
                                data-outreach-body="{{ $outreachDraft->body }}">
                                <svg width="13" height="13" viewBox="0 0 16 16" fill="none"
                                    aria-hidden="true" class="me-1">
                                    <rect x="5.5" y="5.5" width="8.5" height="8.5" rx="1.3"
                                        stroke="currentColor" stroke-width="1.3" />
                                    <path
                                        d="M10.5 5.5V3.8A1.3 1.3 0 0 0 9.2 2.5H3.3A1.3 1.3 0 0 0 2 3.8v6a1.3 1.3 0 0 0 1.3 1.3H5.5"
                                        stroke="currentColor" stroke-width="1.3" />
                                </svg>
                                <span class="outreach-copy-label">Copy</span>
                            </button>
                        </div>

                        <div class="outreach-card-subject-row px-4 py-2">
                            <span class="outreach-card-field-label">Subject</span>
                            <span class="outreach-card-field-value">{{ $outreachDraft->subject }}</span>
                        </div>

                        <div class="card-body p-4">
                            <pre class="outreach-draft-body mb-3">{{ $outreachDraft->body }}</pre>

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
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
    @if ($outreachDraft)
        <script src="{{ asset('js/outreach-copy.js') }}"></script>
    @endif
@endpush
