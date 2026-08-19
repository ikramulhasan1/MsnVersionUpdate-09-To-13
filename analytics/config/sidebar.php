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
 */
return [
    'items' => [
        [
            'label' => 'Website Audit',
            'icon' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'home',
            'active' => ['home', 'audits.*'],
        ],
        [
            'label' => 'Website Discovery',
            'icon' => '<path d="M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM21 21l-4.35-4.35" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'discovery.index',
            'active' => ['discovery.*'],
        ],
        [
            'label' => 'Bulk Audit',
            'icon' => '<path d="M4 6h16M4 12h16M4 18h7" stroke-linecap="round" stroke-linejoin="round"/>',
            'route' => 'bulk-audits.create',
            'active' => ['bulk-audits.*'],
        ],
    ],
];