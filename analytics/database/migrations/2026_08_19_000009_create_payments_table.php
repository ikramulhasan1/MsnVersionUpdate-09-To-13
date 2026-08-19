<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N6 (Multiple Payment Methods) — the single source of truth for
 * BOTH "did this payment actually succeed" (what activates a plan —
 * see App\Http\Controllers\Payments\StripeWebhookController and
 * App\Http\Controllers\Payments\SslCommerzController for the two
 * gateways that write here) AND the user-facing Billing History page
 * (resources/views/billing/history.blade.php) — one row per payment
 * attempt, success or failure alike, so a failed attempt still shows
 * up with a clear status rather than vanishing.
 *
 * gateway_reference is the OTHER side's own transaction/session
 * identifier (a Stripe Checkout Session ID, or an SSLCommerz
 * tran_id/val_id) — kept as a plain string since the two gateways'
 * own ID formats share nothing in common; never assumed to be unique
 * globally across gateways (a Stripe session ID and an SSLCommerz
 * tran_id could theoretically collide as raw strings), only unique
 * scoped to $gateway, enforced by the composite unique index below.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->string('gateway_reference');
            $table->unsignedInteger('amount_cents');
            $table->string('currency', 3)->default('USD');
            $table->string('status');
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'gateway_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};