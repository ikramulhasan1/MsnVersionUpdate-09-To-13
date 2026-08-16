<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase G2 (Watchlist Monitoring Job).
 *
 * App\Discovery\Enums\WebsiteConnectivityStatus's own docblock has said
 * since Phase C1 that no column stores this yet — the Advanced
 * Filters panel's "Website Status" checkboxes have been UI-only ever
 * since for exactly that reason. App\Discovery\Jobs\MonitorWatchlistChangesJob
 * is the first thing in this module that actually needs a persisted
 * "online or offline last time we checked" value to compare a fresh
 * check against (there's no earlier run to compare against without
 * one), so this column is added now, driven by that real need rather
 * than added speculatively ahead of one.
 *
 * Nullable, like every other enrichment column on this table: a
 * discovered site's connectivity is only known once something has
 * actually tried to reach it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->string('connectivity_status', 20)->nullable()->after('ssl_status');
        });
    }

    public function down(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->dropColumn('connectivity_status');
        });
    }
};