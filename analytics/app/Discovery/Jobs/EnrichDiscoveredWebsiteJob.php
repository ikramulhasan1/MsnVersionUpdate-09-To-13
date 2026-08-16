<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Technology\TechnologyDetector;
use App\Discovery\Jobs\Concerns\BuildsSinglePageCrawlResult;
use App\Models\DiscoveredWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * The bridge between Website Discovery and the existing Audit engine —
 * runs the same five analyzers a real audit uses
 * (SeoAnalyzerServiceInterface, PerformanceAnalyzer, SecurityAnalyzer,
 * AccessibilityAnalyzer, TechnologyDetector), completely unmodified,
 * against ONLY the homepage — never the full multi-page crawl,
 * PDF/Excel export, or any of the other Audit-pipeline jobs
 * (FetchAndCrawlJob/AnalyzeChunkJob/AssembleAnalysisResultsJob) a real
 * audit runs. A discovered site's row needs just enough signal to be
 * searchable/filterable by score, grade, and technology — not a full
 * audit report — so this job is deliberately a lightweight, one-page,
 * one-job "quick scan" instead of reusing the full pipeline.
 *
 * Writes ONLY score + grade (seo/performance/security/accessibility)
 * and technology stack (cms/framework/ecommerce_platform/server/cdn)
 * onto the DiscoveredWebsite row — no other column (website_type,
 * business_size, country/region/city, contact info, domain age, ...)
 * is touched here; those belong to other enrichment steps this job
 * doesn't attempt. mobile_score/opportunity_score also stay untouched:
 * no analyzer this job runs produces either as a distinct value.
 *
 * The CrawledPage/CrawlResult-building and technology-column-value
 * helpers this job needs are shared with
 * App\Discovery\Jobs\MonitorWatchlistChangesJob (Phase G2) via
 * App\Discovery\Jobs\Concerns\BuildsSinglePageCrawlResult — see that
 * trait's own docblock; this job's own copies of those methods were
 * extracted there once a second job needed the exact same logic.
 *
 * A future Phase I3 (App\Discovery\Sources\InternalCrawlSource, not
 * built yet) is expected to dispatch this job automatically whenever
 * it discovers a new domain; nothing dispatches it automatically today
 * — call EnrichDiscoveredWebsiteJob::dispatch($website) directly (e.g.
 * from a controller action or artisan command) until that source
 * exists.
 */
final class EnrichDiscoveredWebsiteJob implements ShouldQueue
{
    use BuildsSinglePageCrawlResult;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly DiscoveredWebsite $website,
    ) {}

    public function handle(
        WebsiteFetcherServiceInterface $fetcher,
        SeoAnalyzerServiceInterface $seoAnalyzer,
        PerformanceAnalyzer $performanceAnalyzer,
        SecurityAnalyzer $securityAnalyzer,
        AccessibilityAnalyzer $accessibilityAnalyzer,
        TechnologyDetector $technologyDetector,
    ): void {
        $fetch = $fetcher->fetch($this->website->url);

        if (! $fetch->success) {
            // A failed homepage fetch has nothing to score — leave the
            // row exactly as it was rather than writing zeros/nulls
            // over any signal a previous successful enrichment already
            // found. App\Discovery\Jobs\MonitorWatchlistChangesJob
            // (Phase G2) is what now records "we tried and it failed"
            // via discovered_websites.connectivity_status, not this job.
            return;
        }

        $page = $this->crawledPageFrom($fetch);

        $seoResult = $seoAnalyzer->analyze($this->singlePageCrawlResult($fetch, $page));
        $performanceResult = $performanceAnalyzer->analyze($page);
        $securityResult = $securityAnalyzer->analyze($fetch);
        $accessibilityResult = $accessibilityAnalyzer->analyze($fetch);
        $technologyResult = $technologyDetector->detect($fetch);

        $this->website->update([
            'seo_score' => $seoResult->averageScore,
            'seo_grade' => $this->gradeFor($seoResult->averageScore),
            'performance_score' => $performanceResult->score,
            'performance_grade' => $performanceResult->grade,
            'security_score' => $securityResult->score,
            'security_grade' => $securityResult->grade,
            'accessibility_score' => $accessibilityResult->score,
            'accessibility_grade' => $accessibilityResult->grade,
            'cms' => $this->technologyColumnValue($technologyResult, ['CMS']),
            'framework' => $this->technologyColumnValue(
                $technologyResult,
                ['Backend Framework', 'JavaScript Framework', 'CSS Framework'],
            ),
            'ecommerce_platform' => $this->technologyColumnValue($technologyResult, ['Ecommerce']),
            'server' => $technologyResult->serverHeader,
            'cdn' => $this->technologyColumnValue($technologyResult, ['Infrastructure']),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }

    /**
     * SeoAuditResult has no grade of its own (unlike Security/
     * Accessibility/Performance, whose analyzers already compute one) —
     * mirrors the exact A/B/C/D/F thresholds every one of those
     * analyzers' own grade() methods already use by default
     * (gradeAThreshold=90/gradeBThreshold=75/gradeCThreshold=60/
     * gradeDThreshold=40), so an SEO grade means the same thing as a
     * Security/Accessibility/Performance grade at the same score.
     */
    private function gradeFor(int $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 40 => 'D',
            default => 'F',
        };
    }
}
