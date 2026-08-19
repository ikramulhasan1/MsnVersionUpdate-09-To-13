<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\Plan;
use App\Models\User;

/**
 * Phase N1.5 (Free Trial) — the ONE place a brand new account gets its
 * starting role and plan, called from BOTH
 * App\Http\Controllers\Auth\RegisteredUserController::store() (password
 * registration) and App\Http\Controllers\Auth\GoogleAuthController::callback()
 * (Google sign-in, new-account branch only — an EXISTING account
 * linking Google for the first time is not "new" and must not be
 * re-onboarded, which would silently reset a real paid plan back to
 * Free Trial).
 *
 * PRODUCTION INCIDENT — read before removing the role-assignment call
 * below: Phase N3 (Role & Permission System) shipped a
 * RolesAndPermissionsSeeder that created the 'User' role but nothing
 * anywhere ever ASSIGNED it to a newly-registered account — every
 * real signup after that phase had ZERO role and therefore zero
 * permissions, meaning 'run-audit' (and everything else) failed for
 * every brand new user. This class exists specifically to close that
 * gap in the same place the OTHER new-account default (Free Trial)
 * gets set, so the two can never drift out of sync again by being
 * assigned in two different, easy-to-forget locations.
 */
final class NewUserOnboarder
{
    public function onboard(User $user): void
    {
        $user->assignRole('User');

        $trialPlan = Plan::query()->where('is_default_trial', true)->first();

        if ($trialPlan === null) {
            // No trial plan configured at all (e.g.
            // database/seeders/PlansSeeder hasn't been run yet) —
            // leave the account planless rather than guessing or
            // throwing; every planAllowsFeature() check already
            // treats "no plan" as "nothing allowed", the same safe
            // default as a genuinely expired trial.
            return;
        }

        $user->forceFill([
            'plan_id' => $trialPlan->id,
            'subscribed_at' => now(),
            'trial_ends_at' => $trialPlan->duration_days !== null
                ? now()->addDays($trialPlan->duration_days)
                : null,
        ])->save();
    }
}