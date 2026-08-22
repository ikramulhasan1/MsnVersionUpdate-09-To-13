@extends('layouts.app')

@section('title', 'Image SEO Results')

@section('content')
    <section class="container dashboard-section">
        <p class="text-secondary small mb-1">
            <a href="{{ route('image-seo.index') }}">&larr; Image SEO</a>
        </p>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <h1 class="h4 fw-semibold mb-0">Image SEO Results</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('image-seo.export', [$job, 'json']) }}" class="btn btn-sm btn-outline-secondary">Export JSON</a>
                <a href="{{ route('image-seo.export', [$job, 'csv']) }}" class="btn btn-sm btn-outline-secondary">Export CSV</a>
            </div>
        </div>

        @if (session('upload_warnings'))
            <div class="alert alert-warning small">
                <strong>Some files couldn't be processed:</strong>
                <ul class="mb-0 ps-3">
                    @foreach (session('upload_warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Image Context summary --}}
        <div class="card mb-4">
            <div class="card-body p-3 small text-secondary">
                <div class="row g-2">
                    <div class="col-6 col-md-3"><strong class="text-body">Primary Keyword:</strong> {{ $job->context['primary_keyword'] ?: '—' }}</div>
                    <div class="col-6 col-md-3"><strong class="text-body">Product:</strong> {{ $job->context['product_name'] ?: '—' }}</div>
                    <div class="col-6 col-md-3"><strong class="text-body">Brand:</strong> {{ $job->context['brand'] ?: '—' }}</div>
                    <div class="col-6 col-md-3"><strong class="text-body">Purpose:</strong> {{ $purposes[$job->context['purpose'] ?? ''] ?? '—' }}</div>
                </div>
            </div>
        </div>

        @php
            $textFields = [
                'filename' => ['label' => 'Filename', 'type' => 'input'],
                'title' => ['label' => 'Title', 'type' => 'input'],
                'caption' => ['label' => 'Caption', 'type' => 'textarea'],
                'description' => ['label' => 'Description', 'type' => 'textarea'],
            ];
        @endphp

        @foreach ($items as $item)
            @php
                $result = $item->result ?? [];
                $score = (int) data_get($result, 'checklist.score', 0);
                $scoreBadge = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                $activeStyle = data_get($result, 'alt.active_style', 'seo');
                $activeIndex = (int) data_get($result, 'alt.active_index', 0);
                $activeAlt = (string) data_get($result, "alt.styles.{$activeStyle}.candidates.{$activeIndex}.text", '');
                $activeTitle = (string) data_get($result, 'title.selected', '');
                $activeFilename = (string) data_get($result, 'filename.selected', $item->original_filename);
            @endphp
            <div class="card mb-4 image-seo-item"
                data-item-id="{{ $item->id }}"
                data-width="{{ (int) $item->width }}"
                data-height="{{ (int) $item->height }}"
                data-regenerate-url="{{ route('image-seo.items.regenerate', [$job, $item]) }}"
                data-select-url="{{ route('image-seo.items.select', [$job, $item]) }}">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-3 text-center">
                            <img src="{{ route('image-seo.items.preview', [$job, $item]) }}"
                                class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: contain;" alt="">
                            <p class="small text-secondary mb-0 text-break">{{ $item->original_filename }}</p>
                            <p class="small text-secondary mb-2">{{ $item->width }}&times;{{ $item->height }} &middot; {{ $item->format }} &middot; {{ number_format($item->file_size_bytes / 1024, 0) }} KB</p>
                            <span class="badge bg-{{ $scoreBadge }} score-badge">SEO Score: {{ $score }}/100</span>
                        </div>

                        <div class="col-12 col-md-9">
                            <div class="alert alert-warning small py-2 px-3 mb-3 duplicate-alt-warning"
                                style="{{ data_get($result, 'duplicate_alt.is_duplicate') ? '' : 'display:none;' }}">
                                <strong>Duplicate alt text detected.</strong>
                                <span class="duplicate-alt-message">{{ data_get($result, 'duplicate_alt.suggestion') }}</span>
                            </div>

                            @foreach ($textFields as $fieldKey => $cfg)
                                @php
                                    $selected = (string) data_get($result, "{$fieldKey}.selected", '');
                                    $candidate0 = data_get($result, "{$fieldKey}.candidates.0", []);
                                @endphp
                                <div class="mb-3 image-seo-field" data-field="{{ $fieldKey }}">
                                    <label class="form-label small fw-medium d-flex align-items-center gap-2 mb-1">
                                        {{ $cfg['label'] }}
                                        @if (($candidate0['source'] ?? 'generated') === 'generated')
                                            <span class="badge bg-light text-dark border generated-badge">&#10024; Generated</span>
                                        @endif
                                    </label>
                                    <div class="input-group input-group-sm">
                                        @if ($cfg['type'] === 'textarea')
                                            <textarea class="form-control field-input" rows="2" readonly>{{ $selected }}</textarea>
                                        @else
                                            <input type="text" class="form-control field-input" value="{{ $selected }}" readonly>
                                        @endif
                                        <button class="btn btn-outline-secondary btn-edit" type="button">Edit</button>
                                        <button class="btn btn-outline-secondary btn-regenerate" type="button">Regenerate</button>
                                    </div>
                                    @if (! empty($candidate0['note']))
                                        <div class="form-text text-warning-emphasis field-note">{{ $candidate0['note'] }}</div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Alt text --}}
                            <div class="mb-3 image-seo-field" data-field="alt">
                                <label class="form-label small fw-medium mb-1">Alt Text</label>
                                <div class="btn-group btn-group-sm mb-2 alt-style-switch" role="group">
                                    @foreach ($altStyles as $styleKey => $styleLabel)
                                        <button type="button"
                                            class="btn btn-outline-secondary alt-style-btn {{ $activeStyle === $styleKey ? 'active' : '' }}"
                                            data-style="{{ $styleKey }}">{{ $styleLabel }}</button>
                                    @endforeach
                                </div>

                                <div class="alt-candidates">
                                    @foreach ($altStyles as $styleKey => $styleLabel)
                                        <div class="alt-style-pane" data-style-pane="{{ $styleKey }}"
                                            style="{{ $activeStyle === $styleKey ? '' : 'display:none;' }}">
                                            @foreach (data_get($result, "alt.styles.{$styleKey}.candidates", []) as $i => $candidate)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input alt-option" type="radio"
                                                        name="alt-{{ $item->id }}-{{ $styleKey }}"
                                                        data-index="{{ $i }}"
                                                        @checked($activeStyle === $styleKey && $activeIndex === $i)>
                                                    <label class="form-check-label small">
                                                        {{ $candidate['text'] }}
                                                        @if (($candidate['source'] ?? 'generated') === 'generated')
                                                            <span class="badge bg-light text-dark border">&#10024;</span>
                                                        @endif
                                                        @if (! empty($candidate['note']))
                                                            <span class="text-warning-emphasis d-block">{{ $candidate['note'] }}</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <div class="input-group input-group-sm mt-2">
                                    <input type="text" class="form-control alt-edit-input" placeholder="Or type your own alt text&hellip;">
                                    <button class="btn btn-outline-secondary btn-alt-save" type="button">Save</button>
                                    <button class="btn btn-outline-secondary btn-regenerate" type="button">Regenerate</button>
                                </div>
                            </div>

                            {{-- Checklist --}}
                            <div class="mb-3 checklist-block">
                                <p class="small fw-medium mb-1">Image SEO Checklist</p>
                                <ul class="list-unstyled small mb-0">
                                    @foreach (data_get($result, 'checklist.criteria', []) as $criterion)
                                        @php
                                            $dot = match ($criterion['status']) {
                                                'green' => 'success',
                                                'yellow' => 'warning',
                                                default => 'danger',
                                            };
                                        @endphp
                                        <li class="d-flex align-items-start gap-2 mb-1">
                                            <span class="badge rounded-pill bg-{{ $dot }}">&nbsp;</span>
                                            <span><strong>{{ $criterion['label'] }}</strong> ({{ $criterion['points'] }}/{{ $criterion['max'] }}) &mdash; {{ $criterion['detail'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Implementation code --}}
                            <div class="img-html-block">
                                <label class="form-label small fw-medium mb-1">Implementation Code</label>
                                <div class="input-group input-group-sm">
                                    <textarea class="form-control font-monospace img-html-code" rows="2" readonly>{{ \App\Http\Controllers\ImageSeoController::imgHtml($activeFilename, $activeAlt, $activeTitle, $item->width, $item->height) }}</textarea>
                                    <button class="btn btn-outline-secondary btn-copy-html" type="button">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    @push('scripts')
        <script src="{{ asset('js/image-seo.js') }}"></script>
    @endpush
@endsection