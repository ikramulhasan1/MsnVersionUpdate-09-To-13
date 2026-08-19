<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase N1.5 (Free Trial) — a SECOND, independent gate on top of
 * spatie/laravel-permission's own 'permission:...' middleware (Phase
 * N3). The two answer different questions:
 *   - 'permission:run-bulk-audit' (role-based): "is this person's
 *     ROLE allowed to use Bulk Audit at all" — an Admin grants this
 *     once, it doesn't change day to day.
 *   - 'plan:run-bulk-audit' (this middleware, plan-based): "does their
 *     CURRENT plan include it right now" — changes the moment a trial
 *     expires or a subscription lapses, with no role change involved.
 * A route needing both stacks them: ->middleware(['permission:X', 'plan:X']).
 * Either one failing blocks access — a User-role account (which HAS
 * the run-bulk-audit permission by default, see
 * database/seeders/RolesAndPermissionsSeeder) whose Free Trial plan
 * marks that same feature false is correctly blocked by THIS
 * middleware even though the permission check alone would have let
 * them through.
 *
 * Redirects to the dashboard with a flashed upgrade message rather
 * than a bare 403 — a role/permission failure (Phase N3) is a real
 * access-control violation worth a blunt 403, but a plan limit is
 * something the person can ACT on (upgrade), so it gets a clear,
 * actionable message and a path forward instead.
 */
final class EnsurePlanAllowsFeature
{
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = $request->user();

        if ($user !== null && $user->planAllowsFeature($featureKey)) {
            return $next($request);
        }

        $message = $user?->trialExpired()
            ? 'Your free trial has ended. Upgrade your plan to unlock this feature.'
            : 'Your current plan doesn\'t include this feature. Upgrade to unlock it.';

        return redirect()->route('dashboard')->with('plan_limit_message', $message);
    }
}