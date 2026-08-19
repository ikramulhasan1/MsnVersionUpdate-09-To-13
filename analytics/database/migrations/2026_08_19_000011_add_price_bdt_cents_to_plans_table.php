<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N6 (Multiple Payment Methods) — PRODUCTION BUG CAUGHT BEFORE
 * DEPLOY: App\Models\Plan::$price_cents is USD (set via the Admin
 * plan form's own "Price (USD)" field, Phase N5) — the FIRST version
 * of App\Http\Controllers\Payments\CheckoutController's own SSLCommerz
 * flow sent that SAME raw number to SSLCommerz labeled as BDT, with no
 * conversion at all (a $19.00 plan would have charged 19.00 BDT — off
 * by roughly the entire USD/BDT exchange rate, not a rounding error).
 *
 * Rather than convert automatically at checkout time using a hardcoded
 * or live-fetched exchange rate (either goes stale or adds a real
 * external dependency this app doesn't otherwise have), an Admin sets
 * price_bdt_cents EXPLICITLY per plan (nullable — many real businesses
 * price a local market independently rather than as a pure mechanical
 * conversion anyway). See App\Http\Controllers\Payments\CheckoutController's
 * own updated logic: a plan with no price_bdt_cents set simply doesn't
 * offer SSLCommerz as a checkout option at all, rather than guessing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->unsignedInteger('price_bdt_cents')->nullable()->after('price_cents');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->dropColumn('price_bdt_cents');
        });
    }
};