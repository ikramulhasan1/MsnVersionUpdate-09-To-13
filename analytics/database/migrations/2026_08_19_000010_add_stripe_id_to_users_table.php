<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N6 (Multiple Payment Methods) — ONLY stripe_id, deliberately.
 * Laravel Cashier's Billable trait normally also expects pm_type,
 * pm_last_four, and its OWN trial_ends_at column — none are added
 * here, because this app uses Cashier for exactly one thing
 * (Billable::checkout(), which creates a one-time Stripe Checkout
 * Session — see App\Http\Controllers\Payments\CheckoutController's
 * own docblock) and nothing else. Cashier's own generic
 * trial_ends_at would collide directly with the SAME column Phase
 * N1.5's Free Trial system already owns with different semantics —
 * adding it here would be a real, silent bug (two different features
 * fighting over one column), not merely an unused one.
 * pm_type/pm_last_four are only needed for Cashier's own stored
 * default-payment-method features, which this app doesn't use either
 * (every checkout is a fresh Stripe Checkout Session, not a saved
 * card charged later).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('stripe_id')->nullable()->index()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('stripe_id');
        });
    }
};