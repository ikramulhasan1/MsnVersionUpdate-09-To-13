<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N1.5 (Free Trial) — links every user to their current plan
 * (nullable — an account created before this column existed, or one
 * an Admin hasn't assigned a plan to yet, simply has none) and tracks
 * when their CURRENT plan period ends. subscribed_at/trial_ends_at
 * are deliberately generic names, not trial_started_at/
 * trial_ends_at specifically — Phase N5's own real paid subscriptions
 * reuse these SAME two columns (subscribed_at = when they started
 * this plan, trial_ends_at = when it expires, null for a plan with no
 * expiry) rather than needing their own separate pair once billing
 * exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('plan_id')->nullable()->after('avatar_url')->constrained()->nullOnDelete();
            $table->timestamp('subscribed_at')->nullable()->after('plan_id');
            $table->timestamp('trial_ends_at')->nullable()->after('subscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['subscribed_at', 'trial_ends_at']);
            $table->dropConstrainedForeignId('plan_id');
        });
    }
};