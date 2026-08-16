<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase A1 (foundation).
 *
 * A short-list of discovered sites a user has flagged for follow-up
 * (audit/export/lead-scoring later) — a simple bookmark-with-notes
 * record, not a scored or scheduled entity of its own.
 *
 * Deliberately no `uuid`/route-bindable identity and no `user_id`
 * here: a watchlist entry is always reached through its parent
 * discovered_websites record (e.g. "toggle watchlist for site X"),
 * never linked to directly on its own, and — matching
 * discovery_searches' own nullable/unauthenticated-friendly `user_id`
 * — this module doesn't yet require per-user scoping anywhere, so a
 * watchlist entry isn't forced to belong to a signed-in user either. A
 * later phase can add user_id here (or promote this to its own
 * uuid-keyed resource) if per-user watchlists become a real
 * requirement, without disrupting this table's core shape.
 *
 * `discovered_website_id` is unique: a site can only be on the
 * watchlist once — re-adding an already-watchlisted site should
 * update the existing row (e.g. its notes), never create a duplicate
 * entry. cascadeOnDelete() removes the watchlist entry automatically
 * if the underlying discovered_websites row is ever deleted, since a
 * watchlist entry has no meaning without the site it refers to.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovery_watchlist', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discovered_website_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_watchlist');
    }
};
