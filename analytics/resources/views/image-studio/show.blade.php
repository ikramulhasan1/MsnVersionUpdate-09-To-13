{{--
    Phase S4 (Image Studio). Every operation card's HTML (both ones
    that already existed when this page loaded, and new ones a person
    creates during this visit) is rendered by ONE shared function in
    public/js/image-studio.js — this Blade template only ever embeds
    each item's existing operations as a JSON data blob
    (#item-{id}-operations-data) rather than also hand-building that
    same markup in PHP, so there is exactly one place that knows what
    an operation card looks like.
--}}
@extends('layouts.app')

@section('title', 'Image Studio')

@section('content')
    <section class="container dashboard-section">
        <p class="text-secondary small mb-1">
            <a href="{{ route('image-studio.index') }}">&larr; Image Studio</a>
        </p>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <h1 class="h4 fw-semibold mb-0">Image Studio</h1>
            <a href="{{ route('image-studio.download-zip', $job) }}" class="btn btn-sm btn-outline-secondary">Download All (ZIP)</a>
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

        {{-- Config handed to public/js/image-studio.js so client-side
             logic (Smart Resize autofill, crop ratio buttons, the
             compression live-estimate formula, responsive width
             checkboxes) never hard-codes numbers that could drift out
             of sync with config/image-processing.php's own
             'image_studio' section. --}}
        <script type="application/json" id="image-studio-config">
            {!! json_encode([
                'resizePresets' => $resizePresets,
                'thumbnailWidth' => $thumbnailWidth,
                'smartResizePresets' => $smartResizePresets,
                'cropRatios' => $cropRatios,
                'compressionModes' => $compressionModes,
                'compressionEstimateCurve' => config('image-processing.image_studio.compression_estimate_curve'),
                'formats' => $formats,
                'responsiveWidths' => $responsiveWidths,
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
        </script>

        @foreach ($items as $item)
            @php
                $recommendation = $formatRecommendations[$item->id] ?? null;
            @endphp
            <div class="card mb-4 image-studio-item"
                data-item-id="{{ $item->id }}"
                data-item-width="{{ (int) $item->width }}"
                data-item-height="{{ (int) $item->height }}"
                data-item-format="{{ $item->format }}"
                data-item-size="{{ (int) $item->file_size_bytes }}"
                data-original-preview-url="{{ route('image-studio.items.preview', [$job, $item]) }}"
                data-operations-url="{{ route('image-studio.items.operations.store', [$job, $item]) }}">
                <script type="application/json" class="item-operations-data">
                    {!! $item->operations->map(fn ($operation) => [
                        'id' => $operation->id,
                        'type' => $operation->type,
                        'status' => $operation->status->value,
                        'result' => $operation->result,
                        'error_message' => $operation->error_message,
                        'poll_url' => route('image-studio.items.operations.poll', [$job, $item, $operation]),
                        'preview_url' => route('image-studio.items.operations.preview', [$job, $item, $operation]),
                        'download_url' => route('image-studio.items.operations.download', [$job, $item, $operation]),
                    ])->values()->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
                </script>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-4">
                            <img src="{{ route('image-studio.items.preview', [$job, $item]) }}"
                                class="img-fluid rounded border original-preview-img" alt="" style="max-height: 260px; object-fit: contain; width: 100%;">
                            <p class="small text-secondary mt-2 mb-0 text-break">{{ $item->original_filename }}</p>
                            <p class="small text-secondary mb-0">{{ $item->width }}&times;{{ $item->height }} &middot; {{ $item->format }} &middot; {{ number_format($item->file_size_bytes / 1024, 0) }} KB</p>
                        </div>

                        <div class="col-12 col-md-8">
                            <ul class="nav nav-pills nav-sm mb-3 flex-wrap gap-1 studio-tabs">
                                <li class="nav-item"><button type="button" class="nav-link active studio-tab-btn" data-tab="resize">Resize</button></li>
                                <li class="nav-item"><button type="button" class="nav-link studio-tab-btn" data-tab="crop">Crop</button></li>
                                <li class="nav-item"><button type="button" class="nav-link studio-tab-btn" data-tab="compress">Compress</button></li>
                                <li class="nav-item"><button type="button" class="nav-link studio-tab-btn" data-tab="convert">Convert</button></li>
                                <li class="nav-item"><button type="button" class="nav-link studio-tab-btn" data-tab="responsive">Responsive Set</button></li>
                            </ul>

                            {{-- Resize --}}
                            <div class="studio-tab-pane" data-tab-pane="resize">
                                <div class="row g-2 align-items-end mb-2">
                                    <div class="col-4">
                                        <label class="form-label small mb-1">Width (px)</label>
                                        <input type="number" class="form-control form-control-sm resize-width" min="1" max="8000">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small mb-1">Height (px)</label>
                                        <input type="number" class="form-control form-control-sm resize-height" min="1" max="8000">
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input resize-maintain-aspect" type="checkbox" checked>
                                            <label class="form-check-label small">Keep aspect ratio</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <p class="small text-secondary mb-1">Presets</p>
                                    <div class="d-flex flex-wrap gap-1 resize-presets"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small mb-1">Smart Resize for&hellip;</label>
                                    <select class="form-select form-select-sm smart-resize-select">
                                        <option value="">&mdash; Custom dimensions above &mdash;</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-run-operation" data-type="resize">Resize Image</button>
                            </div>

                            {{-- Crop --}}
                            <div class="studio-tab-pane" data-tab-pane="crop" style="display:none;">
                                <p class="small text-secondary">Manual or center crop only &mdash; no automatic subject detection.</p>
                                <div class="mb-2">
                                    <p class="small text-secondary mb-1">Fixed ratio (center crop)</p>
                                    <div class="d-flex flex-wrap gap-1 crop-ratio-buttons"></div>
                                </div>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary crop-free-toggle">Free Crop (drag on image)</button>
                                </div>
                                <div class="crop-free-area mb-2" style="display:none;">
                                    <div class="crop-drag-wrapper position-relative d-inline-block border" style="max-width: 100%;">
                                        <img class="crop-drag-img" src="" alt="" style="max-width: 100%; display: block; user-select: none;">
                                        <div class="crop-drag-rect position-absolute border border-primary" style="display:none; background: rgba(13,110,253,.15); pointer-events: none;"></div>
                                    </div>
                                    <div class="form-text">Drag on the image to select the crop area, then click Crop Image.</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-run-operation" data-type="crop" disabled>Crop Image</button>
                            </div>

                            {{-- Compress --}}
                            <div class="studio-tab-pane" data-tab-pane="compress" style="display:none;">
                                <div class="mb-2">
                                    <p class="small text-secondary mb-1">Mode</p>
                                    <div class="d-flex flex-wrap gap-1 compression-mode-buttons"></div>
                                </div>
                                <label class="form-label small mb-1">Quality: <span class="compress-quality-value">75</span></label>
                                <input type="range" class="form-range compress-quality-slider" min="10" max="100" value="75">
                                <div class="d-flex justify-content-between small text-secondary mb-2">
                                    <span>Original: <strong class="compress-original-size"></strong></span>
                                    <span>Estimated: <strong class="compress-estimated-size"></strong></span>
                                    <span>Est. savings: <strong class="compress-estimated-savings"></strong></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-run-operation" data-type="compress">Compress Image</button>
                            </div>

                            {{-- Convert --}}
                            <div class="studio-tab-pane" data-tab-pane="convert" style="display:none;">
                                @if ($recommendation)
                                    <div class="alert alert-info small py-2 px-3">
                                        Converting to <strong>{{ $recommendation['to'] }}</strong> could save roughly <strong>{{ $recommendation['savings_percent'] }}%</strong> (estimate).
                                    </div>
                                @endif
                                <label class="form-label small mb-1">Target Format</label>
                                <select class="form-select form-select-sm convert-format-select mb-3">
                                    @foreach ($formats as $format)
                                        <option value="{{ $format }}">{{ $format }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-primary btn-run-operation" data-type="convert">Convert Image</button>
                            </div>

                            {{-- Responsive --}}
                            <div class="studio-tab-pane" data-tab-pane="responsive" style="display:none;">
                                <p class="small text-secondary mb-2">Generates a WebP version at each selected width (narrower than the original only) plus ready-to-copy srcset code.</p>
                                <div class="d-flex flex-wrap gap-3 mb-3 responsive-width-checks"></div>
                                <button type="button" class="btn btn-sm btn-primary btn-run-operation" data-type="responsive">Generate Responsive Set</button>
                            </div>

                            <div class="operations-list mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    @push('scripts')
        <script src="{{ asset('js/image-studio.js') }}"></script>
    @endpush
@endsection