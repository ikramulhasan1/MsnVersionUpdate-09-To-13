<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\DiscoveryWatchlistItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase N4 (User Dashboard) — every query below is explicitly scoped
 * to $request->user()->id OR a genuinely ownerless row (never another
 * REAL person's own data), the one hard requirement this whole phase
 * was built around: this page must NEVER show a DIFFERENT person's
 * own audits or watchlist, even for an Admin. auth()->id() would work
 * identically here (this controller only ever runs behind the 'auth'
 * middleware), but $request->user() is used throughout for the same
 * reason the rest of this app's newer controllers prefer it — it's
 * the request-bound instance Laravel already resolved, not a second
 * global-facade lookup.
 *
 * PRODUCTION GAP CLOSED, THEN NARROWED — read before widening the
 * isAdmin() branch below back to "every row, any owner" again: every
 * Audit row created before Phase N2 added the user_id column has
 * user_id = NULL — genuinely orphaned legacy data, not owned by
 * anyone. The FIRST fix for that gap widened an Admin's own scope to
 * literally everything (every row regardless of owner) — but that
 * showed OTHER real users' own private data to an Admin too, which
 * wasn't the actual ask: an Admin wanted their OWN pre-multi-user
 * data back, not a cross-account overview. The scope below is
 * "$user->id OR NULL" specifically — an Admin's own rows plus
 * ownerless legacy rows (which functionally WERE this Admin's own
 * data, from before per-account ownership existed at all) — never a
 * different real user's own row. A genuine cross-account admin
 * overview, if ever needed later, belongs on its own dedicated
 * admin-only page (like /admin/users already is), not folded into
 * this personal dashboard.
 */
final class DashboardController extends Controller
{
    private const int RECENT_AUDITS_LIMIT = 5;

    private const int RECENT_NOTIFICATIONS_LIMIT = 5;

    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        $recentAudits = Audit::query()
            ->where(function ($query) use ($user, $isAdmin): void {
                $query->where('user_id', $user->id);

                if ($isAdmin) {
                    $query->orWhereNull('user_id');
                }
            })
            ->whereNull('bulk_audit_batch_id')
            ->latest()
            ->limit(self::RECENT_AUDITS_LIMIT)
            ->get();

        $auditCount = Audit::query()
            ->where(function ($query) use ($user, $isAdmin): void {
                $query->where('user_id', $user->id);

                if ($isAdmin) {
                    $query->orWhereNull('user_id');
                }
            })
            ->count();

        $watchlistCount = DiscoveryWatchlistItem::query()
            ->where(function ($query) use ($user, $isAdmin): void {
                $query->where('user_id', $user->id);

                if ($isAdmin) {
                    $query->orWhereNull('user_id');
                }
            })
            ->count();

        $recentNotifications = $user->notifications()->latest()->limit(self::RECENT_NOTIFICATIONS_LIMIT)->get();

        return view('dashboard.index', [
            'recentAudits' => $recentAudits,
            'auditCount' => $auditCount,
            'watchlistCount' => $watchlistCount,
            'recentNotifications' => $recentNotifications,
            'unreadNotificationCount' => $user->unreadNotifications()->count(),
            // Phase N1.5 (Free Trial) — real data now, no longer a
            // placeholder: $user->plan/onTrial()/trialExpired() are
            // all real, queried values (see App\Models\User's own
            // docblock on each). Phase N5 (Dynamic Pricing/
            // Subscription) is where a PAID, non-trial subscription's
            // own richer status (renewal date, payment method, ...)
            // gets added — this same $user->plan relation is what that
            // phase builds on, not a replacement for it.
            'plan' => $user->plan,
            'onTrial' => $user->onTrial(),
            'trialExpired' => $user->trialExpired(),
            'isAdmin' => $isAdmin,
        ]);
    }
}