@extends('layouts.app')

@section('title', 'My Keyword Lists — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-semibold mb-1">My Keyword Lists</h1>
                <p class="text-secondary mb-0">Keywords you've saved from Keyword Research and Keyword Magic Tool.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#new-list-modal">
                New List
            </button>
        </div>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if ($lists->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    No keyword lists yet — add keywords from
                    <a href="{{ route('keyword-research.index') }}">Keyword Research</a> or
                    <a href="{{ route('keyword-magic-tool.index') }}">Keyword Magic Tool</a> to get started.
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($lists as $list)
                    <div class="col-12 col-md-4">
                        <a href="{{ route('keyword-lists.show', $list) }}" class="text-decoration-none text-reset">
                            <div class="card h-100">
                                <div class="card-body p-4">
                                    <h2 class="h6 fw-semibold mb-1">{{ $list->name }}</h2>
                                    <p class="text-secondary small mb-0">{{ $list->items_count }} keyword(s)</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- New List modal --}}
    <div class="modal fade" id="new-list-modal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('keyword-lists.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Keyword List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="name" class="form-label">List Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Blog Ideas Q3" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection