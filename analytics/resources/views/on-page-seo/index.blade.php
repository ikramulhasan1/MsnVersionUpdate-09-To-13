@extends('layouts.app')

@section('title', 'On-Page SEO Checker — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">On-Page SEO Checker</h1>
        <p class="text-secondary mb-4">Analyze one page's title, headings, content, images, and more.</p>

        <form method="GET" action="{{ route('on-page-seo.show') }}" class="card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="url" class="form-label">Page URL</label>
                        <input type="text" class="form-control" id="url" name="url"
                            value="{{ old('url', $url) }}" placeholder="https://example.com/page" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="target_keyword" class="form-label">
                            Target Keyword <span class="text-secondary small">(optional)</span>
                        </label>
                        <input type="text" class="form-control" id="target_keyword" name="target_keyword"
                            value="{{ old('target_keyword', $targetKeyword ?? '') }}" placeholder="e.g. running shoes">
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Check Page</button>
                    </div>
                </div>
            </div>
        </form>

        @if (isset($fetchError) && $fetchError !== null)
            <div class="alert alert-danger">
                Couldn't analyze this page: {{ $fetchError }}
            </div>
        @endif

        {{--
            PRODUCTION GAP CLOSED — see App\Models\OnPageSeoCheck's own
            migration docblock: every completed check is now saved and
            browsable here, the same "history list" every other
            analysis tool in this app already offers.
        --}}
        @if ($result === null)
            <h2 class="h6 fw-semibold mb-3">Recent Checks</h2>

            @if ($checks->isEmpty())
                <div class="card">
                    <div class="card-body p-4 text-center text-secondary">No checks yet.</div>
                </div>
            @else
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>Target Keyword</th>
                                    <th>Score</th>
                                    <th>Checked</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($checks as $check)
                                    <tr>
                                        <td class="small text-truncate" style="max-width: 320px;">{{ $check->url }}</td>
                                        <td class="small">{{ $check->target_keyword ?? '—' }}</td>
                                        <td>{{ $check->score ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $check->created_at->diffForHumans() }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('on-page-seo.show-saved', $check) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        @if ($result !== null)
            <div class="d-flex gap-2 mb-3">
                <a href="{{ route('on-page-seo.export-pdf', ['url' => $url, 'target_keyword' => $targetKeyword ?? null]) }}"
                    class="btn btn-sm btn-outline-secondary">Download PDF</a>
                <a href="{{ route('on-page-seo.export-csv', ['url' => $url, 'target_keyword' => $targetKeyword ?? null]) }}"
                    class="btn btn-sm btn-outline-secondary">Export Issues CSV</a>
            </div>

            {{-- Title --}}
            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h2 class="h6 fw-semibold mb-0">Title Tag</h2>
                        <span class="badge {{ $result->title['status'] === 'ok' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                            {{ ucfirst(str_replace('_', ' ', $result->title['status'])) }}
                        </span>
                    </div>
                    <p class="mb-1">{{ $result->title['text'] ?? '(missing)' }}</p>
                    <p class="text-secondary small mb-0">
                        {{ $result->title['length'] }} characters &middot; ~{{ $result->title['pixel_width_estimate'] }}px
                        @if ($result->title['exceeds_pixel_limit'])
                            <span class="text-danger">(likely truncated in Google search results)</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Meta Description --}}
            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h2 class="h6 fw-semibold mb-0">Meta Description</h2>
                        <span class="badge {{ $result->metaDescription['status'] === 'ok' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                            {{ ucfirst(str_replace('_', ' ', $result->metaDescription['status'])) }}
                        </span>
                    </div>
                    <p class="mb-1">{{ $result->metaDescription['text'] ?? '(missing)' }}</p>
                    <p class="text-secondary small mb-0">{{ $result->metaDescription['length'] }} characters</p>
                </div>
            </div>

            {{-- Headings --}}
            <div class="card mb-3">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-2">
                        Heading Structure
                        <span class="badge {{ $result->headings['h1_count'] === 1 ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                            {{ $result->headings['h1_count'] }} H1
                        </span>
                    </h2>
                    @if ($result->headings['skipped_level'])
                        <p class="text-warning small mb-2">Heading levels skip — hierarchy isn't sequential.</p>
                    @endif
                    <ul class="list-unstyled small mb-0">
                        @foreach ($result->headings['hierarchy'] as $heading)
                            <li style="padding-left: {{ ($heading['level'] - 1) * 16 }}px;">
                                <span class="text-secondary">H{{ $heading['level'] }}</span> — {{ $heading['text'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="row g-4 mb-3">
                {{-- Content Quality --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Content Quality</h2>
                            <p class="mb-1 small"><strong>Word count:</strong> {{ $result->content['word_count'] }}</p>
                            <p class="mb-1 small">
                                <strong>Readability:</strong> {{ $result->content['readability_score'] }}
                                ({{ $result->content['readability_label'] }})
                            </p>
                            <p class="mb-0 small">
                                <strong>Content-to-HTML ratio:</strong> {{ $result->content['content_to_html_ratio'] }}%
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Image SEO --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Image SEO</h2>
                            <p class="mb-1 small">
                                <strong>{{ $result->images['with_alt'] }}/{{ $result->images['total'] }}</strong>
                                images have alt text
                                ({{ $result->images['without_alt_percent'] }}% missing)
                            </p>
                            @if (count($result->images['oversized']) > 0)
                                <p class="text-warning small mb-1">{{ count($result->images['oversized']) }} image(s) over 200KB</p>
                            @endif
                            <p class="text-secondary small mb-0">
                                (file size checked for first {{ $result->images['size_checked_count'] }} of {{ $result->images['total'] }} images)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-3">
                {{-- Links --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Links</h2>
                            <p class="mb-0 small">
                                <strong>{{ $result->links['internal_count'] }}</strong> internal,
                                <strong>{{ $result->links['external_count'] }}</strong> external
                            </p>
                        </div>
                    </div>
                </div>

                {{-- URL / Canonical --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">URL &amp; Canonical</h2>
                            <p class="mb-1 small"><strong>Length:</strong> {{ $result->urlAnalysis['length'] }} characters</p>
                            <p class="mb-1 small">
                                <strong>Canonical:</strong>
                                {{ $result->canonical['canonical'] ?? '(none)' }}
                                @if ($result->canonical['canonical'] !== null && ! $result->canonical['is_self_referencing'])
                                    <span class="text-warning">(points elsewhere)</span>
                                @endif
                            </p>
                            <p class="mb-0 small"><strong>Robots:</strong> {{ $result->canonical['robots'] ?? '(default)' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Social Meta --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Social Meta Tags</h2>
                            <p class="mb-1 small">
                                Open Graph:
                                <span class="badge {{ $result->social['open_graph']['complete'] ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    {{ $result->social['open_graph']['complete'] ? 'Complete' : 'Incomplete' }}
                                </span>
                            </p>
                            <p class="mb-0 small">
                                Twitter Card:
                                <span class="badge {{ $result->social['twitter']['complete'] ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    {{ $result->social['twitter']['complete'] ? 'Complete' : 'Incomplete' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Structured Data --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Structured Data</h2>
                            @if (count($result->schema['types']) > 0)
                                @foreach ($result->schema['types'] as $type)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis me-1">{{ $type }}</span>
                                @endforeach
                            @else
                                <p class="text-secondary small mb-0">No structured data found.</p>
                            @endif
                            @if ($result->schema['has_errors'])
                                <p class="text-danger small mt-2 mb-0">Some JSON-LD failed to parse.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keyword Optimization --}}
            @if ($result->keywordOptimization !== null)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 fw-semibold mb-0">
                                Keyword Optimization — "{{ $result->keywordOptimization['keyword'] }}"
                            </h2>
                            <span class="h4 fw-bold mb-0">{{ $result->keywordOptimization['score'] }}/100</span>
                        </div>

                        @if (isset($keywordMetrics) && $keywordMetrics !== null)
                            <p class="small text-secondary mb-3">
                                Volume: {{ $keywordMetrics['volume'] !== null ? number_format($keywordMetrics['volume']) : '—' }}
                                &middot;
                                Difficulty: {{ $keywordMetrics['difficulty'] !== null ? $keywordMetrics['difficulty'] . '%' : '—' }}
                            </p>
                        @else
                            <p class="small text-secondary mb-3">Volume/difficulty temporarily unavailable.</p>
                        @endif

                        <div class="row g-2 small">
                            @foreach ([
                                'in_title' => 'In Title',
                                'in_h1' => 'In H1',
                                'in_first_100_words' => 'In First 100 Words',
                                'in_url' => 'In URL',
                                'in_meta_description' => 'In Meta Description',
                            ] as $key => $label)
                                <div class="col-6 col-md-4">
                                    <span class="badge {{ $result->keywordOptimization[$key] ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                        {{ $result->keywordOptimization[$key] ? '✓' : '✗' }} {{ $label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- AI Priority Fix List --}}
            @if ($aiResult !== null)
                @php
                    $issuePriority = $aiResult->recommendations['issue_priority'] ?? null;
                @endphp
                @if ($issuePriority !== null)
                    <div class="card">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-semibold mb-3">Priority Fix List</h2>
                            @forelse ($issuePriority['items'] as $item)
                                <div class="d-flex gap-3 border-bottom py-2">
                                    <span class="badge {{ match($item['severity'] ?? 'notice') {
                                        'critical' => 'bg-danger-subtle text-danger-emphasis',
                                        'warning' => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis',
                                    } }}">
                                        {{ ucfirst($item['severity'] ?? 'notice') }}
                                    </span>
                                    <div>
                                        <p class="mb-0 small">{{ $item['message'] ?? '' }}</p>
                                        @if (! empty($item['recommendation']))
                                            <p class="mb-0 small text-secondary">{{ $item['recommendation'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">No issues found — this page looks solid.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </section>
@endsection