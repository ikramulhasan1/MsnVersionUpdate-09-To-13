{{--
    Website Discovery — Phase E1 (detail page).

    A full page (not an off-canvas drawer — this route already exists
    as its own dedicated GET page, resources/views/discovery/show.blade.php
    since Phase A3, and building a second off-canvas entry point
    alongside it would mean maintaining two ways to reach the same
    content; a full page also matches App\Audit's own
    audit/result.blade.php detail-page precedent, which this file's own
    header/tab styling deliberately draws from) with six Bootstrap
    nav-tabs: Overview, SEO, Performance, Security, Technology, Business
    Intelligence.

    Every tab is honestly thinner than audit/result.blade.php's own
    accordion-based "Detailed Results" section: discovered_websites only
    ever stores AGGREGATE score/grade per category (see
    App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own docblock — it
    deliberately writes score+grade+technology stack only, never
    per-check detail), so the SEO/Performance/Security tabs each show a
    single score+grade "seal" (reusing dashboard.blade.php's own
    .grade-seal class unchanged) rather than a checklist of individual
    passes/warnings/fails the way an actual audit's own category card
    does. Each of those three tabs ends with a "Run a Full Audit" button
    (the same audits.store form pattern
    resources/views/discovery/partials/result-card.blade.php's own
    "Audit Website" button already uses) as the honest next step for
    anyone who wants that granular detail.

    Expects:
      $website     App\Models\DiscoveredWebsite
      $isWatched   bool

    Phase G3 computes the Opportunity indicator (Overview tab) and its
    full breakdown (Business Intelligence tab) via a live
    App\Discovery\Scoring\OpportunityScorer call rather than the raw
    (never-populated) DiscoveredWebsite::$opportunity_score column —
    see that scorer's own docblock for the exact SEO/Performance/
    Mobile/Accessibility/Technology-Age formula.

    Phase G4 adds a "Recommended Services" badge row (Business
    Intelligence tab) from App\Discovery\Scoring\ServiceOpportunityDetector
    — the exact six rules App\Discovery\Enums\OpportunityFilter::criterion()
    already documented back in Phase C4, finally evaluated against real
    site data. See that detector's own docblock for exactly how honest
    each of the six rules can be with the data this module currently has.
--}}
@extends('layouts.app')

@section('title', $website->business_name ?? $website->domain)

@php
    $opportunityResult = new \App\Discovery\Scoring\OpportunityScorer()->score($website);
    $serviceOpportunities = new \App\Discovery\Scoring\ServiceOpportunityDetector()->detect($website);
    $opportunityLevel = \App\Discovery\Enums\OpportunityLevel::fromScore($opportunityResult->score);
    $opportunityColor = match ($opportunityLevel) {
        \App\Discovery\Enums\OpportunityLevel::HIGH => 'var(--audit-danger)',
        \App\Discovery\Enums\OpportunityLevel::MEDIUM => 'var(--audit-warning)',
        \App\Discovery\Enums\OpportunityLevel::LOW => 'var(--audit-success)',
    };

    $technologies = collect([$website->cms, $website->framework, $website->ecommerce_platform, $website->cdn])
        ->filter()
        ->flatMap(fn(string $value): array => array_map('trim', explode(',', $value)))
        ->filter()
        ->unique()
        ->values();

    $scoreRings = [
        'SEO' => $website->seo_score,
        'Performance' => $website->performance_score,
        'Security' => $website->security_score,
        'Accessibility' => $website->accessibility_score,
    ];
@endphp

@section('content')
    <section class="result-header">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <p class="text-secondary small mb-1">Discovered Website</p>
                    <h1 class="h3 mb-0">{{ $website->business_name ?? $website->domain }}</h1>
                    @if ($website->business_name)
                        <p class="text-secondary small mb-0">{{ $website->domain }}</p>
                    @endif
                    <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none">
                        {{ $website->url }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="POST" action="{{ route('audits.store') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="url" value="{{ $website->url }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Audit Website</button>
                    </form>

                    @if ($isWatched)
                        <form method="POST" action="{{ route('discovery.unwatch', $website) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-outline-secondary btn-sm discovery-star-btn discovery-star-btn-active"
                                aria-pressed="true">
                                @include('discovery.partials.star-icon', ['filled' => true])
                                <span class="ms-1">Remove from Watchlist</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('discovery.watch', $website) }}"
                            class="btn btn-outline-secondary btn-sm discovery-star-btn" aria-pressed="false">
                            @include('discovery.partials.star-icon', ['filled' => false])
                            <span class="ms-1">Add to Watchlist</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container dashboard-section">
        <ul class="nav nav-tabs" id="discoveryDetailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="discovery-tab-overview" data-bs-toggle="tab"
                    data-bs-target="#discovery-pane-overview" type="button" role="tab"
                    aria-controls="discovery-pane-overview" aria-selected="true">
                    Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="discovery-tab-seo" data-bs-toggle="tab" data-bs-target="#discovery-pane-seo"
                    type="button" role="tab" aria-controls="discovery-pane-seo" aria-selected="false">
                    SEO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="discovery-tab-performance" data-bs-toggle="tab"
                    data-bs-target="#discovery-pane-performance" type="button" role="tab"
                    aria-controls="discovery-pane-performance" aria-selected="false">
                    Performance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="discovery-tab-security" data-bs-toggle="tab"
                    data-bs-target="#discovery-pane-security" type="button" role="tab"
                    aria-controls="discovery-pane-security" aria-selected="false">
                    Security
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="discovery-tab-technology" data-bs-toggle="tab"
                    data-bs-target="#discovery-pane-technology" type="button" role="tab"
                    aria-controls="discovery-pane-technology" aria-selected="false">
                    Technology
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="discovery-tab-bi" data-bs-toggle="tab" data-bs-target="#discovery-pane-bi"
                    type="button" role="tab" aria-controls="discovery-pane-bi" aria-selected="false">
                    Business Intelligence
                </button>
            </li>
        </ul>

        <div class="tab-content pt-4" id="discoveryDetailTabsContent">
            {{-- Overview --------------------------------------------------- --}}
            <div class="tab-pane fade show active" id="discovery-pane-overview" role="tabpanel"
                aria-labelledby="discovery-tab-overview" tabindex="0">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                            <div class="d-flex flex-wrap gap-2">
                                @if ($website->industry)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $website->industry }}
                                    </span>
                                @endif
                                @if ($website->sub_niche)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $website->sub_niche }}
                                    </span>
                                @endif
                                @if ($website->country)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $website->country }}
                                    </span>
                                @endif
                                @if ($website->region)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $website->region }}
                                    </span>
                                @endif
                                @if ($website->city)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $website->city }}
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-shrink-0"
                                title="{{ $opportunityResult->summary }}">
                                <span class="opportunity-dot" style="background-color: {{ $opportunityColor }};"></span>
                                <span class="small fw-medium">
                                    {{ $opportunityLevel->label() }} Opportunity
                                    ({{ $opportunityResult->score }}/100)
                                </span>
                            </div>
                        </div>

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

                        @if ($technologies->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($technologies as $technology)
                                    <span class="tech-pill">{{ $technology }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6">Contact Information</h2>
                                @if ($website->email || $website->phone || $website->contact_page_url)
                                    <ul class="list-unstyled mb-0">
                                        @if ($website->email)
                                            <li class="mb-2">
                                                <span class="text-secondary small d-block">Email</span>
                                                <a href="mailto:{{ $website->email }}">{{ $website->email }}</a>
                                            </li>
                                        @endif
                                        @if ($website->phone)
                                            <li class="mb-2">
                                                <span class="text-secondary small d-block">Phone</span>
                                                {{ $website->phone }}
                                            </li>
                                        @endif
                                        @if ($website->contact_page_url)
                                            <li class="mb-0">
                                                <span class="text-secondary small d-block">Contact Page</span>
                                                <a href="{{ $website->contact_page_url }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    {{ $website->contact_page_url }}
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                @else
                                    <p class="text-secondary small mb-0">No contact information detected yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <h2 class="h6">Social Media</h2>
                                @if (!empty($website->social_profiles))
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($website->social_profiles as $platform => $profileUrl)
                                            <li class="mb-2">
                                                <span class="text-secondary small d-block">
                                                    {{ ucfirst($platform) }}
                                                </span>
                                                <a href="{{ $profileUrl }}" target="_blank" rel="noopener noreferrer">
                                                    {{ $profileUrl }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-secondary small mb-0">No social profiles detected yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO / Performance / Security — aggregate score+grade only,
                 see this file's own docblock for why. --}}
            @foreach ([
            'seo' => ['label' => 'SEO', 'score' => $website->seo_score, 'grade' => $website->seo_grade],
            'performance' => ['label' => 'Performance', 'score' => $website->performance_score, 'grade' => $website->performance_grade],
        ] as $tabKey => $tabData)
                <div class="tab-pane fade" id="discovery-pane-{{ $tabKey }}" role="tabpanel"
                    aria-labelledby="discovery-tab-{{ $tabKey }}" tabindex="0">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            @if ($tabData['score'] !== null)
                                <span class="grade-seal mb-3"
                                    style="color: {{ audit_score_color_var($tabData['score']) }};">
                                    {{ $tabData['grade'] ?? audit_score_grade_letter($tabData['score']) }}
                                </span>
                                <p class="h4 mb-1">{{ $tabData['score'] }} / 100</p>
                                <p class="text-secondary small mb-4">{{ $tabData['label'] }} score</p>
                            @else
                                <p class="text-secondary mb-4">
                                    {{ $tabData['label'] }} score not available yet for this site.
                                </p>
                            @endif

                            <p class="text-secondary small mb-3">
                                This is a lightweight, homepage-only score — not the full checklist a real audit
                                produces.
                            </p>
                            <form method="POST" action="{{ route('audits.store') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="url" value="{{ $website->url }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    Run a Full Audit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Security — same score+grade seal as SEO/Performance above, plus ssl_status. --}}
            <div class="tab-pane fade" id="discovery-pane-security" role="tabpanel"
                aria-labelledby="discovery-tab-security" tabindex="0">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        @if ($website->security_score !== null)
                            <span class="grade-seal mb-3"
                                style="color: {{ audit_score_color_var($website->security_score) }};">
                                {{ $website->security_grade ?? audit_score_grade_letter($website->security_score) }}
                            </span>
                            <p class="h4 mb-1">{{ $website->security_score }} / 100</p>
                            <p class="text-secondary small mb-3">Security score</p>
                        @else
                            <p class="text-secondary mb-3">Security score not available yet for this site.</p>
                        @endif

                        @if ($website->ssl_status)
                            <p class="mb-3">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    SSL: {{ ucfirst($website->ssl_status) }}
                                </span>
                            </p>
                        @endif

                        <p class="text-secondary small mb-3">
                            This is a lightweight, homepage-only score — not the full checklist a real audit
                            produces.
                        </p>
                        <form method="POST" action="{{ route('audits.store') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="url" value="{{ $website->url }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                Run a Full Audit
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Technology --------------------------------------------------- --}}
            <div class="tab-pane fade" id="discovery-pane-technology" role="tabpanel"
                aria-labelledby="discovery-tab-technology" tabindex="0">
                <div class="card">
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">CMS</dt>
                            <dd class="col-sm-9">{{ $website->cms ?? 'Not detected yet' }}</dd>

                            <dt class="col-sm-3">Framework</dt>
                            <dd class="col-sm-9">{{ $website->framework ?? 'Not detected yet' }}</dd>

                            <dt class="col-sm-3">E-commerce Platform</dt>
                            <dd class="col-sm-9">{{ $website->ecommerce_platform ?? 'Not detected yet' }}</dd>

                            <dt class="col-sm-3">Server</dt>
                            <dd class="col-sm-9">{{ $website->server ?? 'Not detected yet' }}</dd>

                            <dt class="col-sm-3">CDN</dt>
                            <dd class="col-sm-9 mb-0">{{ $website->cdn ?? 'Not detected yet' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Business Intelligence --------------------------------------------------- --}}
            <div class="tab-pane fade" id="discovery-pane-bi" role="tabpanel" aria-labelledby="discovery-tab-bi"
                tabindex="0">
                <div class="card">
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Business Size</dt>
                            <dd class="col-sm-8">{{ $website->business_size?->label() ?? 'Unknown' }}</dd>

                            <dt class="col-sm-4">Website Type</dt>
                            <dd class="col-sm-8">{{ $website->website_type?->label() ?? 'Unknown' }}</dd>

                            <dt class="col-sm-4">Domain Age</dt>
                            <dd class="col-sm-8">
                                @if ($website->domain_age_days !== null)
                                    {{ intdiv($website->domain_age_days, 365) }} years
                                @else
                                    Unknown
                                @endif
                            </dd>

                            <dt class="col-sm-4">Est. Traffic</dt>
                            <dd class="col-sm-8">{{ $website->estimated_traffic_range ?? 'Not available yet' }}</dd>

                            <dt class="col-sm-4">Opportunity</dt>
                            <dd class="col-sm-8 d-flex align-items-center gap-2">
                                <span class="opportunity-dot" style="background-color: {{ $opportunityColor }};"></span>
                                {{ $opportunityLevel->label() }}
                                ({{ $opportunityResult->score }}/100, grade {{ $opportunityResult->grade }})
                            </dd>

                            <dt class="col-sm-4">Opportunity Breakdown</dt>
                            <dd class="col-sm-8">
                                <ul class="list-unstyled mb-0 small">
                                    <li>SEO: {{ $opportunityResult->breakdown['seo'] }} / 25 pts</li>
                                    <li>Performance: {{ $opportunityResult->breakdown['performance'] }} / 25 pts</li>
                                    <li>Mobile: {{ $opportunityResult->breakdown['mobile'] }} / 20 pts</li>
                                    <li>
                                        Accessibility: {{ $opportunityResult->breakdown['accessibility'] }} / 15 pts
                                    </li>
                                    <li>
                                        Technology Age: {{ $opportunityResult->breakdown['technology_age'] }} / 15 pts
                                    </li>
                                </ul>
                                <p class="text-secondary small mb-0 mt-1">
                                    Mobile and Technology Age currently read 0 for most sites — neither
                                    mobile_score nor last_updated_at is populated by any enrichment job yet, so
                                    those buckets have no real data to score from.
                                </p>
                            </dd>

                            <dt class="col-sm-4">Recommended Services</dt>
                            <dd class="col-sm-8">
                                @if ($serviceOpportunities === [])
                                    <span class="text-secondary small">
                                        None detected yet — either this site doesn't meet any of the six
                                        opportunity rules below its current data, or that data isn't available
                                        yet (see the note above about Mobile/Technology Age).
                                    </span>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($serviceOpportunities as $opportunity)
                                            <span class="badge bg-danger-subtle text-danger-emphasis"
                                                title="{{ $opportunity->reason }}">
                                                {{ $opportunity->serviceName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Discovery Source</dt>
                            <dd class="col-sm-8">{{ $website->discovery_source ?? 'Unknown' }}</dd>

                            <dt class="col-sm-4">Discovered</dt>
                            <dd class="col-sm-8">
                                {{ $website->discovered_at?->format('M j, Y') ?? 'Unknown' }}
                            </dd>

                            <dt class="col-sm-4">Last Updated</dt>
                            <dd class="col-sm-8 mb-0">
                                {{ $website->last_updated_at?->format('M j, Y') ?? 'Not tracked yet' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
