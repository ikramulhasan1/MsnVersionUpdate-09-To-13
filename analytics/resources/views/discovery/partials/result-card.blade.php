{{--
    Website Discovery — Phase D3 (result card).

    One discovered site rendered as a self-contained card: business
    name/domain, an Industry + Country + City badge row, detected-
    technology pills, four score mini-rings (reusing dashboard.blade.php's
    own .mini-ring class unchanged — same --ring-pct/--gauge-color CSS
    custom properties, same audit_score_color_var() helper, so a score
    ring here looks and behaves identically to one on the audit
    dashboard), an Opportunity indicator, and an action button row.

    Reuses App\Discovery\Enums\OpportunityLevel::fromScore() (built in
    Phase A2, wired into a real view for the first time here) for the
    Opportunity indicator's High/Medium/Low label — the same 60/30
    thresholds BusinessOpportunityAnalyzer already uses, so "High
    Opportunity" means the same thing here as it does in an audit
    report.

    Expects:
      $website    App\Models\DiscoveredWebsite
      $isWatched  bool — whether $website is already on the watchlist.
                  Passed in by the caller (pre-computed from an eager-
                  loaded watchlistItem relation — see
                  App\Discovery\Search\WebsiteSearchService::query())
                  rather than queried here, so rendering a list of many
                  cards never causes one watchlist lookup per card.
--}}
@php
    $displayName = $website->business_name ?? $website->domain;

    $opportunityLevel = \App\Discovery\Enums\OpportunityLevel::fromScore($website->opportunity_score);
    $opportunityColor = match ($opportunityLevel) {
        \App\Discovery\Enums\OpportunityLevel::HIGH => 'var(--audit-danger)',
        \App\Discovery\Enums\OpportunityLevel::MEDIUM => 'var(--audit-warning)',
        \App\Discovery\Enums\OpportunityLevel::LOW => 'var(--audit-success)',
    };

    // Each technology column can hold several comma-joined display
    // names (see App\Discovery\Jobs\EnrichDiscoveredWebsiteJob::technologyColumnValue()
    // — e.g. framework might be "React, Bootstrap") — split every
    // column back into individual pills rather than showing the raw
    // joined string as one long pill.
    $technologies = collect([$website->cms, $website->framework, $website->ecommerce_platform, $website->cdn])
        ->filter()
        ->flatMap(fn(string $value): array => array_map('trim', explode(',', $value)))
        ->filter()
        ->unique()
        ->values();

    $scoreRings = [
        'SEO' => $website->seo_score,
        'Perf' => $website->performance_score,
        'Sec' => $website->security_score,
        'A11y' => $website->accessibility_score,
    ];
@endphp
<div class="card result-card">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h3 class="h6 mb-1">
                    <a href="{{ route('discovery.show', $website) }}" class="text-decoration-none">
                        {{ $displayName }}
                    </a>
                </h3>
                @if ($website->business_name)
                    <p class="text-secondary small mb-2">{{ $website->domain }}</p>
                @endif

                <div class="d-flex flex-wrap gap-2">
                    @if ($website->industry)
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            {{ $website->industry }}
                        </span>
                    @endif
                    @if ($website->country)
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            {{ $website->country }}
                        </span>
                    @endif
                    @if ($website->city)
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            {{ $website->city }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-shrink-0"
                title="{{ $opportunityLevel->label() }} opportunity — {{ $website->opportunity_score !== null ? $website->opportunity_score . '/100' : 'score not available yet' }}">
                <span class="opportunity-dot" style="background-color: {{ $opportunityColor }};"></span>
                <span class="small fw-medium">{{ $opportunityLevel->label() }} Opportunity</span>
            </div>
        </div>

        @if ($technologies->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach ($technologies as $technology)
                    <span class="tech-pill">{{ $technology }}</span>
                @endforeach
            </div>
        @endif

        <div class="result-card-rings mb-3">
            @foreach ($scoreRings as $label => $score)
                <div class="text-center">
                    <div class="mini-ring"
                        style="--ring-pct: {{ $score ?? 0 }}; --gauge-color: {{ audit_score_color_var($score) }};"
                        role="img"
                        aria-label="{{ $label }} score {{ $score ?? 'not available' }} out of 100">
                        <span class="mini-ring-value">{{ $score ?? '—' }}</span>
                    </div>
                    <p class="result-card-ring-label mb-0">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 pt-3" style="border-top: 1px solid var(--audit-border);">
            <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer"
                class="btn btn-sm btn-outline-secondary">
                View Website
            </a>

            {{-- Reuses the exact same audits.store endpoint the home page's own
                 audit-submission form posts to (resources/views/home/index.blade.php) —
                 kicking off a real audit from a discovered site is the same
                 action as submitting its URL there manually. --}}
            <form method="POST" action="{{ route('audits.store') }}" class="d-inline">
                @csrf
                <input type="hidden" name="url" value="{{ $website->url }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">Audit Website</button>
            </form>

            <a href="{{ route('discovery.show', $website) }}" class="btn btn-sm btn-outline-secondary">
                View Details
            </a>

            @if ($isWatched)
                <form method="POST" action="{{ route('discovery.unwatch', $website) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="btn btn-sm btn-outline-secondary discovery-star-btn discovery-star-btn-active"
                        aria-pressed="true">
                        @include('discovery.partials.star-icon', ['filled' => true])
                        <span class="ms-1">Saved</span>
                    </button>
                </form>
            @else
                <a href="{{ route('discovery.watch', $website) }}"
                    class="btn btn-sm btn-outline-secondary discovery-star-btn" aria-pressed="false">
                    @include('discovery.partials.star-icon', ['filled' => false])
                    <span class="ms-1">Save</span>
                </a>
            @endif

            {{-- UI-only for now — no "compare selected sites" feature exists
                 yet to wire this into; included so a future comparison view
                 has a ready-made selection checkbox on every card already in
                 place. --}}
            <div class="form-check ms-auto mb-0">
                <input class="form-check-input" type="checkbox" name="compare[]" value="{{ $website->uuid }}"
                    id="discovery-compare-{{ $website->uuid }}">
                <label class="form-check-label small" for="discovery-compare-{{ $website->uuid }}">
                    Compare
                </label>
            </div>
        </div>
    </div>
</div>
