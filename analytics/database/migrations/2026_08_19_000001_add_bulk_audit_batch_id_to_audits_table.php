<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase K2 (Bulk Audit Batch) — links an Audit back to the
 * BulkAuditBatch it was submitted as part of, when it was. Nullable:
 * an audit submitted the ordinary way (home/index.blade.php's own
 * single-URL form) has no batch at all, and this column stays null
 * for it — this is the one, single place that distinguishes "a
 * standalone audit" from "one audit out of a bulk submission", not a
 * separate boolean flag or a second table.
 *
 * cascadeOnDelete(): deleting a BulkAuditBatch deletes every Audit
 * that belongs to it. This is a deliberate choice, not an oversight —
 * a bulk batch and its own child audits are meant to be managed as
 * one unit (Phase K5's own results page is the only place these
 * particular audits are ever really looked at again); an orphaned
 * Audit left behind with a dangling bulk_audit_batch_id reference
 * would be strictly worse than the batch and its audits disappearing
 * together.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->foreignId('bulk_audit_batch_id')
                ->nullable()
                ->after('mode')
                ->constrained('bulk_audit_batches')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bulk_audit_batch_id');
        });
    }
};