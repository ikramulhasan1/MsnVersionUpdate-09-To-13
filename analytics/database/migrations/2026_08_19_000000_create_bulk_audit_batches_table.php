<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase K2 (Bulk Audit Batch) — one row per "audit several websites
 * together" submission (see App\Models\BulkAuditBatch's own docblock
 * for the model). Follows this app's own uuid route-key-binding
 * convention (App\Models\Audit, App\Models\DiscoverySearch, ...) —
 * `uuid` is the public/route-bindable identity, `id` never leaks into
 * a URL.
 *
 * total_count/completed_count/failed_count are plain denormalized
 * counters, not computed from audits.bulk_audit_batch_id on every
 * read: a batch's own progress bar (Phase K5) needs to be read
 * cheaply and often (polling, the same way a single audit's own
 * progress already is — see App\Audit\Cache\AuditCacheService), and
 * counting COUNT(*) ... WHERE bulk_audit_batch_id = ? GROUP BY status
 * on every poll doesn't scale as well as incrementing two columns as
 * each child Audit finishes. See App\Models\BulkAuditBatch's own
 * progressPercent() for how these three numbers turn into a
 * percentage.
 *
 * `status` and `mode` are both plain string columns cast to real PHP
 * enums at the model layer (App\Audit\Enums\BulkAuditBatchStatus,
 * App\Audit\Enums\AuditMode) — the same "no schema-level ENUM type"
 * convention `audits.status`/`audits.mode` already established on
 * this same table family, chosen for the same reason: adding a future
 * case is a one-line PHP enum change, not an ALTER TABLE.
 *
 * `mode` here is the mode EVERY audit in this batch was submitted
 * with (Quick Scan and Full Audit aren't mixed within a single bulk
 * submission — see the Phase K3 submission form) — stored on the
 * batch itself so a batch's own results page (Phase K5) can label
 * itself "Quick Scan — 12 websites" without joining out to any child
 * Audit to find out.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_audit_batches', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('completed_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('status', 32)->default('pending')->index();
            $table->string('mode', 16)->default('full');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_audit_batches');
    }
};