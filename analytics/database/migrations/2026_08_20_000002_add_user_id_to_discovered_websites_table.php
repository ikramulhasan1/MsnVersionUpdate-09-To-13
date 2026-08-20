<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION INCIDENT (Website Discovery access control) — REPLACES an
 * earlier, destructive approach to the same underlying problem: an
 * earlier migration (2026_08_20_000002_remove_audit_sourced_discovered_websites,
 * now deleted rather than kept as dead history — it never ran in
 * production) DELETED every discovered_websites row with
 * discovery_source = 'audit' outright. That's the wrong fix for real
 * production data an Admin explicitly wants kept — deleting a website
 * someone spent real effort auditing just because it leaked into the
 * wrong list throws away real work to fix a visibility bug, when
 * fixing the VISIBILITY is enough on its own.
 *
 * This migration instead adds user_id — nullable, and ONLY ever
 * meaningfully set going forward for a row created FROM a private
 * audit (discovery_source = 'audit') by
 * App\Audit\Jobs\AssembleAnalysisResultsJob::syncToDiscoveredWebsite();
 * a row from a REAL Discovery search/crawl (discovery_source =
 * 'yelp'/'internal_crawl'/'web') has no individual owner at all and
 * this column stays null for those, same as always. See
 * App\Discovery\Search\WebsiteSearchService::query()'s own new
 * visibility filter for how this column is actually used: only the
 * owning user (or an Admin) can see an audit-sourced row that has an
 * owner; a row with discovery_source = 'audit' AND user_id still
 * null (every row that predates this column — no owner was ever
 * tracked for those) is visible to an Admin only, which is exactly
 * what was asked for the existing 4 already-leaked rows in this app's
 * own real production data — kept, not deleted, but no longer visible
 * to every other user.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};