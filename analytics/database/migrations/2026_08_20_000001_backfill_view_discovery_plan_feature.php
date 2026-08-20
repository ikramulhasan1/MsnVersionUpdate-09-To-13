<?php

declare(strict_types=1);

use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;

/**
 * PRODUCTION INCIDENT (Website Discovery access control) — read before
 * removing this migration: 'view-discovery' was gated ONLY by role
 * permission (Phase N3's own database/seeders/RolesAndPermissionsSeeder,
 * 'view-discovery' granted to the 'User' role by default) — never by
 * the person's actual Pricing Plan at all, unlike run-bulk-audit/
 * export-data, which App\Http\Middleware\EnsurePlanAllowsFeature
 * already enforced via a real 'plan:...' route middleware. Since
 * every registered customer holds the 'User' role, every customer —
 * regardless of which Plan they were actually on — could see the
 * entire shared Discovery pool, which is exactly the "user রা যা
 * দেখার কথা তার চেয়ে বেশি দেখছে" (users see more than their Plan
 * should allow) production report this migration is part of fixing.
 *
 * This migration runs BEFORE the corresponding route-middleware fix
 * goes live (see routes/web.php's own 'plan:view-discovery' addition)
 * specifically to backfill EVERY existing plan's own features JSON
 * with 'view-discovery' => true first — without this, deploying the
 * new middleware ahead of this backfill would have IMMEDIATELY broken
 * Discovery access for every existing customer on every existing
 * plan (a JSON key that doesn't exist defaults to false in
 * App\Models\Plan::allowsFeature()). An Admin can then turn
 * 'view-discovery' off for a specific plan afterward, through the
 * normal Admin Pricing Plans UI, if real per-plan Discovery
 * restriction is wanted going forward.
 */
return new class extends Migration
{
    public function up(): void
    {
        Plan::query()->get()->each(function (Plan $plan): void {
            $features = $plan->features;

            if (! array_key_exists('view-discovery', $features)) {
                $features['view-discovery'] = true;
                $plan->update(['features' => $features]);
            }
        });
    }

    public function down(): void
    {
        // Deliberately a no-op — removing 'view-discovery' from every
        // plan's own features JSON on rollback could strip an Admin's
        // own, later, deliberate per-plan choice (e.g. they explicitly
        // turned it OFF for a specific plan after this migration ran),
        // which this migration has no way to distinguish from its own
        // backfill. Reversing this specific data change safely isn't
        // possible without risking exactly the wrong thing.
    }
};