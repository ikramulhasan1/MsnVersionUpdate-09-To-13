<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image Everything (Phase S2 — Metadata & Quality Analysis).
 *
 * Deliberately TWO new JSON columns, not one, and NOT the existing
 * generic $result column from Phase S1's own migration:
 *
 *   - $metadata        — everything App\ImageProcessing\ImageMetadataExtractor
 *                        reads directly off the file/Imagick (dimensions,
 *                        aspect ratio, color profile, EXIF, DPI,
 *                        orientation, transparency).
 *   - $quality_analysis — the derived, COMPUTED quality metrics (blur/
 *                         noise/compression/dynamic-range) plus the
 *                         final weighted score.
 *
 * Splitting these two lets a later phase (S3's own SEO metadata
 * generation) read/write ITS OWN thing into the still-untouched
 * $result column without those three JSON blobs colliding with each
 * other or overwriting one another wholesale on every partial update.
 *
 * $quality_score is pulled OUT into its own plain integer column
 * (0-100) even though it also lives inside $quality_analysis, purely
 * so a future "sort/filter images by quality" query never has to
 * reach into JSON — MySQL/SQLite JSON extraction is fine occasionally
 * but not something to build a WHERE/ORDER BY clause on.
 *
 * $analyzed_at is separate from $updated_at because $updated_at
 * changes on ANY future update to this row (e.g. Phase S4's own
 * resize/compress writing $processed_path) — this column freezes the
 * moment analysis itself actually finished.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('image_processing_items', function (Blueprint $table): void {
            $table->json('metadata')->nullable()->after('result');
            $table->json('quality_analysis')->nullable()->after('metadata');
            $table->unsignedTinyInteger('quality_score')->nullable()->after('quality_analysis');
            $table->timestamp('analyzed_at')->nullable()->after('quality_score');

            $table->index('quality_score');
        });
    }

    public function down(): void
    {
        Schema::table('image_processing_items', function (Blueprint $table): void {
            $table->dropIndex(['quality_score']);
            $table->dropColumn(['metadata', 'quality_analysis', 'quality_score', 'analyzed_at']);
        });
    }
};
