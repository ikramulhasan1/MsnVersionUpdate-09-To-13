<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Phase N1.5 (Free Trial) — creates the ONE plan every new
 * registration actually gets automatically
 * (App\Http\Controllers\Auth\RegisteredUserController::store()/
 * App\Http\Controllers\Auth\GoogleAuthController::callback() both
 * assign whichever plan has is_default_trial = true, by that flag,
 * not by hard-coding this specific row's own id/slug), plus two
 * placeholder PAID plans purely so the homepage's own Pricing preview
 * section (resources/views/home/index.blade.php) has real rows to
 * render instead of hard-coded marketing copy. Phase N5's own Admin
 * Pricing UI is where these two placeholders get replaced with
 * whatever an Admin actually wants to charge — updateOrCreate() by
 * slug throughout, so re-running this seeder after Phase N5 exists
 * won't clobber real Admin edits to Free Trial's own limits, only
 * fill in a slug that's missing entirely.
 */
final class PlansSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['slug' => 'free-trial'],
            [
                'name' => 'Free Trial',
                'description' => '3 days to try every core feature, with a few limits.',
                'price_cents' => 0,
                'billing_cycle' => null,
                'duration_days' => 3,
                'features' => [
                    'run-audit' => true,
                    'view-discovery' => true,
                    'run-bulk-audit' => false,
                    'export-data' => false,
                    'daily_audit_limit' => 3,
                ],
                'is_default_trial' => true,
                'is_public' => false,
                'sort_order' => 0,
            ],
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'description' => 'For freelancers and small agencies auditing a handful of sites.',
                'price_cents' => 1900,
                'billing_cycle' => 'month',
                'duration_days' => null,
                'features' => [
                    'run-audit' => true,
                    'view-discovery' => true,
                    'run-bulk-audit' => true,
                    'export-data' => true,
                    'daily_audit_limit' => 25,
                ],
                'is_default_trial' => false,
                'is_public' => true,
                'sort_order' => 1,
            ],
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'agency'],
            [
                'name' => 'Agency',
                'description' => 'For teams running audits and discovery at real scale.',
                'price_cents' => 4900,
                'billing_cycle' => 'month',
                'duration_days' => null,
                'features' => [
                    'run-audit' => true,
                    'view-discovery' => true,
                    'run-bulk-audit' => true,
                    'export-data' => true,
                    'daily_audit_limit' => null,
                ],
                'is_default_trial' => false,
                'is_public' => true,
                'sort_order' => 2,
            ],
        );
    }
}