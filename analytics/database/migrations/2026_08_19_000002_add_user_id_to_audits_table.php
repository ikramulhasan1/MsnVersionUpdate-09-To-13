<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N2 (Sidebar + Dynamic Notification System) — added earlier
 * than originally planned (Phase N4, User Dashboard): a notification
 * telling someone their audit finished genuinely can't be sent
 * without knowing WHO to send it to, so this one column had to move
 * up. Phase N4 will build on this same column for "my own audits"
 * dashboard scoping, rather than adding a second one.
 *
 * Nullable — every route that creates an Audit is now behind the
 * 'auth' middleware (Phase N1), so in practice this is always set
 * going forward, but nullable rather than NOT NULL avoids retroactively
 * breaking any Audit row that already existed before this column did
 * (every audit run before Phase N1 shipped has no real owner at all).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};