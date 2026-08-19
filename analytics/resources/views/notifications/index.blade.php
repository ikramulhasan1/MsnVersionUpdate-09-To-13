@extends('layouts.app')

@section('title', 'Notifications — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4" style="max-width: 720px;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 fw-semibold mb-0">Notifications</h1>

            @if ($notifications->getCollection()->contains(fn ($n) => $n->read_at === null))
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all read</button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    You don't have any notifications yet.
                </div>
            </div>
        @else
            <div class="card">
                <ul class="list-group list-group-flush">
                    @foreach ($notifications as $notification)
                        <li class="list-group-item d-flex align-items-start justify-content-between gap-3 p-3
                            {{ $notification->read_at === null ? 'app-notification-item unread' : '' }}">
                            <div class="flex-grow-1">
                                @if ($notification->data['url'] ?? null)
                                    <a href="{{ $notification->data['url'] }}" class="text-decoration-none d-block">
                                        <div class="fw-semibold small">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                        <div class="small text-secondary">{{ $notification->data['message'] ?? '' }}</div>
                                    </a>
                                @else
                                    <div class="fw-semibold small">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                    <div class="small text-secondary">{{ $notification->data['message'] ?? '' }}</div>
                                @endif
                                <div class="small text-secondary mt-1" style="font-size: 0.75rem;">
                                    {{ $notification->created_at?->diffForHumans() }}
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                @if ($notification->read_at === null)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Mark read</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                                    onsubmit="return confirm('Delete this notification?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
@endsection