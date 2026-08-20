@extends('layouts.app')

@section('title', $list->name . ' — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <p class="text-secondary small mb-1">
            <a href="{{ route('keyword-lists.index') }}">&larr; My Keyword Lists</a>
        </p>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 fw-semibold mb-0">{{ $list->name }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('keyword-lists.export', $list) }}" class="btn btn-outline-secondary btn-sm">
                    Export CSV
                </a>
                <form method="POST" action="{{ route('keyword-lists.destroy', $list) }}"
                    onsubmit="return confirm('Delete this entire list?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete List</button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        {{--
            Phase O5 — see this list's own migration docblock
            (database/migrations/2026_08_22_000002_create_keyword_list_items_table.php):
            every number below is a SNAPSHOT from when the keyword was
            added, never silently refreshed — stated plainly here so
            nobody mistakes a saved list for a live lookup.
        --}}
        <p class="text-secondary small mb-3">
            Volume/KD/CPC below are snapshots from when each keyword was added — not live data.
        </p>

        @if ($items->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    No keywords in this list yet.
                </div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Volume</th>
                                <th>KD%</th>
                                <th>CPC</th>
                                <th>Added</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->keyword }}</td>
                                    <td>{{ $item->volume !== null ? number_format($item->volume) : '—' }}</td>
                                    <td>{{ $item->difficulty !== null ? $item->difficulty . '%' : '—' }}</td>
                                    <td>{{ $item->cpc !== null ? '$' . number_format($item->cpc, 2) : '—' }}</td>
                                    <td class="small text-secondary">{{ $item->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('keyword-lists.remove-item', [$list, $item]) }}"
                                            onsubmit="return confirm('Remove this keyword?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection