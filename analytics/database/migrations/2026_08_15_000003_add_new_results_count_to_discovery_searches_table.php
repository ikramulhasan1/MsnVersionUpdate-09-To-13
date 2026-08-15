<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase F4 (Scheduled Search + New Website
 * Detection).
 *
 * discovery_searches already had `is_scheduled`/`last_run_at` (Phase
 * A1, built ahead of any scheduler actually using them) — this adds
 * the one more column App\Discovery\Jobs\RunScheduledDiscoverySearchJob
 * needs to report "N New Websites Found" on the Saved Searches page
 * (resources/views/discovery/saved.blade.php): the count of matching
 * discovered_websites rows whose discovered_at is newer than this
 * search's own last_run_at, computed fresh on every scheduled run and
 * simply displayed, not recomputed on page load.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovery_searches', function (Blueprint $table): void {
            $table->unsignedInteger('new_results_count')->nullable()->after('last_run_at');
        });
    }

    public function down(): void
    {
        Schema::table('discovery_searches', function (Blueprint $table): void {
            $table->dropColumn('new_results_count');
        });
    }
};