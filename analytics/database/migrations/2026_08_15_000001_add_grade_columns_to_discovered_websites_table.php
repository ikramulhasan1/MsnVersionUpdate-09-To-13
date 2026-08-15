<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase D0 (enrichment pipeline).
 *
 * Phase A1's original migration (2026_08_14_000000_create_discovered_websites_table.php)
 * added a *_score column for each analyzer (seo_score, performance_score,
 * security_score, accessibility_score, ...) but no matching *_grade
 * column — that gap only became concrete once
 * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob (which writes both a
 * score AND a letter grade per analyzer, mirroring what
 * SecurityAnalyzer/AccessibilityAnalyzer/PerformanceAnalyzer already
 * return from their own analyze() calls) was being built.
 *
 * Only seo/performance/security/accessibility get a grade column here:
 * those are the four analyzers EnrichDiscoveredWebsiteJob actually runs
 * (see that job's own docblock for why — Technology has no numeric
 * score/grade of its own, and mobile_score/opportunity_score are
 * existing columns this job deliberately does not populate, since no
 * analyzer in its lightweight homepage-only scan produces either).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->string('seo_grade', 1)->nullable()->after('seo_score');
            $table->string('performance_grade', 1)->nullable()->after('performance_score');
            $table->string('security_grade', 1)->nullable()->after('security_score');
            $table->string('accessibility_grade', 1)->nullable()->after('accessibility_score');
        });
    }

    public function down(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->dropColumn(['seo_grade', 'performance_grade', 'security_grade', 'accessibility_grade']);
        });
    }
};