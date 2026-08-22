<?php

declare(strict_types=1);

/**
 * Phase N2 (Sidebar Navigation) — the ONE place this app's left
 * sidebar's own menu items are defined. resources/views/layouts/partials/sidebar.blade.php
 * loops over this array to build the actual markup — adding a future
 * feature (Phase N7's API dashboard, a future reporting module, ...)
 * to the sidebar is always "add one more entry here", never editing
 * that blade file's own HTML directly.
 *
 * Each entry:
 *   label      string, the visible menu text.
 *   icon       string, an inline SVG `<path>`/shape's own "d" attribute
 *              (a 24x24 viewBox, stroke-based icon — see the sidebar
 *              partial's own <svg> wrapper) OR a full raw SVG string
 *              starting with '<svg' for an icon that doesn't fit that
 *              simple single-path shape. Kept here rather than as
 *              separate icon files/an icon font — this app has no
 *              other icon dependency yet (every existing icon in this
 *              app's own blade files is hand-inlined SVG, see e.g.
 *              layouts/app.blade.php's own theme-toggle icons), so
 *              adding one changes nothing else.
 *   route      string, a named route this item links to.
 *   active     array<int, string>, route-name patterns (Route::is()
 *              wildcard syntax) that should mark this item "active" —
 *              usually more than just the exact route name itself
 *              (e.g. Discovery's own item should stay highlighted on
 *              its watchlist/saved-searches/compare sub-pages too).
 *   permission ?string (Phase N3) — a spatie/laravel-permission
 *              permission name this item requires; null means "no
 *              SPECIFIC permission needed", but see 'public' below —
 *              null here does NOT by itself mean "visible to a
 *              logged-out visitor".
 *   role       ?string (Phase N3) — same idea as 'permission' above,
 *              but a ROLE name instead (Admin Panel below uses this,
 *              not a permission — see
 *              database/seeders/RolesAndPermissionsSeeder's own
 *              docblock on 'view-admin-panel' for why the admin panel
 *              is gated by role, not that permission). An item can
 *              have 'permission', 'role', both, or neither.
 *   public     bool (Phase N4) — true means this item shows even to a
 *              logged-out visitor; every other item requires at least
 *              being logged in, on top of whatever 'permission'/'role'
 *              it also carries. Only 'home' below is true — this
 *              app's own genuinely public entry point (see
 *              routes/web.php's own docblock on why that ONE route
 *              stays ungated) — 'Dashboard' specifically has no
 *              permission/role of its own but still isn't public: a
 *              logged-out visitor has no dashboard to see at all. The
 *              sidebar partial hides an item entirely (not merely
 *              grays it out) for a viewer who fails any of these
 *              checks — a logged-out visitor or a permission-less
 *              Employee sees a shorter, honest sidebar rather than
 *              menu items that would just redirect/403 if clicked.
 */
return [
    'items' => [
        [
            'label' => 'Dashboard',
            'icon' => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'dashboard',
            'active' => ['dashboard'],
            'permission' => null,
            'public' => false,
        ],
        [
            'label' => 'Website Audit',
            'icon' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'home',
            'active' => ['home', 'audits.*'],
            'permission' => null,
            'public' => true,
        ],
        [
            'label' => 'Website Discovery',
            'icon' => '<path d="M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM21 21l-4.35-4.35" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'discovery.index',
            'active' => ['discovery.*'],
            'permission' => 'view-discovery',
        ],
        [
            // Phase O3 (Keyword Research) — no 'permission' key at all,
            // matching how 'Bulk Audit' below has none either: this
            // sidebar item is visible to any logged-in user, gated
            // instead by whether App\KeywordData\KeywordDataService
            // actually has an active provider configured for what the
            // page needs — see
            // resources/views/keyword-research/index.blade.php's own
            // handling of App\KeywordData\Exceptions\NoAvailableProviderException
            // for what a visitor sees if no provider is set up yet.
            'label' => 'Keyword Research',
            'icon' => '<path d="M9 3v18M3 9h18" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="9" r="6"/>',
            'route' => 'keyword-research.index',
            'active' => ['keyword-research.*'],
            'permission' => 'use-keyword-research',
        ],
        [
            // Phase O4 (Keyword Magic Tool) — same reasoning as Keyword
            // Research's own item just above: no 'permission' key,
            // gated by provider availability rather than a role/plan
            // check.
            'label' => 'Keyword Magic Tool',
            'icon' => '<path d="M15 4l-1 3-3 1 3 1 1 3 1-3 3-1-3-1-1-3zM6 14l-.7 2-2 .7 2 .7.7 2 .7-2 2-.7-2-.7-.7-2z" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'keyword-magic-tool.index',
            'active' => ['keyword-magic-tool.*'],
            'permission' => 'use-keyword-magic-tool',
        ],
        [
            // Phase O5 (Keyword List/Project Management).
            'label' => 'My Keyword Lists',
            'icon' => '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'keyword-lists.index',
            'active' => ['keyword-lists.*'],
            'permission' => 'use-keyword-lists',
        ],
        [
            // Phase Q2 (Competitor Analysis) — no 'permission' key, same
            // reasoning as Keyword Research's own item (gated by
            // provider availability, not a role/plan check).
            'label' => 'Competitor Analysis',
            'icon' => '<path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6 4a4 4 0 1 0-8 0" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'competitor-analysis.index',
            'active' => ['competitor-analysis.*'],
            'permission' => 'use-competitor-analysis',
        ],
        [
            // Phase Q3 (Backlink Analysis) — same gating reasoning as
            // Competitor Analysis's own item just above.
            'label' => 'Backlink Analysis',
            'icon' => '<path d="M13.828 10.172a4 4 0 0 0-5.656 0l-4 4a4 4 0 1 0 5.656 5.656l1.102-1.101m-.758-4.899a4 4 0 0 0 5.656 0l4-4a4 4 0 0 0-5.656-5.656l-1.1 1.1" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'backlink-analysis.index',
            'active' => ['backlink-analysis.*'],
            'permission' => 'use-backlink-analysis',
        ],
        [
            // Phase R1 (On-Page SEO Checker).
            'label' => 'On-Page SEO Checker',
            'icon' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'on-page-seo.index',
            'active' => ['on-page-seo.*'],
            'permission' => 'use-onpage-seo-checker',
        ],
        [
            // Phase R2 (Technical SEO Audit).
            'label' => 'Technical SEO Audit',
            'icon' => '<path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'technical-seo.index',
            'active' => ['technical-seo.*'],
            'permission' => 'use-technical-seo-audit',
        ],
        [
            // Phase S3 (Image SEO / Smart Metadata Generator) — placed
            // directly under Technical SEO Audit per this phase's own
            // requirement.
            'label' => 'Image SEO',
            'icon' => '<rect x="3" y="4" width="18" height="16" rx="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M21 16l-5.5-5.5a2 2 0 0 0-2.83 0L4 19" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'image-seo.index',
            'active' => ['image-seo.*'],
            'permission' => 'use-image-seo',
        ],
        [
            'label' => 'Bulk Audit',
            'icon' => '<path d="M4 6h16M4 12h16M4 18h7" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'bulk-audits.create',
            'active' => ['bulk-audits.*'],
            'permission' => 'run-bulk-audit',
        ],
        [
            'label' => 'Admin Panel',
            'icon' => '<path d="M12 2a4 4 0 0 1 4 4v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1V6a4 4 0 0 1 4-4zM9 8h6V6a3 3 0 0 0-6 0v2z" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'admin.users.index',
            'active' => ['admin.*'],
            'role' => 'Admin',
        ],
    ],
];