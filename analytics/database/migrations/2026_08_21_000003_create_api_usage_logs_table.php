<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase O2 (Keyword Data Service Layer) — one row per REAL provider
 * call App\KeywordData\KeywordDataService actually makes (a cache hit
 * never reaches here at all — see keyword_data_cache's own migration).
 * Backs the Admin cost-summary page
 * (resources/views/admin/api-usage/index.blade.php) — "how much have
 * we actually spent this month, broken down by provider".
 *
 * estimated_cost_usd is exactly that — ESTIMATED, computed from each
 * provider TYPE's own known/documented per-call pricing at the moment
 * of the call (see App\KeywordData\KeywordDataService::estimatedCostUsd()'s
 * own docblock for the actual figures and their source) — never a
 * real, provider-confirmed invoice line. Treat this table as a
 * budgeting/monitoring aid, not an accounting-grade record — a
 * provider's real bill is the authoritative source for anything
 * financial.
 *
 * DECIMAL(10,6), not integer cents — DataForSEO's own real per-call
 * price is $0.000075 per keyword (their $0.075/1,000 rate); a small
 * batch (say, 10 keywords = $0.00075) would round to literally zero
 * in whole cents, silently losing all cost data for typical single-
 * lookup requests. Six decimal places keeps a single keyword's own
 * fractional-cent cost meaningfully non-zero.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_usage_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('api_provider_id')->constrained()->cascadeOnDelete();
            $table->string('capability');
            $table->unsignedInteger('keyword_count')->default(1);
            $table->decimal('estimated_cost_usd', 10, 6)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['api_provider_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};