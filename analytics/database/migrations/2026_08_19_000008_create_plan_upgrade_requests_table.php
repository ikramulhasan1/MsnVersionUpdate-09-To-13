<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — records a user's own
 * "I want this plan" click on resources/views/subscription/upgrade.blade.php.
 * No payment system exists yet (Phase N6) — this table is the whole
 * "request পাঠাবে" half of this phase's own requirement (the OTHER
 * half, "checkout flow শুরু করবে", is exactly what Phase N6 adds), so
 * a request here is fulfilled by an Admin manually assigning the plan
 * via App\Http\Controllers\Admin\UserManagementController::update()
 * — status just tracks whether that's happened yet, not any real
 * transaction.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_upgrade_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_upgrade_requests');
    }
};