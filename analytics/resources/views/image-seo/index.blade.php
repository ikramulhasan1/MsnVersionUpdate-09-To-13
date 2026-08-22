{{--
    Phase S3 (Image SEO / Smart Metadata Generator) — ONE form posts
    both the "Image Context" fields and the file(s) together (see
    App\Http\Controllers\ImageSeoController::store()'s own docblock for
    why generation happens synchronously, right on this same request,
    rather than behind a progress page like Technical SEO Audit uses).
--}}
@extends('layouts.app')

@section('title', 'Image SEO — Website Audit & Analysis Platform')

@section('content')
    <section class="container dashboard-section">
        <div class="mb-4">
            <p class="text-secondary small mb-1">Website Audit</p>
            <h1 class="h3 mb-0">Image SEO</h1>
            <p class="text-secondary mt-2 mb-0">
                Upload one or more images, tell us what they're for, and get SEO-friendly filenames, alt text,
                titles, captions, and descriptions — generated locally, no external AI, nothing ever forced on you.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('image-seo.store') }}" enctype="multipart/form-data">
                    @csrf

                    <h2 class="h6 fw-semibold mb-3">Image Context</h2>
                    <p class="text-secondary small mb-3">
                        Applies to every image in this batch — the more you fill in, the better the generated
                        metadata (and your Context Relevance score).
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="primary_keyword" class="form-label small fw-medium">Primary Keyword</label>
                            <input type="text" class="form-control" id="primary_keyword" name="primary_keyword"
                                value="{{ old('primary_keyword') }}" placeholder="e.g. running shoes" maxlength="255">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="secondary_keywords" class="form-label small fw-medium">Secondary Keywords</label>
                            <input type="text" class="form-control" id="secondary_keywords" name="secondary_keywords"
                                value="{{ old('secondary_keywords') }}" placeholder="comma-separated, e.g. marathon, cushioned sole" maxlength="500">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="page_topic" class="form-label small fw-medium">Page Topic</label>
                            <input type="text" class="form-control" id="page_topic" name="page_topic"
                                value="{{ old('page_topic') }}" placeholder="e.g. Best running shoes for 2026" maxlength="255">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="product_name" class="form-label small fw-medium">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name"
                                value="{{ old('product_name') }}" placeholder="e.g. Air Zoom Pegasus 41" maxlength="255">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="brand" class="form-label small fw-medium">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand"
                                value="{{ old('brand') }}" placeholder="e.g. Nike" maxlength="255">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="category" class="form-label small fw-medium">Category</label>
                            <input type="text" class="form-control" id="category" name="category"
                                value="{{ old('category') }}" placeholder="e.g. Running Shoes" maxlength="255">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="target_audience" class="form-label small fw-medium">Target Audience</label>
                            <input type="text" class="form-control" id="target_audience" name="target_audience"
                                value="{{ old('target_audience') }}" placeholder="e.g. marathon runners" maxlength="255">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="purpose" class="form-label small fw-medium">Image Purpose</label>
                            <select class="form-select" id="purpose" name="purpose">
                                <option value="">— Select —</option>
                                @foreach ($purposes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('purpose') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h2 class="h6 fw-semibold mb-2">Images</h2>
                    <div class="mb-1">
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
                        <div class="form-text">JPEG, PNG, WebP, or GIF. Up to {{ $maxImages }} images per batch.</div>
                    </div>
                    <div id="image-seo-file-list" class="small text-secondary mb-4"></div>

                    <button type="submit" class="btn btn-primary">Generate SEO Metadata</button>
                </form>
            </div>
        </div>

        <h2 class="h6 fw-semibold mb-3">Recent Batches</h2>

        @if ($jobs->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">No batches yet.</div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Images</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobs as $recentJob)
                                <tr>
                                    <td>{{ $recentJob->total_images }}</td>
                                    <td><span class="badge {{ $recentJob->status->badgeClass() }}">{{ $recentJob->status->label() }}</span></td>
                                    <td class="small text-secondary">{{ $recentJob->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('image-seo.show', $recentJob) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>

    @push('scripts')
        <script src="{{ asset('js/image-seo.js') }}"></script>
    @endpush
@endsection