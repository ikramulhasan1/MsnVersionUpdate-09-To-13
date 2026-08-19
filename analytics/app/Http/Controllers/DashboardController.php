<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\DiscoveryWatchlistItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase N4 (User Dashboard) — every query below is explicitly scoped
 * to $request->user()->id (never a bare Audit::query()/
 * DiscoveryWatchlistItem::query() with no where('user_id', ...)), the
 * one hard requirement this whole phase was built around: this page
 * must NEVER show another person's own audits or watchlist, even by
 * accident. auth()->id() would work identically here (this controller
 * only ever runs behind the 'auth' middleware), but $request->user()
 * is used throughout for the same reason the rest of this app's
 * newer controllers prefer it — it's the request-bound instance
 * Laravel already resolved, not a second global-facade lookup.
 */
final class DashboardController extends Controller
{
    private const int RECENT_AUDITS_LIMIT = 5;

    private const int RECENT_NOTIFICATIONS_LIMIT = 5;

    public function index(Request $request): View
    {
        $user = $request->user();

        $recentAudits = Audit::query()
            ->where('user_id', $user->id)
            ->whereNull('bulk_audit_batch_id')
            ->latest()
            ->limit(self::RECENT_AUDITS_LIMIT)
            ->get();

        $auditCount = Audit::query()->where('user_id', $user->id)->count();
        $watchlistCount = DiscoveryWatchlistItem::query()->where('user_id', $user->id)->count();

        $recentNotifications = $user->notifications()->latest()->limit(self::RECENT_NOTIFICATIONS_LIMIT)->get();

        return view('dashboard.index', [
            'recentAudits' => $recentAudits,
            'auditCount' => $auditCount,
            'watchlistCount' => $watchlistCount,
            'recentNotifications' => $recentNotifications,
            'unreadNotificationCount' => $user->unreadNotifications()->count(),
            // Phase N4 — a deliberate placeholder. Phase N5 (Dynamic
            // Pricing/Subscription) is where this becomes a real
            // relation read off a genuine subscriptions table; nothing
            // in resources/views/dashboard/index.blade.php's own
            // Subscription card needs to change when that lands, since
            // this array already carries the exact shape a real plan
            // record will (name/status/renewsAt) — only this literal
            // array gets replaced with a real query.
            'subscription' => [
                'plan' => 'Not subscribed yet',
                'status' => 'placeholder',
                'renewsAt' => null,
            ],
        ]);
    }
}