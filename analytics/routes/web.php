<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ApiProviderController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BulkAuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscoveryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Payments\CheckoutController;
use App\Http\Controllers\Payments\SslCommerzController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\PreventLiteSpeedCaching;
use Illuminate\Support\Facades\Route;

/**
 * Phase N1 (Authentication Foundation) — every route below EXCEPT
 * 'home' now requires a real, logged-in AND email-verified session
 * (the 'auth' + 'verified' middleware group). 'home' itself stays
 * public deliberately — see this file's own earlier docblock history
 * (Phase N1.5, Homepage + Quick Audit Hero, not yet built) for why.
 *
 * Phase N3 (Role & Permission System) layers a PERMISSION check on top
 * of that auth requirement for every real feature route — see
 * database/seeders/RolesAndPermissionsSeeder's own docblock for
 * exactly what each of the 5 permission names below means and which
 * role(s) get it by default. A logged-in, verified user who lacks the
 * relevant permission (e.g. an Employee an Admin hasn't granted
 * run-bulk-audit to) gets a real 403 Forbidden here, via spatie/
 * laravel-permission's own 'permission' middleware alias (registered
 * in bootstrap/app.php — see that file's own docblock for why that
 * registration had to be explicit on this Laravel version) — never a
 * silent redirect or a broken page.
 */
Route::get('/', [AuditController::class, 'index'])->name('home');

// Phase N1.5 (Homepage + Quick Audit Hero) — see
// App\Http\Controllers\AuditController::quickAudit()'s own docblock.
// Public and unauthenticated, deliberately: this is this app's SECOND
// (and only other) genuinely public route besides 'home' itself.
Route::post('/quick-audit', [AuditController::class, 'quickAudit'])->name('quick-audit');

Route::middleware(['auth', 'verified'])->group(function (): void {
    // Phase N4 (User Dashboard) — no permission gate, unlike every
    // feature group below: every logged-in, verified account sees
    // THEIR OWN dashboard regardless of role/permissions (an Employee
    // with zero feature permissions granted yet still has an account
    // and should still be able to land somewhere real after logging
    // in, rather than a dashboard route that 403s for them specifically).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:run-audit')->group(function (): void {
        Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
        Route::get('/audits/{audit}/progress', [AuditController::class, 'progress'])->name('audits.progress');
    });

    // Phase N3 — split out from the run-audit group above: exporting a
    // report is a separately-toggleable capability (see
    // RolesAndPermissionsSeeder's own docblock on 'export-data' for
    // why this ONE permission covers both Audit's and Discovery's own
    // export surfaces, not two module-specific ones).
    Route::middleware(['permission:export-data', 'plan:export-data'])->group(function (): void {
        Route::get('/audits/{audit}/export', [AuditController::class, 'export'])->name('audits.export');
        Route::get('/audits/{audit}/export-excel', [AuditController::class, 'exportExcel'])
            ->name('audits.export.excel');
    });

    // Phase K3 (Bulk Audit) — "create" and "{bulkAuditBatch}" both need to
    // sit before any wildcard segment that could otherwise swallow them,
    // the same reasoning every other module in this app's own route file
    // already follows (see the discovery group below for several more
    // examples of the same pattern) — but this group has no OTHER
    // wildcard segment at all yet, so this is really just future-proofing
    // against one being added later without anyone remembering to check
    // route order again.
    Route::prefix('bulk-audits')->name('bulk-audits.')->middleware(['permission:run-bulk-audit', 'plan:run-bulk-audit'])
        ->group(function (): void {
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
    //
    // Phase N3 — 'permission:view-discovery' gates the WHOLE group;
    // "/export" additionally requires 'permission:export-data' (stacked
    // on top of view-discovery, not instead of it — exporting Discovery
    // data without being able to view Discovery at all wouldn't make
    // sense) via its own extra ->middleware() call below, rather than
    // being pulled out into a separate top-level group the way Audit's
    // own export routes were: Discovery's "export" still needs to sit
    // in THIS exact position in the route list (before /{website})
    // for the route-ordering reason this group's own comments already
    // explain throughout.
    //
    // PRODUCTION INCIDENT CLOSED — 'plan:view-discovery' added: until
    // now, this whole group was gated ONLY by role permission, never
    // by the person's actual Pricing Plan — see
    // database/migrations/2026_08_20_000001_backfill_view_discovery_plan_feature.php's
    // own docblock for the full "why" and the required backfill that
    // migration performs before this middleware can safely go live
    // (deploy that migration FIRST, or every existing customer loses
    // Discovery access the moment this line does).
    Route::prefix('discovery')->name('discovery.')
        ->middleware([PreventLiteSpeedCaching::class, 'permission:view-discovery', 'plan:view-discovery'])
        ->group(function (): void {
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
            // static segment in this group (Phase H2). See this whole group's
            // own middleware comment above for why 'export-data' is stacked
            // on ONLY this one route rather than applied group-wide.
            Route::get('/export', [DiscoveryController::class, 'export'])->name('export')
                ->middleware(['permission:export-data', 'plan:export-data']);

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

    // Phase N2 (Sidebar + Dynamic Notification System) — "recent" (the
    // bell dropdown's own polling endpoint) and "read-all" both need to
    // sit before /{notification} for the same "static segment before
    // wildcard" reasoning this file's other route groups already
    // follow throughout (see the discovery group above for several
    // more examples). No permission gate — every logged-in, verified
    // user can see/manage their OWN notifications regardless of role;
    // App\Http\Controllers\NotificationController's own docblock is
    // what actually keeps this scoped to auth()->user() only.
    Route::prefix('notifications')->name('notifications.')->group(function (): void {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Phase N3 — role:Admin, not a permission check: the admin panel
    // is an all-or-nothing surface reachable only by this one specific
    // role, unlike every feature group above (which an Employee can be
    // granted piecemeal access to). See
    // database/seeders/RolesAndPermissionsSeeder's own docblock on
    // 'view-admin-panel' for why that permission exists but isn't what
    // actually gates this group.
    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function (): void {
        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            // Phase N5 — a separate route/method from update() above,
            // matching admin/users/edit.blade.php's own separate <form>
            // for plan assignment (see
            // App\Http\Controllers\Admin\UserManagementController::updatePlan()'s
            // own docblock for why the two are independent).
            Route::put('/{user}/plan', [UserManagementController::class, 'updatePlan'])->name('update-plan');
        });

        // Phase N5 (Dynamic Pricing/Subscription) — "create"/"{plan}"
        // ordering doesn't actually matter here (Plan's own route
        // model binding is numeric-id-based, not a string that could
        // collide with the literal word "create" the way a uuid-typed
        // binding could — see the discovery group above for where that
        // DOES matter), but ordered this way anyway for readability,
        // matching this file's own convention throughout.
        Route::prefix('plans')->name('plans.')->group(function (): void {
            Route::get('/', [PlanController::class, 'index'])->name('index');
            Route::get('/create', [PlanController::class, 'create'])->name('create');
            Route::post('/', [PlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [PlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}', [PlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [PlanController::class, 'destroy'])->name('destroy');
        });

        // Phase O1 (API Provider Management System) — the foundation
        // for Keyword Research/Keyword Magic Tool (Phase O3/O4). "test"
        // and "toggle" both sit before /{apiProvider}/edit for the same
        // "static segment before wildcard" reasoning this file's other
        // route groups already follow throughout.
        Route::prefix('api-providers')->name('api-providers.')->group(function (): void {
            Route::get('/', [ApiProviderController::class, 'index'])->name('index');
            Route::get('/create', [ApiProviderController::class, 'create'])->name('create');
            Route::post('/', [ApiProviderController::class, 'store'])->name('store');
            Route::get('/{apiProvider}/edit', [ApiProviderController::class, 'edit'])->name('edit');
            Route::put('/{apiProvider}', [ApiProviderController::class, 'update'])->name('update');
            Route::delete('/{apiProvider}', [ApiProviderController::class, 'destroy'])->name('destroy');
            Route::post('/{apiProvider}/toggle', [ApiProviderController::class, 'toggleActive'])->name('toggle');
            Route::post('/{apiProvider}/test', [ApiProviderController::class, 'testConnection'])->name('test');
        });
    });

    // Phase N5 (Dynamic Pricing/Subscription) — no permission/plan gate
    // of its own: every logged-in, verified user (regardless of role
    // or current plan) can see available plans and request an upgrade,
    // including someone whose trial has fully expired — that's
    // arguably the person MOST likely to want this page.
    Route::prefix('subscription')->name('subscription.')->group(function (): void {
        Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
        Route::post('/upgrade', [SubscriptionController::class, 'requestUpgrade'])->name('request-upgrade');

        // Phase N6 (Multiple Payment Methods) — the real checkout flow
        // Phase N5's own "request an upgrade" above deliberately
        // deferred to this phase.
        Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])->name('checkout');
        Route::post('/checkout/{plan}', [CheckoutController::class, 'start'])->name('checkout.start');
    });

    // Phase N6 — Billing History (this phase's own explicit
    // requirement) plus the Stripe success-redirect fallback (see
    // App\Http\Controllers\BillingController::stripeSuccess()'s own
    // docblock for why this exists alongside the real webhook).
    Route::prefix('billing')->name('billing.')->group(function (): void {
        Route::get('/', [BillingController::class, 'history'])->name('history');
        Route::get('/stripe/success', [BillingController::class, 'stripeSuccess'])->name('stripe.success');
    });
});

// Phase N6 — payment gateway callbacks, deliberately OUTSIDE the
// auth+verified group above: SSLCommerz's own redirect/IPN requests
// come from the person's browser (which may have lost its session by
// the time they return, e.g. a long detour through a banking app) or
// directly from SSLCommerz's own servers (no user session at all,
// ever) — neither could pass an 'auth' check. Each of these routes
// independently confirms the real payment via
// App\Payments\SslCommerzGateway::validateTransaction() before doing
// anything, rather than relying on the request being authenticated at
// all — see App\Http\Controllers\Payments\SslCommerzController's own
// docblock.
Route::prefix('billing/sslcommerz')->name('billing.sslcommerz.')->group(function (): void {
    Route::post('/success', [SslCommerzController::class, 'success'])->name('success');
    Route::post('/fail', [SslCommerzController::class, 'fail'])->name('fail');
    Route::post('/cancel', [SslCommerzController::class, 'cancel'])->name('cancel');
    Route::post('/ipn', [SslCommerzController::class, 'ipn'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('ipn');
});

// Phase N6 — PRODUCTION GAP CLOSED: laravel/cashier auto-registers its
// OWN webhook route (at config('cashier.path').'/webhook', i.e.
// /stripe/webhook) pointing at Cashier's OWN base
// Laravel\Cashier\Http\Controllers\WebhookController — NOT at
// App\Http\Controllers\Payments\StripeWebhookController, this app's
// own subclass that actually implements handleCheckoutSessionCompleted().
// Left alone, Stripe's real webhook deliveries would hit Cashier's
// generic base controller, which has no idea what
// checkout.session.completed should DO for this app — every payment
// would silently never activate a plan. Cashier::ignoreRoutes()
// (called from App\Providers\AppServiceProvider::boot() — see that
// file's own comment) disables Cashier's own auto-registration
// entirely, and THIS route below — pointing explicitly at this app's
// own controller — is what replaces it. VerifyCsrfToken is removed the
// same way the SSLCommerz IPN route's is above: Stripe's own webhook
// POST carries no CSRF token at all (it's server-to-server, not a
// browser form submission), and Cashier's own base controller
// verifies the request via the Stripe-Signature header instead — a
// strictly stronger check than CSRF ever was for this kind of request.
Route::post('stripe/webhook', [\App\Http\Controllers\Payments\StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('cashier.webhook');

require __DIR__.'/auth.php';