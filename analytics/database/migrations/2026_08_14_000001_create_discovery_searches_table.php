<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase A1 (foundation).
 *
 * A saved Industry/Niche + advanced-filter search — the criteria a user
 * built once against discovered_websites and wants to re-run later
 * (optionally on a recurring schedule) rather than rebuild from
 * scratch every time.
 *
 * `uuid`, unique, is this table's public/route-bindable identity,
 * mirroring audits.uuid / discovered_websites.uuid — the numeric `id`
 * never leaks into a URL.
 *
 * `user_id` is nullable and constrained (not required): this app's own
 * `users` table exists but audits and the rest of this module don't
 * currently require authentication to use, so a saved search isn't
 * forced to belong to a signed-in user — an anonymous/system-level
 * saved search is a valid case, not an error state. nullOnDelete()
 * keeps a saved search around (rather than cascading it away) if its
 * owning user is ever deleted, since the search criteria itself is
 * still meaningful/reusable independent of who originally saved it.
 *
 * `filters` is the actual search criteria as JSON — every Industry/
 * Niche/Location/Technology/score-range filter the module's own
 * advanced filter UI collects — rather than a fixed set of columns,
 * since the exact filter shape is still being defined phase by phase
 * and a JSON blob avoids a migration every time a new filter is added.
 *
 * `is_scheduled` + `last_run_at` support a later phase's recurring-
 * search feature (e.g. "email me new matches weekly"); both are inert
 * until that phase wires up a scheduler, but are included now so this
 * table doesn't need another migration just to add them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovery_searches', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->json('filters');
            $table->boolean('is_scheduled')->default(false)->index();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_searches');
    }
};