<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N2 (Sidebar + Dynamic Notification System) — the standard
 * schema Laravel's own `notifications:table` stub generates for the
 * built-in 'database' notification channel, written by hand here
 * since this app couldn't run that Artisan command directly (see this
 * phase's own deploy notes). Every notification class in
 * app/Notifications/ (AuditCompletedNotification,
 * BulkAuditBatchCompletedNotification,
 * DiscoveryNewWebsitesFoundNotification) writes here via its own
 * toDatabase() method, and App\Models\User's already-present
 * Notifiable trait (see that model's own docblock) reads from here
 * via $user->notifications (an Eloquent morphMany relation the trait
 * provides automatically — no relation needs to be declared by hand).
 *
 * id is a UUID (not an auto-increment int) — Laravel's own
 * DatabaseNotification model expects this specifically, matching how
 * every real Laravel app's notifications table is shaped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};