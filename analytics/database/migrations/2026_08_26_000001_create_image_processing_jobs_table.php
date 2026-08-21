<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image Everything (Phase S1 — Temporary Job Infrastructure) — the
 * TOP-LEVEL record for one image-processing session (a single upload,
 * or a bulk batch — see image_processing_items' own migration for the
 * per-image rows underneath one of these). PRODUCTION-CRITICAL DESIGN
 * PRINCIPLE, read before adding any column that stores raw image
 * bytes here or anywhere near it: this app's own explicit requirement
 * is that NO image is ever stored permanently in the database or
 * public storage — this table (and image_processing_items) hold only
 * METADATA and TEMPORARY FILE PATHS (see App\Models\ImageProcessingItem's
 * own $temp_path/$processed_path columns), never image content itself.
 * The actual bytes live only in storage/app/private/image-processing/{uuid}/
 * (see config/filesystems.php's own new 'private-images' disk) and are
 * deleted by App\Console\Commands\CleanupExpiredImageJobsCommand once
 * $expires_at passes — this table's own row is deleted in that SAME
 * cleanup pass, never left behind as an orphaned "metadata-only"
 * record pointing at files that no longer exist.
 *
 * user_id is NOT nullable — same reasoning as App\Models\KeywordList/
 * App\Models\TechnicalSeoScan's own migrations: every job is created by
 * a real, logged-in person taking an explicit action.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_processing_jobs', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedInteger('total_images')->default(0);
            $table->unsignedInteger('processed_images')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('last_activity_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('expires_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_processing_jobs');
    }
};