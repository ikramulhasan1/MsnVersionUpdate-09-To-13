<?php

declare(strict_types=1);

use App\Models\DiscoveredWebsite;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * PRODUCTION INCIDENT (Website Discovery per-user privacy) — read
 * before removing this migration: every discovered_websites row that
 * existed BEFORE
 * database/migrations/2026_08_20_000002_add_user_id_to_discovered_websites_table.php
 * added the user_id column has no owner at all (user_id is null for
 * every one of them the moment that column is added). This app's own
 * explicit requirement — see
 * App\Discovery\Search\WebsiteSearchService::applyOwnershipVisibility()'s
 * own docblock — is that a row with NO owner is visible to an Admin
 * only, not every user. Left alone, that's already correct
 * (unowned = Admin-only is the DEFAULT this app's own visibility
 * filter already applies) — but the person who actually built this
 * app's own Discovery data before real per-account ownership existed
 * (every row here, both the ones from real Yelp/crawl searches and
 * the ones that leaked in from an early audit) genuinely WAS a single
 * user at the time. This migration makes that historical fact
 * EXPLICIT rather than leaving it as an anonymous "unowned" state:
 * every existing row gets user_id set to whichever account
 * config('app.admin_email') identifies (the same config value
 * database/seeders/RolesAndPermissionsSeeder already uses to bootstrap
 * the very first Admin — see that seeder's own docblock), so this
 * app's own real historical Discovery data is attributed to the
 * person who actually built it, not left as a generic "nobody" that
 * merely defaults to Admin-only by the absence of an owner.
 *
 * Silently does nothing if ADMIN_EMAIL isn't set or doesn't match a
 * real account yet (same reasoning
 * database/seeders/RolesAndPermissionsSeeder's own bootstrap already
 * follows) — every row simply stays unowned in that case, which is
 * still Admin-only per this app's own default visibility rule, just
 * not explicitly attributed to a specific person.
 */
return new class extends Migration
{
    public function up(): void
    {
        $adminEmail = config('app.admin_email');

        if (! is_string($adminEmail) || $adminEmail === '') {
            return;
        }

        $admin = User::query()->where('email', $adminEmail)->first();

        if ($admin === null) {
            return;
        }

        DiscoveredWebsite::query()->whereNull('user_id')->update(['user_id' => $admin->id]);
    }

    public function down(): void
    {
        // Deliberately a no-op — reversing this would mean setting
        // user_id back to null for whatever rows this migration
        // touched, which risks also nulling out a DIFFERENT, real
        // ownership assignment made after this migration ran (e.g. an
        // Admin manually reassigning a row's owner later). Safely
        // reversing this specific data change isn't possible without
        // risking exactly the wrong thing.
    }
};