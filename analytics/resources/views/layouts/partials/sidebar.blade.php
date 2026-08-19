{{--
    Phase N2 (Sidebar Navigation) — builds ENTIRELY from
    config('sidebar.items') (see that file's own docblock for the
    exact shape each item is expected to have). Adding a future
    feature to the sidebar is always "add one more entry to that
    config array" — this file itself never needs to change for that.

    Collapsible: #app-sidebar-toggle (in layouts/app.blade.php's own
    top bar) adds/removes the 'app-sidebar-collapsed' class on <body>,
    persisted in localStorage by public/js/sidebar.js so the sidebar's
    own open/closed state survives a page reload rather than resetting
    every time.
--}}
<aside class="app-sidebar" id="app-sidebar">
    <div class="app-sidebar-header">
        <a href="{{ route('home') }}" class="app-sidebar-brand text-decoration-none">
            <span class="brand-mark">AI</span>
            <span class="app-sidebar-brand-text">Website Audit</span>
        </a>
    </div>

    <nav class="app-sidebar-nav">
        @foreach (config('sidebar.items', []) as $item)
            @php
                $isActive = false;
                foreach ($item['active'] ?? [] as $pattern) {
                    if (request()->routeIs($pattern)) {
                        $isActive = true;
                        break;
                    }
                }
            @endphp
            <a href="{{ route($item['route']) }}"
                class="app-sidebar-link {{ $isActive ? 'active' : '' }}">
                <svg class="app-sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    {!! $item['icon'] !!}
                </svg>
                <span class="app-sidebar-link-text">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    @auth
        <div class="app-sidebar-footer">
            {{--
                Phase N2 — dropup (not dropdown) since this bell lives at
                the BOTTOM of a vertical sidebar; opening downward would
                push the menu off-screen. Starts empty/loading — never
                server-rendered with real notification data (that would
                mean every single page load queries notifications even
                for someone who never opens this dropdown) — filled in by
                public/js/notifications.js's own poll of GET
                notifications.recent right after this partial loads, and
                re-polled periodically after that so the unread badge
                stays current without a full page reload.
            --}}
            <div class="dropup">
                <button type="button" class="app-sidebar-link app-notification-bell w-100 text-start border-0 bg-transparent"
                    id="app-notification-bell-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg class="app-sidebar-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="app-sidebar-link-text">Notifications</span>
                    <span class="app-notification-badge d-none" id="app-notification-badge"></span>
                </button>
                <div class="dropdown-menu app-notification-dropdown" aria-labelledby="app-notification-bell-toggle">
                    <div class="app-notification-dropdown-header">
                        <span class="fw-semibold small">Notifications</span>
                        <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0 app-notification-mark-all">
                                Mark all read
                            </button>
                        </form>
                    </div>
                    <div id="app-notification-list" class="app-notification-list">
                        <p class="text-secondary small text-center py-3 mb-0">Loading&hellip;</p>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="app-notification-dropdown-footer">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>
    @endauth
</aside>