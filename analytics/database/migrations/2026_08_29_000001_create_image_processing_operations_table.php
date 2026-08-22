<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image Everything (Phase S4 — Image Studio: Resize/Compress/Convert).
 *
 * One row per PROCESSING OPERATION a person runs against a single
 * image_processing_items row — deliberately its OWN table, not a
 * second JSON blob bolted onto ImageProcessingItem's own $result
 * (which Phase S3 already owns for that item's SEO metadata): an item
 * can have MANY operations over its lifetime (resize it, then also
 * compress it, then also generate a responsive set from the same
 * original), each with its own status/progress/output, and each needs
 * to be independently pollable and independently downloadable. A
 * single column could not hold that history.
 *
 * $result's shape depends on 'type':
 *   - resize/crop/compress/convert: {path, width, height, format,
 *     file_size_bytes, original_file_size_bytes, savings_percent}
 *   - responsive: {variants: [{width, path, file_size_bytes}, ...],
 *     srcset_html}
 * See App\ImageProcessing\ImageStudioProcessor's own docblock for
 * exactly which method produces which shape.
 *
 * $status reuses App\Enums\ImageItemStatus verbatim (not a new enum)
 * — that enum's own docblock already documents PENDING/PROCESSING/
 * COMPLETED/FAILED as covering the whole Image Everything pipeline
 * generically, and an operation's own lifecycle is exactly that same
 * shape.
 *
 * Every real OUTPUT FILE an operation produces lives under that same
 * item's own job's 'private-images' disk folder (see
 * App\ImageProcessing\ImageStudioProcessor's own docblock for the
 * exact path), so App\Console\Commands\CleanupExpiredImageJobsCommand
 * deleting a whole job's directory already sweeps every operation's
 * output automatically — no separate cleanup path needed for this
 * table's own files.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_processing_operations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('image_processing_items')->cascadeOnDelete();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->json('params');
            $table->json('result')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_processing_operations');
    }
};