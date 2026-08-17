<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase G2 (Watchlist Monitoring Job).
 *
 * A log table, not a "current state" table: discovered_websites itself
 * already holds each metric's own current value (seo_score,
 * performance_score, cms/framework/etc, ssl_status, connectivity_status
 * — the last one added by this same phase's other migration). This
 * table instead records the HISTORY of changes
 * App\Discovery\Jobs\MonitorWatchlistChangesJob detects between one run
 * and the next — one row per detected change, not one row per check
 * (a check that finds nothing different writes nothing here).
 *
 * discovered_website_id is used directly rather than going through
 * discovery_watchlist, even though this job's own dispatch loop starts
 * from a DiscoveryWatchlistItem: the change itself is a property of the
 * SITE (its SEO score changed), not of the watchlist entry that
 * happened to be how this module noticed to check it — a site removed
 * from the watchlist and re-added later should still see its own
 * change history, not start a fresh one. cascadeOnDelete() still
 * applies, matching every other discovered_websites-referencing table
 * in this schema: a change log has no meaning once the site itself is
 * gone.
 *
 * old_value/new_value are plain nullable strings — every value being
 * compared (a score, a technology summary, an ssl_status string, a
 * connectivity_status string) is already representable as one, so a
 * JSON column would add complexity this table doesn't need yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovery_watchlist_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discovered_website_id')
                ->constrained('discovered_websites')
                ->cascadeOnDelete();
            $table->string('change_type', 50);
            $table->string('old_value', 500)->nullable();
            $table->string('new_value', 500)->nullable();
            $table->timestamp('detected_at');
            $table->timestamps();

            // Explicit short name: Laravel's own auto-generated name for
            // this composite index ("discovery_watchlist_changes_
            // discovered_website_id_change_type_detected_at_index") is
            // 82 characters — over MySQL's 64-character identifier
            // limit, which made this migration fail outright on MySQL
            // with "Identifier name ... is too long" (worked fine on
            // SQLite, which has no such limit, so this went unnoticed
            // until a real MySQL deployment). A short, explicit name
            // sidesteps the limit entirely.
            $table->index(['discovered_website_id', 'change_type', 'detected_at'], 'dwc_website_type_detected_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_watchlist_changes');
    }
};
