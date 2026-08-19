<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Phase N3 (Role & Permission System) — the single source of truth
 * for this app's own permission/role vocabulary. Re-running this
 * seeder (php artisan db:seed --class=Database\Seeders\RolesAndPermissionsSeeder)
 * is always safe — every create() call below is firstOrCreate(),
 * never a plain create() that would throw a duplicate-key error or
 * pile up duplicate rows on a second run.
 *
 * PERMISSIONS (5, matching this phase's own requirement list
 * exactly):
 *   run-audit         Submit/view a single Website Audit
 *                      (routes/web.php's own audits.store/show/progress).
 *   view-discovery     Browse Website Discovery (routes/web.php's own
 *                      discovery.* group, minus its own export route).
 *   run-bulk-audit      Submit/view a Bulk Audit batch
 *                      (routes/web.php's own bulk-audits.* group).
 *   export-data         Download a PDF/Excel/CSV/JSON export from
 *                      EITHER Website Audit or Website Discovery —
 *                      ONE permission covering both export surfaces
 *                      (not two), since "can this person export data
 *                      out of the app at all" is a single meaningful
 *                      question an admin would want to toggle as one
 *                      switch, not worry about separately per module.
 *   view-admin-panel    Reach anything under /admin — see
 *                      routes/web.php's own 'admin.' route group,
 *                      protected by role:Admin middleware rather than
 *                      this specific permission directly (Admin is the
 *                      only role that should EVER reach the admin
 *                      panel, by definition — granting this one
 *                      permission to an Employee wouldn't actually let
 *                      them in, since the route itself checks the
 *                      ROLE, not this permission; it exists mainly for
 *                      completeness/future use, e.g. a future Gate
 *                      check inside an admin view that wants finer
 *                      granularity than "is Admin" alone).
 *
 * ROLES (3, matching this phase's own requirement list exactly):
 *   Admin        every permission above, unconditionally — the
 *                sync() call below means adding a 6th permission to
 *                this list in the future automatically reaches every
 *                Admin without this seeder needing to change, as long
 *                as it's added to $permissions before this role's own
 *                sync() call runs.
 *   Employee     NO permissions by default — "নির্দিষ্ট,
 *                admin-নির্ধারিত এক্সেস" (specific, admin-determined
 *                access) was this phase's own literal requirement: an
 *                Employee starts with nothing and an Admin grants
 *                exactly what that specific person needs via
 *                App\Http\Controllers\Admin\UserManagementController's
 *                own per-user permission checkboxes, rather than this
 *                seeder guessing a "reasonable default" set that would
 *                just get immediately overridden anyway.
 *   User         run-audit, view-discovery, run-bulk-audit,
 *                export-data — every FEATURE permission except
 *                view-admin-panel. Phase N5 (Dynamic Pricing/
 *                Subscription) is where this stops being a flat role-
 *                wide grant and starts being gated per subscription
 *                plan instead — see that phase's own docblock (once
 *                written) for exactly how; until then, every
 *                registered customer has full feature access, which
 *                is the correct default for an app with no billing
 *                system live yet (nothing to actually restrict against).
 */
final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'run-audit',
            'view-discovery',
            'run-bulk-audit',
            'export-data',
            'view-admin-panel',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('Admin', 'web');
        $admin->syncPermissions($permissions);

        // Deliberately empty — see this class's own docblock for why.
        Role::findOrCreate('Employee', 'web');

        $user = Role::findOrCreate('User', 'web');
        $user->syncPermissions([
            'run-audit',
            'view-discovery',
            'run-bulk-audit',
            'export-data',
        ]);

        // Bootstraps the FIRST real Admin — without this, there is no
        // way into App\Http\Controllers\Admin\UserManagementController's
        // own panel at all (a chicken-and-egg problem: only an Admin
        // can grant the Admin role, but no Admin exists yet). Reads a
        // specific email from .env (ADMIN_EMAIL) rather than
        // hard-coding one, or guessing "the first registered user" —
        // an explicit, deliberate choice avoids accidentally promoting
        // whichever random person happens to sign up first in
        // production. Silently does nothing if that user doesn't exist
        // yet (e.g. this seeder ran before that person ever
        // registered) — re-running this seeder later, after they
        // register, is the documented way to actually grant it (see
        // this phase's own deploy notes).
        $adminEmail = config('app.admin_email');

        if (is_string($adminEmail) && $adminEmail !== '') {
            $adminUser = User::query()->where('email', $adminEmail)->first();

            $adminUser?->assignRole('Admin');
        }
    }
}