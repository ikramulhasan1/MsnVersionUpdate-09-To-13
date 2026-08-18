
<div class="card" id="discovery-search-panel-card" data-sub-niches-url="{{ route('discovery.sub-niches') }}"
    data-regions-url="{{ route('discovery.regions') }}" data-cities-url="{{ route('discovery.cities') }}"
    data-searches-store-url="{{ route('discovery.searches.store') }}">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('discovery.search') }}" id="discovery-search-form">
            @csrf

            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-industry" class="form-label small fw-medium">Industry</label>
                    
                    <select class="form-select" id="discovery-industry" name="industry">
                        <option value="">Any industry</option>
                        @foreach ($industries as $industry)
                            @php($industryCount = $industryCounts[$industry] ?? 0)
                            <option value="{{ $industry }}" @selected(($filters['industry'] ?? '') === $industry)>
                                {{ $industry }} ({{ $industryCount }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-sub-niche" class="form-label small fw-medium">Sub-Niche</label>
                    <select class="form-select" id="discovery-sub-niche" name="sub_niche"
                        data-selected="{{ $filters['sub_niche'] ?? '' }}" @disabled(empty($filters['industry']))>
                        <option value="">
                            {{ empty($filters['industry']) ? 'Choose an industry first' : 'Any sub-niche' }}
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-country" class="form-label small fw-medium">Country</label>
                    
                    <select class="form-select" id="discovery-country" name="country">
                        <option value="">Any country</option>
                        @foreach ($countries as $country)
                            @php($countryCount = $countryCounts[$country['code']] ?? 0)
                            <option value="{{ $country['code'] }}" @selected(($filters['country'] ?? '') === $country['code'])>
                                {{ $country['name'] }} ({{ $countryCount }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-region" class="form-label small fw-medium">Region</label>
                    <select class="form-select" id="discovery-region" name="region"
                        data-selected="{{ $filters['region'] ?? '' }}" @disabled(empty($filters['country']))>
                        <option value="">
                            {{ empty($filters['country']) ? 'Choose a country first' : 'Any region' }}
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-city" class="form-label small fw-medium">City</label>
                    <select class="form-select" id="discovery-city" name="city"
                        data-selected="{{ $filters['city'] ?? '' }}" @disabled(empty($filters['country']))>
                        <option value="">
                            {{ empty($filters['country']) ? 'Choose a country first' : 'Any city' }}
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-radius" class="form-label small fw-medium">Radius (miles)</label>
                    <input type="number" class="form-control" id="discovery-radius" name="radius" min="0"
                        step="1" value="{{ $filters['radius'] ?? '' }}" placeholder="e.g. 25">
                </div>

                
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-discovered-from" class="form-label small fw-medium">
                        Discovered From
                    </label>
                    <input type="date" class="form-control" id="discovery-discovered-from" name="discovered_from"
                        value="{{ $filters['discovered_from'] ?? '' }}">
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="discovery-discovered-to" class="form-label small fw-medium">
                        Discovered To
                    </label>
                    <input type="date" class="form-control" id="discovery-discovered-to" name="discovered_to"
                        value="{{ $filters['discovered_to'] ?? '' }}">
                </div>

                <div class="col-12 col-lg-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Search</button>
                    <button type="button" class="btn btn-outline-secondary flex-shrink-0"
                        id="discovery-save-search-btn" title="Save this search">
                        Save
                    </button>
                </div>

                <div class="col-12 col-lg-3 d-flex align-items-end">
                    
                    <button type="submit" formaction="{{ route('discovery.discover') }}"
                        class="btn btn-outline-primary w-100" id="discovery-discover-btn"
                        title="Queue a search of Google Places and Yelp (and any other connected source) for new websites matching these filters">
                        Discover More
                    </button>
                </div>
            </div>

            <p class="text-secondary small mt-2 mb-0">
                "Search" looks through websites this module already knows about. "Discover More" queues a
                background check (via Google Places, Yelp, and any other connected source) for brand new
                ones matching these same filters. The page reloads right away, but it can take a minute or
                two before any new results actually appear — refresh again after a couple of minutes.
            </p>

            
            <input type="hidden" name="name" id="discovery-save-search-name">

            
            <div class="accordion mt-3" id="discovery-advanced-filters-accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="discovery-advanced-filters-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#discovery-advanced-filters-collapse" aria-expanded="false"
                            aria-controls="discovery-advanced-filters-collapse">
                            Advanced Filters
                        </button>
                    </h2>
                    <div id="discovery-advanced-filters-collapse" class="accordion-collapse collapse"
                        aria-labelledby="discovery-advanced-filters-heading">
                        <div class="accordion-body">
                            <p class="fw-medium mb-1">Boolean Query <span
                                    class="text-secondary small fw-normal">(Advanced)</span></p>
                            <p class="text-secondary small mb-2">
                                Combine terms with AND / OR / NOT — e.g.
                                <code>Restaurant AND WordPress AND NOT Facebook</code>. Evaluated left to right;
                                parentheses/grouping aren't supported yet. Use quotes for a multi-word term, e.g.
                                <code>"fine dining"</code>.
                            </p>
                            <input type="text" class="form-control mb-4" name="boolean_query"
                                id="discovery-boolean-query" value="{{ $filters['boolean_query'] ?? '' }}"
                                placeholder="e.g. Restaurant AND WordPress AND NOT Facebook" maxlength="500">

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">Website Status</p>
                                    @foreach ($websiteStatuses as $status)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="status[]"
                                                id="discovery-status-{{ $status->value }}"
                                                value="{{ $status->value }}" @checked(in_array($status->value, (array) ($filters['status'] ?? []), true))>
                                            <label class="form-check-label"
                                                for="discovery-status-{{ $status->value }}">
                                                {{ $status->label() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">Website Type</p>
                                    @foreach ($websiteTypes as $type)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="website_type[]"
                                                id="discovery-type-{{ $type->value }}" value="{{ $type->value }}"
                                                @checked(in_array($type->value, (array) ($filters['website_type'] ?? []), true))>
                                            <label class="form-check-label" for="discovery-type-{{ $type->value }}">
                                                {{ $type->label() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-3">Technology</p>
                            <div class="row g-4">
                                @foreach ($technologyGroups as $group => $options)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <p class="form-label small fw-medium mb-2">
                                            {{ match ($group) {
                                                'cms' => 'CMS',
                                                'framework' => 'Framework',
                                                'ecommerce_platform' => 'E-commerce Platform',
                                                'cdn' => 'CDN',
                                                default => ucfirst($group),
                                            } }}
                                        </p>
                                        @forelse ($options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="technology[{{ $group }}][]"
                                                    id="discovery-tech-{{ $group }}-{{ $option['slug'] }}"
                                                    value="{{ $option['slug'] }}" @checked(in_array($option['slug'], (array) ($filters['technology'][$group] ?? []), true))>
                                                <label class="form-check-label"
                                                    for="discovery-tech-{{ $group }}-{{ $option['slug'] }}">
                                                    {{ $option['name'] }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-secondary small mb-0">None detected yet.</p>
                                        @endforelse
                                    </div>
                                @endforeach

                                <div class="col-12 col-sm-6 col-lg-4">
                                    <p class="form-label small fw-medium mb-2">Server</p>
                                    @foreach ($serverSoftware as $server)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="technology[server][]"
                                                id="discovery-tech-server-{{ $server->value }}"
                                                value="{{ $server->value }}" @checked(in_array($server->value, (array) ($filters['technology']['server'] ?? []), true))>
                                            <label class="form-check-label"
                                                for="discovery-tech-server-{{ $server->value }}">
                                                {{ $server->label() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-3">Website Quality</p>
                            <div class="row g-4 mb-2">
                                
                                @php
                                    $qualityGroups = [
                                        'seo' => 'SEO',
                                        'performance' => 'Performance',
                                        'security' => 'Security',
                                        'accessibility' => 'Accessibility',
                                    ];
                                @endphp
                                @foreach ($qualityGroups as $qualityKey => $qualityName)
                                    @php
                                        $qualityMin = $filters['quality'][$qualityKey]['min'] ?? 0;
                                        $qualityMax = $filters['quality'][$qualityKey]['max'] ?? 100;
                                    @endphp
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-medium mb-0">
                                                {{ $qualityName }} Score
                                            </label>
                                            <span class="small text-secondary font-mono">
                                                <span data-range-min-label>{{ $qualityMin }}</span>&ndash;<span
                                                    data-range-max-label>{{ $qualityMax }}</span>
                                            </span>
                                        </div>
                                        <div class="discovery-range-slider" data-range-slider>
                                            <input type="range" class="discovery-range-input" min="0"
                                                max="100" step="1"
                                                name="quality[{{ $qualityKey }}][min]" value="{{ $qualityMin }}"
                                                data-range-role="min">
                                            <input type="range" class="discovery-range-input" min="0"
                                                max="100" step="1"
                                                name="quality[{{ $qualityKey }}][max]" value="{{ $qualityMax }}"
                                                data-range-role="max">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">SEO Issues</p>
                                    <div class="discovery-checkbox-scroll">
                                        @foreach ($seoIssues as $issue)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="issue[seo][]"
                                                    id="discovery-issue-seo-{{ $issue['code'] }}"
                                                    value="{{ $issue['code'] }}" @checked(in_array($issue['code'], (array) ($filters['issue']['seo'] ?? []), true))>
                                                <label class="form-check-label"
                                                    for="discovery-issue-seo-{{ $issue['code'] }}">
                                                    {{ $issue['label'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">Security Issues</p>
                                    <div class="discovery-checkbox-scroll">
                                        @foreach ($securityIssues as $issue)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="issue[security][]"
                                                    id="discovery-issue-security-{{ $issue['code'] }}"
                                                    value="{{ $issue['code'] }}" @checked(in_array($issue['code'], (array) ($filters['issue']['security'] ?? []), true))>
                                                <label class="form-check-label"
                                                    for="discovery-issue-security-{{ $issue['code'] }}">
                                                    {{ $issue['label'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-3">Opportunity</p>
                            <div class="row g-2">
                                @foreach ($opportunityFilters as $opportunity)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="opportunity[]"
                                                id="discovery-opportunity-{{ $opportunity->value }}"
                                                value="{{ $opportunity->value }}"
                                                title="{{ $opportunity->criterion() }}" @checked(in_array($opportunity->value, (array) ($filters['opportunity'] ?? []), true))>
                                            <label class="form-check-label"
                                                for="discovery-opportunity-{{ $opportunity->value }}"
                                                title="{{ $opportunity->criterion() }}">
                                                {{ $opportunity->label() }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-3">Age &amp; Size</p>
                            <div class="row g-4 mb-2">
                                @php
                                    $domainAgeMin = $filters['domain_age']['min'] ?? 0;
                                    $domainAgeMax = $filters['domain_age']['max'] ?? 20;
                                    $employeesMin = $filters['employees']['min'] ?? 0;
                                    $employeesMax = $filters['employees']['max'] ?? 500;
                                @endphp
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-medium mb-0">Domain Age (years)</label>
                                        <span class="small text-secondary font-mono">
                                            <span data-range-min-label>{{ $domainAgeMin }}</span>&ndash;<span
                                                data-range-max-label>{{ $domainAgeMax }}</span>{{ $domainAgeMax >= 20 ? '+' : '' }}
                                        </span>
                                    </div>
                                    <div class="discovery-range-slider" data-range-slider>
                                        <input type="range" class="discovery-range-input" min="0"
                                            max="20" step="1" name="domain_age[min]"
                                            value="{{ $domainAgeMin }}" data-range-role="min">
                                        <input type="range" class="discovery-range-input" min="0"
                                            max="20" step="1" name="domain_age[max]"
                                            value="{{ $domainAgeMax }}" data-range-role="max">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-medium mb-0">Employee Estimate</label>
                                        <span class="small text-secondary font-mono">
                                            <span data-range-min-label>{{ $employeesMin }}</span>&ndash;<span
                                                data-range-max-label>{{ $employeesMax }}</span>{{ $employeesMax >= 500 ? '+' : '' }}
                                        </span>
                                    </div>
                                    <div class="discovery-range-slider" data-range-slider>
                                        <input type="range" class="discovery-range-input" min="0"
                                            max="500" step="10" name="employees[min]"
                                            value="{{ $employeesMin }}" data-range-role="min">
                                        <input type="range" class="discovery-range-input" min="0"
                                            max="500" step="10" name="employees[max]"
                                            value="{{ $employeesMax }}" data-range-role="max">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="discovery-last-updated" class="form-label small fw-medium">
                                        Last Updated
                                    </label>
                                    <select class="form-select" id="discovery-last-updated" name="last_updated">
                                        <option value="">Any time</option>
                                        @foreach ($lastUpdatedRanges as $range)
                                            <option value="{{ $range->value }}" @selected(($filters['last_updated'] ?? '') === $range->value)>
                                                {{ $range->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">Business Size</p>
                                    @foreach ($businessSizes as $size)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="business_size[]"
                                                id="discovery-business-size-{{ $size->value }}"
                                                value="{{ $size->value }}" @checked(in_array($size->value, (array) ($filters['business_size'] ?? []), true))>
                                            <label class="form-check-label"
                                                for="discovery-business-size-{{ $size->value }}">
                                                {{ $size->label() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-12 col-md-6">
                                    <p class="form-label small fw-medium mb-2">Est. Traffic</p>
                                    @foreach ($trafficRanges as $traffic)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="traffic[]"
                                                id="discovery-traffic-{{ $traffic->value }}"
                                                value="{{ $traffic->value }}" @checked(in_array($traffic->value, (array) ($filters['traffic'] ?? []), true))>
                                            <label class="form-check-label"
                                                for="discovery-traffic-{{ $traffic->value }}">
                                                {{ $traffic->label() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-3">Social Media</p>
                            <div class="row g-3">
                                @foreach ($socialPlatforms as $platform)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="discovery-social-{{ $platform->value }}"
                                            class="form-label small fw-medium mb-1">
                                            {{ $platform->label() }}
                                        </label>
                                        <select class="form-select form-select-sm"
                                            id="discovery-social-{{ $platform->value }}"
                                            name="social[{{ $platform->value }}]">
                                            <option value="" @selected(($filters['social'][$platform->value] ?? '') === '')>
                                                Any
                                            </option>
                                            <option value="has" @selected(($filters['social'][$platform->value] ?? '') === 'has')>
                                                Has {{ $platform->label() }}
                                            </option>
                                            <option value="missing" @selected(($filters['social'][$platform->value] ?? '') === 'missing')>
                                                Doesn't Have {{ $platform->label() }}
                                            </option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-4" style="border-color: var(--audit-border);">

                            <p class="fw-medium mb-1">Contact Availability</p>
                            <p class="text-secondary small mb-3">
                                Narrows the results below, like most of the Advanced Filters groups above (see
                                the note at the bottom of this panel for the ones that don't yet).
                            </p>
                            <div class="row g-2">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_availability"
                                            id="discovery-contact-any" value="" @checked(empty($filters['contact_availability']))>
                                        <label class="form-check-label" for="discovery-contact-any">Any</label>
                                    </div>
                                </div>
                                @foreach ($contactAvailabilityOptions as $option)
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="contact_availability"
                                                id="discovery-contact-{{ $option->value }}"
                                                value="{{ $option->value }}" @checked(($filters['contact_availability'] ?? '') === $option->value)>
                                            <label class="form-check-label"
                                                for="discovery-contact-{{ $option->value }}">
                                                {{ $option->label() }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-secondary small mb-0 mt-3">
                Industry/Sub-Niche, Location, Website Type, Business Size, Technology, Website Quality score
                ranges, Domain Age, Last Updated, Est. Traffic, Social Media, Contact Availability, and the
                Boolean Query field all narrow the results below. Website Status, Opportunity, SEO/Security
                specific issues, Employee Estimate, and Radius are still UI-only for now — they submit and
                round-trip, but don't filter yet.
            </p>
        </form>
    </div>
</div>
