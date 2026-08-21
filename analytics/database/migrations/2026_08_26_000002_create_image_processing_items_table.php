<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image Everything (Phase S1) — one row per individual image within a
 * image_processing_jobs row (see that table's own migration docblock
 * for the full "never store image content anywhere permanent"
 * principle this table follows too). $temp_path/$processed_path are
 * PATHS on the 'private-images' disk (config/filesystems.php),
 * relative to that job's own {uuid}/ folder — never a full public URL,
 * never raw bytes in this column, and never the ORIGINAL filename used
 * as part of the path (see App\ImageProcessing\ImageJobService's own
 * docblock for why a UUID-based stored filename matters for security).
 *
 * $result is a JSON column added for later phases (S2's own metadata/
 * quality-analysis output, S3's own generated SEO fields) to populate
 * without needing yet another migration each time — deliberately
 * schemaless/flexible here, unlike the job-level columns above which
 * stay strict, since what THIS column holds genuinely differs by
 * which later phase populated it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_processing_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('image_processing_jobs')->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('temp_path');
            $table->string('processed_path')->nullable();
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('format')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_processing_items');
    }
};