<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase K1 (Quick Scan Mode) — see App\Audit\Enums\AuditMode's own
 * docblock for what 'full' vs 'quick' actually changes about how an
 * audit runs.
 *
 * A plain string column (not a MySQL native ENUM type), matching the
 * existing `status` column's own convention on this same table
 * (App\Audit\Enums\AuditStatus is likewise cast on top of a plain
 * `string` column) — application-level enum casting via
 * App\Models\Audit::$casts gives the same type safety in PHP without
 * a schema-level ENUM, which is more awkward to add new cases to
 * later (an ALTER TABLE ... MODIFY, versus just adding a new case to
 * the PHP enum).
 *
 * default('full') so every audit that predates this column — and any
 * insert path that doesn't explicitly set one — keeps behaving
 * exactly as it always has, with no migration of existing rows
 * needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->string('mode', 16)->default('full')->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->dropColumn('mode');
        });
    }
};
