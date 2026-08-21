<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase R2 (Technical SEO Audit) — one row per scan request. Unlike
 * App\Models\DiscoveredWebsite, user_id is NOT nullable here — same
 * reasoning as App\Models\KeywordList's own migration docblock: every
 * scan is created by a real, logged-in person taking an explicit
 * action, no "legacy row from before ownership existed" case applies.
 *
 * $status mirrors App\Audit\Enums\AuditStatus's own vocabulary
 * (queued/crawling/analyzing/completed/failed) — see
 * App\Enums\TechnicalSeoScanStatus, a genuinely SEPARATE enum rather
 * than reusing AuditStatus directly, since this feature's own pipeline
 * has different phases (no 'fetching'/'generating_report' distinction
 * the way the full Audit pipeline does) and is intentionally NOT
 * coupled to the Audit module's own job/status machinery — see
 * App\TechnicalSeo\Jobs\RunTechnicalSeoScanJob's own docblock for why
 * this phase uses its own lightweight job rather than extending
 * App\Audit\Jobs\AuditJob (that base class is tightly bound to the
 * `audits` table via AuditRepositoryInterface, not reusable here).
 *
 * $result is the ENTIRE scan output as one JSON blob (every section's
 * findings, the Core Web Vitals data, the Priority Fix List) — this
 * table intentionally has no separate normalized tables for
 * broken-links/redirects/etc; a Technical SEO scan is read as a whole
 * report, never queried by "find all scans with a broken link to X"
 * or similar, so normalizing further would add real complexity for no
 * actual query benefit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_seo_scans', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('domain');
            $table->string('status')->default('queued');
            $table->unsignedInteger('health_score')->nullable();
            $table->string('health_grade', 1)->nullable();
            $table->json('result')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_seo_scans');
    }
};