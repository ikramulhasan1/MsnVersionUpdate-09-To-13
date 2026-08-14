{{--
    Dashboard components — continues the layout from dashboard.blade.php
    without modifying it. This partial adds:

      - Charts (score comparison bar chart + category radar overview, via
        Chart.js — both rendered/restyled in public/js/dashboard-charts.js)
      - Progress Bars (per-category check-status breakdown)
      - Accordion (per-category detailed checks, Bootstrap accordion)
      - Expandable Sections (per-category recommendations, Bootstrap collapse)

    Expects:
      $categories   array<int, array{
                        key: string,
                        abbr: string,
                        label: string,
                        score: int|null,
                        grade: string|null,
                        summary: string,
                        checks: array<int, array{
                            name: string,
                            status: string,
                            note: string,
                            location: array{
                                page_url: string|null,
                                dom_path: string|null,
                                affected_elements: array<int, array{url: string|null, domPath: string|null, detail: string|null}>,
                            },
                        }>,
                        recommendations: array<int, string>,
                    }>
--}}
<div class="dashboard-components mt-4">

    {{-- § 03 — Charts --}}
    <section class="mb-4">
        <span class="report-eyebrow">03 / Score Analysis</span>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card chart-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h6 mb-3">Score Comparison</h2>
                        <div class="chart-wrap">
                            <canvas id="scoreComparisonChart"
                                data-labels="{{ json_encode(array_column($categories, 'label')) }}"
                                data-scores="{{ json_encode(array_column($categories, 'score')) }}"
                                aria-label="Bar chart comparing the score of every audited category"
                                role="img"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card chart-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h6 mb-3">Category Overview</h2>
                        <div class="chart-wrap">
                            <canvas id="categoryRadarChart"
                                data-labels="{{ json_encode(array_column($categories, 'abbr')) }}"
                                data-scores="{{ json_encode(array_column($categories, 'score')) }}"
                                aria-label="Radar chart comparing every audited category at a glance"
                                role="img"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- § 04 — Checks Overview --}}
    <section class="mb-4">
        <span class="report-eyebrow">04 / Checks Overview</span>

        <div class="card chart-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h2 class="h6 mb-0">Pass / Warning / Fail by Category</h2>
                    <div class="d-flex gap-3">
                        <span class="checks-overview-legend"><span class="dot bg-success"></span> Pass</span>
                        <span class="checks-overview-legend"><span class="dot bg-warning"></span> Warning</span>
                        <span class="checks-overview-legend"><span class="dot bg-danger"></span> Fail</span>
                    </div>
                </div>

                @foreach ($categories as $category)
                    @php
                        $total = max(count($category['checks']), 1);
                        $passCount = count(
                            array_filter(
                                $category['checks'],
                                fn($c) => audit_check_variant($c['status']) === 'success',
                            ),
                        );
                        $warnCount = count(
                            array_filter(
                                $category['checks'],
                                fn($c) => audit_check_variant($c['status']) === 'warning',
                            ),
                        );
                        $failCount = count(
                            array_filter($category['checks'], fn($c) => audit_check_variant($c['status']) === 'danger'),
                        );
                    @endphp
                    <div class="checks-overview-row {{ !$loop->last ? 'mb-3' : '' }}">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-medium small">{{ $category['label'] }}</span>
                            <span class="text-secondary small font-mono">{{ $passCount }}/{{ $total }}
                                passed</span>
                        </div>
                        <div class="progress progress-thin" role="progressbar"
                            aria-label="{{ $category['label'] }} checks breakdown" aria-valuenow="{{ $passCount }}"
                            aria-valuemin="0" aria-valuemax="{{ $total }}">
                            <div class="progress-bar bg-success" style="width: {{ ($passCount / $total) * 100 }}%"
                                title="Pass: {{ $passCount }}"></div>
                            <div class="progress-bar bg-warning" style="width: {{ ($warnCount / $total) * 100 }}%"
                                title="Warning: {{ $warnCount }}"></div>
                            <div class="progress-bar bg-danger" style="width: {{ ($failCount / $total) * 100 }}%"
                                title="Fail: {{ $failCount }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- § 05 — Detailed Results --}}
    <section class="mb-4">
        <span class="report-eyebrow">05 / Detailed Results</span>

        <div class="accordion" id="categoryAccordion">
            @foreach ($categories as $category)
                @php $variant = audit_score_variant($category['score']); @endphp
                <div class="accordion-item">
                    <h3 class="accordion-header" id="heading-{{ $category['key'] }}">
                        <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $category['key'] }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="collapse-{{ $category['key'] }}">
                            <span
                                class="summary-card-icon bg-{{ $variant }}-subtle text-{{ $variant }}-emphasis me-3">
                                {{ $category['abbr'] }}
                            </span>
                            <span class="flex-grow-1">{{ $category['label'] }}</span>
                            <span
                                class="badge font-mono bg-{{ $variant }}-subtle text-{{ $variant }}-emphasis me-3">
                                {{ $category['score'] ?? '—' }}/100
                            </span>
                        </button>
                    </h3>
                    <div id="collapse-{{ $category['key'] }}"
                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                        aria-labelledby="heading-{{ $category['key'] }}" data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <p class="text-secondary">{{ $category['summary'] }}</p>

                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($category['checks'] as $check)
                                    @php
                                        $location = $check['location'] ?? null;
                                        $pageUrl = $location['page_url'] ?? null;
                                        $affectedElements = $location['affected_elements'] ?? [];
                                        $checkUid = $category['key'] . '-' . $loop->index;
                                    @endphp
                                    <li
                                        class="check-item check-item-{{ audit_check_variant($check['status']) }} list-group-item d-flex align-items-start justify-content-between gap-3 ps-3 pe-2 py-3">
                                        <div class="flex-grow-1">
                                            <p class="fw-medium mb-0">{{ $check['name'] }}</p>
                                            <p class="text-secondary small mb-0">{{ $check['note'] }}</p>

                                            {{-- Page URL this check/issue was found on, when known --}}
                                            @if (!empty($pageUrl))
                                                <p class="text-secondary small mb-0">
                                                    <a href="{{ $pageUrl }}" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="check-location-link">{{ $pageUrl }}</a>
                                                </p>
                                            @endif

                                            {{-- Affected element(s)/DOM location(s), when known — collapsed by default --}}
                                            @if (!empty($affectedElements))
                                                <button class="btn btn-link btn-sm p-0 mt-1 small text-decoration-none"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#check-location-{{ $checkUid }}"
                                                    aria-expanded="false"
                                                    aria-controls="check-location-{{ $checkUid }}">
                                                    {{ count($affectedElements) }}
                                                    affected element{{ count($affectedElements) > 1 ? 's' : '' }}
                                                </button>
                                                <div class="collapse mt-1" id="check-location-{{ $checkUid }}">
                                                    <ul class="mb-0 ps-3 check-location-list">
                                                        @foreach ($affectedElements as $element)
                                                            @php
                                                                $bits = array_filter([
                                                                    $element['domPath'] ?? null,
                                                                    $element['detail'] ?? null,
                                                                ]);
                                                            @endphp
                                                            @if (!empty($bits) || !empty($element['url']))
                                                                <li class="text-secondary small">
                                                                    @if (!empty($bits))
                                                                        {{ implode(' — ', $bits) }}
                                                                    @endif
                                                                    @if (!empty($element['url']))
                                                                        <a href="{{ $element['url'] }}" target="_blank"
                                                                            rel="noopener noreferrer">{{ $element['url'] }}</a>
                                                                    @endif
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <span
                                            class="badge check-status-pill bg-{{ audit_check_variant($check['status']) }}-subtle text-{{ audit_check_variant($check['status']) }}-emphasis text-capitalize flex-shrink-0">
                                            @php $checkVariant = audit_check_variant($check['status']); @endphp
                                            @if ($checkVariant === 'success')
                                                <svg width="11" height="11" viewBox="0 0 16 16"
                                                    fill="none" aria-hidden="true">
                                                    <path d="M13.5 4.5L6.5 12L2.5 8.2" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            @elseif ($checkVariant === 'warning')
                                                <svg width="11" height="11" viewBox="0 0 16 16"
                                                    fill="none" aria-hidden="true">
                                                    <path d="M8 1.5L15 14H1L8 1.5Z" stroke="currentColor"
                                                        stroke-width="1.4" stroke-linejoin="round" />
                                                    <path d="M8 6.25V9.75" stroke="currentColor" stroke-width="1.4"
                                                        stroke-linecap="round" />
                                                    <circle cx="8" cy="11.75" r="0.85"
                                                        fill="currentColor" />
                                                </svg>
                                            @else
                                                <svg width="11" height="11" viewBox="0 0 16 16"
                                                    fill="none" aria-hidden="true">
                                                    <path d="M3 3L13 13M13 3L3 13" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            @endif
                                            {{ $check['status'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Expandable Section: recommendations, collapsed by default --}}
                            @if (!empty($category['recommendations']))
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#recommendations-{{ $category['key'] }}" aria-expanded="false"
                                    aria-controls="recommendations-{{ $category['key'] }}">
                                    View recommendations ({{ count($category['recommendations']) }})
                                </button>
                                <div class="collapse mt-3" id="recommendations-{{ $category['key'] }}">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($category['recommendations'] as $recommendation)
                                            <li class="small mb-1">{{ $recommendation }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
