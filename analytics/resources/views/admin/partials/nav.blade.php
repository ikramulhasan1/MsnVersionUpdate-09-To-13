{{--
    Phase N5 (Dynamic Pricing/Subscription) — PRODUCTION GAP CLOSED:
    when Admin Plans management was added, nothing in the UI actually
    linked to it — config/sidebar.php's own "Admin Panel" item only
    ever pointed at admin.users.index (Phase N3), and neither admin
    page linked to the other. An Admin who didn't already know the
    literal /admin/plans URL had no way to discover Plans management
    at all. This shared sub-nav (included at the top of both
    admin/users/index.blade.php and admin/plans/index.blade.php) is
    the fix — a real link to EVERY admin section from EITHER one, not
    just a link one-way; a future admin section (Phase N6's own
    payment/billing settings, say) has an obvious place to add its own
    tab here.
--}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            href="{{ route('admin.users.index') }}">
            Users
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
            href="{{ route('admin.plans.index') }}">
            Pricing Plans
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.api-providers.*') ? 'active' : '' }}"
            href="{{ route('admin.api-providers.index') }}">
            API Providers
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.api-usage.*') ? 'active' : '' }}"
            href="{{ route('admin.api-usage.index') }}">
            API Usage
        </a>
    </li>
</ul>