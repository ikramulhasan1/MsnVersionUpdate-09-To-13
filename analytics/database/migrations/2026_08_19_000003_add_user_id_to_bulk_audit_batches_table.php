<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N2 — same reasoning as
 * database/migrations/2026_08_19_000002_add_user_id_to_audits_table.php's
 * own docblock, for BulkAuditBatch instead of Audit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bulk_audit_batches', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bulk_audit_batches', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};