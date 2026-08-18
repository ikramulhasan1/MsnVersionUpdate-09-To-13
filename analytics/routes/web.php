<?php

declare(strict_types=1);

use App\Http\Controllers\AuditController;
use App\Http\Controllers\BulkAuditController;
use App\Http\Controllers\DiscoveryController;
use App\Http\Middleware\PreventLiteSpeedCaching;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuditController::class, 'index'])->name('home');

Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');

Route::get('/audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
Route::get('/audits/{audit}/progress', [AuditController::class, 'progress'])->name('audits.progress');
Route::get('/audits/{audit}/export', [AuditController::class, 'export'])->name('audits.export');

Route::get('/audits/{audit}/export-excel', [AuditController::class, 'exportExcel'])
    ->name('audits.export.excel');

// Phase K3 (Bulk Audit) — "create" and "{bulkAuditBatch}" both need to
// sit before any wildcard segment that could otherwise swallow them,
// the same reasoning every other module in this app's own route file
// already follows (see the discovery group below for several more
// examples of the same pattern) — but this group has no OTHER
// wildcard segment at all yet, so this is really just future-proofing
// against one being added later without anyone remembering to check
// route order again.
Route::prefix('bulk-audits')->name('bulk-audits.')->group(function (): void {
    Route::get('/create', [BulkAuditController::class, 'create'])->name('create');
    Route::post('/', [BulkAuditController::class, 'store'])->name('store');

    // Phase K5 — both need to sit BEFORE /{bulkAuditBatch} for the same
    // reason every other module in this app's own route file already
    // follows (see the discovery group below for several more examples
    // of the same pattern): "progress"/"export" would otherwise be
    // swallowed as the {bulkAuditBatch} wildcard segment itself.
    Route::get('/{bulkAuditBatch}/progress', [BulkAuditController::class, 'progress'])->name('progress');
    Route::get('/{bulkAuditBatch}/export', [BulkAuditController::class, 'export'])->name('export');

    Route::get('/{bulkAuditBatch}', [BulkAuditController::class, 'show'])->name('show');
});

// PreventLiteSpeedCaching applied to the whole group, not just the
// search-panel JSON endpoints — the index page's own HTML (with its
// server-rendered filter values and cache-busted asset URLs) needs the
// exact same "never cache this" treatment, or a stale cached page can
// keep referencing an old, already-fixed JS file forever. See that
// middleware's own docblock for the production incident this fixes.
Route::prefix('discovery')->name('discovery.')->middleware(PreventLiteSpeedCaching::class)->group(function (): void {
    Route::get('/', [DiscoveryController::class, 'index'])->name('index');
    Route::post('/search', [DiscoveryController::class, 'search'])->name('search');

    // POST — no {website}-shaped wildcard conflict, but this is the
    // module's first REAL discovery action (Phase J1) — see
    // DiscoveryController::discover()'s own docblock.
    Route::post('/discover', [DiscoveryController::class, 'discover'])->name('discover');

    // JSON endpoints backing the search panel's cascading dropdowns
    // (Sub-Niche after Industry, Region/City after Country) — see
    // DiscoveryController's own docblock. Placed before /{website} so
    // "sub-niches"/"regions"/"cities" are never swallowed by that
    // catch-all uuid route segment.
    Route::get('/sub-niches', [DiscoveryController::class, 'subNiches'])->name('sub-niches');
    Route::get('/regions', [DiscoveryController::class, 'regions'])->name('regions');
    Route::get('/cities', [DiscoveryController::class, 'cities'])->name('cities');

    // Also placed before /{website} for the same reason — "compare"
    // would otherwise be swallowed as a uuid route segment (Phase E2).
    Route::get('/compare', [DiscoveryController::class, 'compare'])->name('compare');

    // Same reasoning again — "map-data" before /{website} (Phase E3).
    Route::get('/map-data', [DiscoveryController::class, 'mapData'])->name('map-data');

    // "searches" before /{website} for the same reason again (Phase
    // F3) — /discovery/searches would otherwise be swallowed as a uuid
    // route segment.
    Route::get('/searches', [DiscoveryController::class, 'searches'])->name('searches.index');
    Route::post('/searches', [DiscoveryController::class, 'storeSearch'])->name('searches.store');
    Route::delete('/searches/{search}', [DiscoveryController::class, 'destroySearch'])->name('searches.destroy');

    // Toggles is_scheduled — without this, is_scheduled could never
    // become true for any saved search, and Phase F4's whole scheduled-
    // search/new-website-detection feature would have nothing to act on.
    Route::patch('/searches/{search}/schedule', [DiscoveryController::class, 'toggleScheduledSearch'])
        ->name('searches.toggle-schedule');

    // "watchlist" before /{website} for the same reason again (Phase G1).
    Route::get('/watchlist', [DiscoveryController::class, 'watchlist'])->name('watchlist');

    // POST route — no {website}-shaped wildcard conflict, but grouped here
    // with this module's other action routes for readability (Phase H1).
    Route::post('/bulk-audit', [DiscoveryController::class, 'bulkAudit'])->name('bulk-audit');

    // "export" before /{website} for the same reason as every other
    // static segment in this group (Phase H2).
    Route::get('/export', [DiscoveryController::class, 'export'])->name('export');

    Route::get('/{website}', [DiscoveryController::class, 'show'])->name('show');
    Route::get('/{website}/watch', [DiscoveryController::class, 'watch'])->name('watch');
    Route::delete('/{website}/watch', [DiscoveryController::class, 'unwatch'])->name('unwatch');

    // Delete a discovered website outright (not just remove it from the
    // watchlist — see unwatch() above for that separate, narrower
    // action). cascadeOnDelete() on discovery_watchlist/discovery_watchlist_changes'
    // own discovered_website_id foreign keys (see those two migrations'
    // own docblocks) already handles cleaning up anything referencing
    // this row — this route only needs to delete the DiscoveredWebsite
    // itself.
    Route::delete('/{website}', [DiscoveryController::class, 'destroy'])->name('destroy');
});