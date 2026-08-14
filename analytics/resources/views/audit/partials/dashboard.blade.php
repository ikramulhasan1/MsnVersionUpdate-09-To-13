{{--
    Main audit dashboard layout — Overall Score "certificate" section +
    category Summary Cards, built on the Bootstrap 5 grid.

    Expects:
      $audit          App\Models\Audit
      $overallScore   int|null (0-100)
      $categories     array<int, array{
                          key: string,
                          abbr: string,
                          label: string,
                          score: int|null,
                          grade: string|null,
                          summary: string,
                      }>
      $generatedAt    string, human-readable report timestamp
--}}
<div class="dashboard">

    {{-- § 01 — Overall Score --}}
    <section class="mb-4">
        <span class="report-eyebrow">01 / Overall Score</span>

        <div class="card overall-score-card" style="position: relative; overflow: hidden;">
            {{--
                Positioning/size/opacity are set as INLINE styles here,
                deliberately duplicating what .overall-score-watermark
                already declares in public/css/app.css, rather than
                relying on that class alone: an external-stylesheet
                caching/deploy timing issue (the file's mtime not
                changing on deploy, defeating the ?v= cache-busting in
                layouts/app.blade.php) meant the CSS class wasn't
                reliably applied in practice, and the SVG rendered at
                full size/opacity as a normal block element instead of a
                small absolutely-positioned watermark. Inline styles
                ship with the HTML response itself, so they take effect
                immediately regardless of whether app.css has loaded,
                finished loading, or is stuck on a stale cached copy —
                this is the robust fix; the CSS class is left in place
                only for organization/documentation, not depended on.
            --}}
            <svg class="overall-score-watermark" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true" focusable="false"
                style="position: absolute; top: -3rem; right: -3rem; width: 17rem; height: 17rem; max-width: 40%; color: var(--audit-primary); opacity: 0.04; pointer-events: none; z-index: 0;">
                <path d="M100 6 L178 34 V96 C178 140 145 176 100 194 C55 176 22 140 22 96 V34 Z" fill="currentColor" />
                <path d="M64 100 L88 126 L138 72" stroke="var(--audit-surface)" stroke-width="14" stroke-linecap="round"
                    stroke-linejoin="round" fill="none" />
            </svg>
            <div class="card-body p-4 p-lg-5" style="position: relative; z-index: 1;">
                <div class="row g-4 align-items-center">
                    <div class="col-12 col-lg-4 text-center">
                        <div class="score-gauge"
                            style="--gauge-pct: {{ $overallScore ?? 0 }}; --gauge-color: {{ audit_score_color_var($overallScore) }};"
                            role="img" aria-label="Overall score {{ $overallScore ?? 'not available' }} out of 100">
                            <div class="score-gauge-inner">
                                <span class="score-gauge-value">{{ $overallScore ?? '—' }}</span>
                                <span class="score-gauge-max">/ 100</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                            <span class="grade-seal" style="color: {{ audit_score_color_var($overallScore) }};">
                                {{ audit_score_grade_letter($overallScore) }}
                            </span>
                            <span
                                class="badge rounded-pill bg-{{ audit_score_variant($overallScore) }}-subtle text-{{ audit_score_variant($overallScore) }}-emphasis px-3 py-2">
                                {{ audit_score_label($overallScore) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            <div class="col overall-score-meta">
                                <dt>Website</dt>
                                <dd class="text-truncate" title="{{ display_host($audit->url) }}">
                                    {{ display_host($audit->url) }}</dd>
                            </div>
                            <div class="col overall-score-meta">
                                <dt>Status</dt>
                                <dd>
                                    <span class="badge {{ audit_status_badge_class($audit->status) }}">
                                        {{ audit_status_label($audit->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="col overall-score-meta">
                                <dt>Categories Audited</dt>
                                <dd class="font-mono">{{ count($categories) }}</dd>
                            </div>
                            <div class="col overall-score-meta">
                                <dt>Generated</dt>
                                <dd>{{ $generatedAt }}</dd>
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--audit-border);">

                        <div class="row row-cols-2 row-cols-sm-3 g-3">
                            @foreach ($categories as $category)
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle flex-shrink-0"
                                            style="width: 0.55rem; height: 0.55rem; background: {{ audit_score_color_var($category['score']) }};"></span>
                                        <span class="small text-truncate">{{ $category['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- § 02 — Category Breakdown --}}
    <section>
        <span class="report-eyebrow">02 / Category Breakdown</span>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            @foreach ($categories as $category)
                @php $variant = audit_score_variant($category['score']); @endphp
                <div class="col">
                    <div class="card summary-card h-100"
                        style="--gauge-color: {{ audit_score_color_var($category['score']) }};">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <span
                                        class="summary-card-icon bg-{{ $variant }}-subtle text-{{ $variant }}-emphasis">
                                        {{ $category['abbr'] }}
                                    </span>
                                    <h3 class="h6 mt-2 mb-0">{{ $category['label'] }}</h3>
                                    @if ($category['grade'])
                                        <span class="text-secondary small">Grade {{ $category['grade'] }}</span>
                                    @endif
                                </div>

                                <div class="mini-ring"
                                    style="--ring-pct: {{ $category['score'] ?? 0 }}; --gauge-color: {{ audit_score_color_var($category['score']) }};"
                                    role="img"
                                    aria-label="{{ $category['label'] }} score {{ $category['score'] ?? 'not available' }} out of 100">
                                    <span class="mini-ring-value">{{ $category['score'] ?? '—' }}</span>
                                </div>
                            </div>

                            <p class="summary-card-summary mb-0 mt-auto">{{ $category['summary'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
