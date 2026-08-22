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
 * PERMISSIONS (12 as of this update — the original 5 from Phase N3,
 * plus 7 more added later, closing a real production gap: every
 * feature built across Phase O (Keyword Research/Magic Tool/Lists),
 * Phase Q (Competitor/Backlink Analysis), and Phase R (On-Page/
 * Technical SEO) shipped with NO permission gate at all — each
 * sidebar item/route had no 'permission' key/middleware, reachable by
 * any logged-in user regardless of role. That matched those phases'
 * own explicit design AT THE TIME (deliberately not tied into the
 * Plan/Permission system yet — see each phase's own sidebar comment,
 * e.g. "gated by provider availability, not a role/plan check"), but
 * left an Admin with no way to actually RESTRICT an Employee/User from
 * a specific one of these tools, which defeats the whole point of a
 * granular Employee role. The 7 new permissions below close that gap
 * the same way the original 5 already work for Website Audit/
 * Discovery/Bulk Audit/Export:
 *   use-keyword-research     Keyword Research page (Phase O3).
 *   use-keyword-magic-tool   Keyword Magic Tool page (Phase O4).
 *   use-keyword-lists        My Keyword Lists page (Phase O5) — kept
 *                            SEPARATE from the two above rather than
 *                            folded into either one, since a person
 *                            could reasonably be allowed to run
 *                            Keyword Research/Magic Tool without being
 *                            allowed to save/manage persistent lists,
 *                            or vice versa.
 *   use-competitor-analysis  Competitor Analysis page (Phase Q2).
 *   use-backlink-analysis    Backlink Analysis page (Phase Q3).
 *   use-onpage-seo-checker   On-Page SEO Checker page (Phase R1).
 *   use-technical-seo-audit  Technical SEO Audit page (Phase R2).
 *   use-image-seo            Image SEO / Smart Metadata Generator page
 *                            (Phase S3) — same "close the gap" reasoning
 *                            as the 7 above, added at the same time this
 *                            page itself shipped rather than retrofitted
 *                            later.
 *
 * ROLES (3, matching this phase's own requirement list exactly):
 *   Admin        every permission above, unconditionally — the
 *                sync() call below means adding a permission to
 *                this list in the future automatically reaches every
 *                Admin without this seeder needing to change, as long
 *                as it's added to $permissions before this role's own
 *                sync() call runs. Also see App\Providers\AppServiceProvider's
 *                own Gate::before() — an Admin bypasses every
 *                permission check outright regardless of what's
 *                actually synced here, so this sync is more about
 *                keeping the DATA consistent than the access itself.
 *   Employee     NO permissions by default — "নির্দিষ্ট,
 *                admin-নির্ধারিত এক্সেস" (specific, admin-determined
 *                access) was this phase's own literal requirement: an
 *                Employee starts with nothing and an Admin grants
 *                exactly what that specific person needs via
 *                App\Http\Controllers\Admin\UserManagementController's
 *                own per-user permission checkboxes, rather than this
 *                seeder guessing a "reasonable default" set that would
 *                just get immediately overridden anyway.
 *   User         every FEATURE permission except view-admin-panel —
 *                the 7 new ones included, so a re-run of this seeder
 *                on an EXISTING production database doesn't silently
 *                take away access every existing customer already had
 *                (these tools were reachable by any logged-in user
 *                before this permission existed at all; this seeder
 *                preserves that same access, just makes it a real,
 *                Admin-toggleable permission going forward rather than
 *                an unconditional given). Phase N5 (Dynamic Pricing/
 *                Subscription) is where this stops being a flat role-
 *                wide grant and starts being gated per subscription
 *                plan instead.
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
            // PRODUCTION GAP CLOSED — see this class's own docblock.
            'use-keyword-research',
            'use-keyword-magic-tool',
            'use-keyword-lists',
            'use-competitor-analysis',
            'use-backlink-analysis',
            'use-onpage-seo-checker',
            'use-technical-seo-audit',
            'use-image-seo',
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
            'use-keyword-research',
            'use-keyword-magic-tool',
            'use-keyword-lists',
            'use-competitor-analysis',
            'use-backlink-analysis',
            'use-onpage-seo-checker',
            'use-technical-seo-audit',
            'use-image-seo',
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