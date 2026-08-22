{{--
    Phase S4 (Image Studio: Resize/Compress/Convert) — unlike Image
    SEO's own index page, there's no "context" form here: Image Studio
    doesn't generate anything from what an image is FOR, it just
    processes the pixels a person already uploaded. Upload here, then
    every resize/crop/compress/convert/responsive action happens on
    the results page per image.
--}}
@extends('layouts.app')

@section('title', 'Image Studio — Website Audit & Analysis Platform')

@section('content')
    <section class="container dashboard-section">
        <div class="mb-4">
            <p class="text-secondary small mb-1">Website Audit</p>
            <h1 class="h3 mb-0">Image Studio</h1>
            <p class="text-secondary mt-2 mb-0">
                Resize, crop, compress, convert, or generate a responsive image set — all processed locally on
                this server (Imagick/GD), nothing sent to an external API.
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
                <form method="POST" action="{{ route('image-studio.store') }}" enctype="multipart/form-data">
                    @csrf

                    <label for="images" class="form-label small fw-medium">Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
                    <div class="form-text mb-3">JPEG, PNG, WebP, or GIF. Up to {{ $maxImages }} images per batch.</div>
                    <div id="image-studio-file-list" class="small text-secondary mb-3"></div>

                    <button type="submit" class="btn btn-primary">Upload &amp; Open Studio</button>
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
                                        <a href="{{ route('image-studio.show', $recentJob) }}" class="btn btn-sm btn-outline-secondary">Open</a>
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
        <script src="{{ asset('js/image-studio.js') }}"></script>
    @endpush
@endsection