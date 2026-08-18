{{--
    Phase M1 (Discovery "View Details" → Full Audit Report tab) —
    extracted from resources/views/audit/result.blade.php's own
    "completed audit" branch, which used to build this markup inline
    and only ever rendered it on the single-audit result page. Now the
    ONE place this markup exists — result.blade.php includes it for
    its own COMPLETED case exactly as before (see that file's own
    diff), and discovery/show.blade.php's new "Full Audit Report" tab
    (DiscoveryController::show()'s own docblock) includes this SAME
    partial for whichever completed Audit it finds matching a
    DiscoveredWebsite's own URL — so a future change to how a
    completed report looks never needs to be made in two places, and
    the two pages can never silently drift out of sync with each
    other.

    Expects (identical to what result.blade.php's own COMPLETED branch
    already required before this extraction):
      $audit                  App\Models\Audit
      $categories              array<int, array{...}> — see
                               audit/partials/dashboard-components.blade.php's
                               own docblock for the exact shape
      $overallScore            int|null (0-100)
      $prospectQualification   ?App\Audit\Lead\DTO\ProspectQualificationResult
      $outreachDraft           ?App\Audit\Outreach\DTO\OutreachDraftResult
      $generatedAt             string, human-readable report timestamp
--}}
@include('audit.partials.dashboard', [
    'audit' => $audit,
    'overallScore' => $overallScore,
    'categories' => $categories,
    'generatedAt' => $generatedAt,
])

@include('audit.partials.dashboard-components', [
    'categories' => $categories,
])

{{--
    Group U: Prospect Qualification and Outreach Draft get their own
    dedicated blocks here rather than only living inside the generic
    "Lead Intelligence" category card above — a qualification breakdown
    and a draft email body don't fit that card's generic
    checks/recommendations shape. Each block is entirely optional: it
    renders only when that part of the pipeline has actually produced a
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
                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" aria-hidden="true"
                        class="me-1">
                        <rect x="5.5" y="5.5" width="8.5" height="8.5" rx="1.3" stroke="currentColor"
                            stroke-width="1.3" />
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