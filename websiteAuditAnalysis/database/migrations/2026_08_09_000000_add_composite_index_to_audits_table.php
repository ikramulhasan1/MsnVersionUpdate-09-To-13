<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AuditRepository::findLatestPendingByUrl() filters on url_hash AND
 * status (whereNotIn COMPLETED/FAILED) together, then orders by id —
 * every audit submission runs this query. The single-column url_hash
 * index (added in add_url_hash_to_audits_table) already lets the
 * database jump straight to the matching rows, but as a URL
 * accumulates history (repeated audits of the same site over time),
 * the engine still has to check `status` and sort `id` for each
 * matching row individually. A composite (url_hash, status, id) index
 * lets it satisfy the whole WHERE ... ORDER BY in one index scan
 * instead. Purely additive — no column, data, or query behavior
 * changes, only how the existing query is served.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->index(['url_hash', 'status', 'id'], 'audits_url_hash_status_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->dropIndex('audits_url_hash_status_id_index');
        });
    }
};
