<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N1.5 (Free Trial) — the FIRST real row in this table is the
 * "Free Trial" plan (see database/seeders/PlansSeeder), but this
 * table's own shape is deliberately built to be the SAME one Phase N5
 * (Dynamic Pricing/Subscription) will use for every real paid plan an
 * Admin creates later — not a trial-specific table that N5 would need
 * to replace. An Admin editing "Free Trial" through N5's own future
 * plan-management UI and an Admin creating a brand new "Pro" plan
 * there go through the exact same form, against the exact same
 * columns.
 *
 * `features` is a single JSON column rather than a separate table
 * (plan_features) or one boolean column per feature: this app's own
 * feature set is small and known (run-audit, run-bulk-audit,
 * export-data, plus non-boolean settings like a daily audit limit —
 * see App\Models\Plan's own docblock for the exact expected shape),
 * and a JSON column lets a NEW feature/limit be added to this app
 * later (Phase N7's own API access, say) without a schema migration —
 * only App\Models\Plan's own docblock (documenting the expected keys)
 * and whatever code actually checks that key need to change.
 *
 * `duration_days` is nullable — null means "does not expire" (every
 * real paid plan N5 will eventually create), a real integer means
 * "expires this many days after being assigned" (Free Trial's own
 * value: 3). `is_default_trial` marks WHICH plan (there should only
 * ever be one) gets auto-assigned to a brand new registration — see
 * App\Http\Controllers\Auth\RegisteredUserController::store()'s own
 * use of this flag.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('billing_cycle')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->json('features');
            $table->boolean('is_default_trial')->default(false);
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};