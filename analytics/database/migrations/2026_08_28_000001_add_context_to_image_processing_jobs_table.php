<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image Everything (Phase S3 — Image SEO / Smart Metadata Generator).
 *
 * $context is the "Image Context" form a person fills in before/after
 * uploading (Primary Keyword, Secondary Keywords, Page Topic, Product
 * Name, Brand, Category, Target Audience, Image Purpose) — ONE json
 * blob on the JOB (not per-item) because that context applies to the
 * whole batch a job represents, matching how a person actually works:
 * they upload a set of images for the SAME product/page/topic at
 * once. See App\ImageProcessing\SmartMetadataGenerator's own docblock
 * for exactly which keys this array holds and how each is used.
 *
 * Deliberately NOT reusing image_processing_items' own $result column
 * for this — that column is PER-IMAGE generated output (Phase S2's
 * own migration already earmarked it for "S3's own SEO metadata
 * generation" at the ITEM level); $context is the INPUT that feeds
 * every item's own generation, shared across every item in the job,
 * so it belongs on the job row once, not duplicated onto every item.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('image_processing_jobs', function (Blueprint $table): void {
            $table->json('context')->nullable()->after('processed_images');
        });
    }

    public function down(): void
    {
        Schema::table('image_processing_jobs', function (Blueprint $table): void {
            $table->dropColumn('context');
        });
    }
};