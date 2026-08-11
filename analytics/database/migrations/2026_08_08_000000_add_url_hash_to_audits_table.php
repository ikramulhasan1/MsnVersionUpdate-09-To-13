<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * AuditRepository::findLatestPendingByUrl() runs on every audit
 * submission and, before this, filtered directly on the `url` column
 * — up to 2048 characters, unindexed. Indexing that column directly
 * is impractical (MySQL/InnoDB caps index key length well below
 * 2048 bytes for utf8mb4), so instead this adds a fixed-length,
 * indexed hash column and backfills it for any existing rows, mirroring
 * the md5()-based key strategy AuditCacheService already uses for the
 * same reason (see keyFor() there).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->char('url_hash', 32)->nullable()->after('url')->index();
        });

        // Backfill any rows created before this column existed.
        DB::table('audits')->select(['id', 'url'])->orderBy('id')->chunkById(500, function ($audits): void {
            foreach ($audits as $audit) {
                DB::table('audits')
                    ->where('id', $audit->id)
                    ->update(['url_hash' => md5((string) $audit->url)]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table): void {
            $table->dropColumn('url_hash');
        });
    }
};
