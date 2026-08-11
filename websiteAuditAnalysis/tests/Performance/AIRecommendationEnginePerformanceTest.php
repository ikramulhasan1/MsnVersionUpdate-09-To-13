<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\BusinessOpportunity\BusinessOpportunityAnalyzer;
use App\Audit\Content\ContentAnalyzer;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Technology\TechnologyDetector;
use App\Audit\UiUx\UiUxAnalyzer;
use App\Audit\Validation\DTO\SslInfo;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeSslInspector;
use Tests\Support\FetchResultFactory;

/**
 * AIRecommendationEngine (964 lines, the largest single class in the
 * pipeline) walks every analyzer's DTO tree to build the executive
 * summary, priority issues, quick wins, and cost/time estimates. It
 * runs synchronously inside AssembleAnalysisResultsJob's request path
 * (not queued separately), so a regression here directly extends how
 * long a user waits for "Generating report" to finish.
 *
 * These tests assert wall-clock and memory budgets rather than exact
 * timings, since absolute numbers vary by hardware/CI load — the
 * point is to catch accidental O(n^2) passes or unbounded growth, not
 * to benchmark precisely.
 */
final class AIRecommendationEnginePerformanceTest extends TestCase
{
    private function engine(): AIRecommendationEngine
    {
        return new AIRecommendationEngine();
    }

    private function analysisResultsFor(FetchResult $fetch): AnalysisResults
    {
        $page = $this->crawledPageFrom($fetch);

        return new AnalysisResults(
            url: $fetch->url,
            security: (new SecurityAnalyzer(new FakeSslInspector(new SslInfo(valid: true, daysUntilExpiry: 200))))->analyze($fetch),
            accessibility: (new AccessibilityAnalyzer())->analyze($fetch),
            content: (new ContentAnalyzer())->analyze($fetch),
            uiUx: (new UiUxAnalyzer())->analyze($fetch),
            performance: (new PerformanceAnalyzer())->analyze($page),
            businessOpportunity: (new BusinessOpportunityAnalyzer())->analyze($fetch),
            technology: (new TechnologyDetector())->detect($fetch),
            seo: null,
        );
    }

    private function crawledPageFrom(FetchResult $fetch): CrawledPage
    {
        return new CrawledPage(
            url: $fetch->url,
            depth: 0,
            success: $fetch->success,
            finalUrl: $fetch->finalUrl,
            statusCode: $fetch->statusCode,
            redirectChain: $fetch->redirectChain,
            meta: $fetch->meta,
            title: $fetch->meta?->title,
            canonical: $fetch->meta?->canonical,
            noIndex: false,
            noFollow: false,
            anchors: $fetch->anchors,
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: $fetch->images,
            cssAssets: $fetch->cssLinks,
            jsAssets: $fetch->jsLinks,
            fontAssets: $fetch->fonts,
            headings: $fetch->headings,
            schema: $fetch->schema,
            wordCount: $fetch->wordCount,
            responseTimeMs: $fetch->responseTimeMs,
            errors: $fetch->errors,
        );
    }

    /**
     * A page with a large number of images (all missing alt text) and
     * a large word count. AccessibilityAnalyzer and ContentAnalyzer
     * both iterate these collections, and AIRecommendationEngine then
     * iterates *their* output again while building priority issues —
     * this is the shape most likely to expose an accidental nested
     * loop (O(images) inside O(issues) inside O(recommendations)).
     */
    private function largePageHtml(int $imageCount, int $paragraphCount): string
    {
        $images = str_repeat('<img src="https://example.com/i.jpg">', $imageCount);
        $paragraphs = str_repeat('<p>' . str_repeat('word ', 50) . '</p>', $paragraphCount);

        return '<html><head><title>Large Page</title></head><body>'
            . '<nav><a href="/">Home</a></nav>'
            . $images
            . $paragraphs
            . '</body></html>';
    }

    public function test_engine_completes_within_time_budget_for_a_healthy_page(): void
    {
        $fetch = FetchResultFactory::make(wordCount: 900);
        $results = $this->analysisResultsFor($fetch);

        $start = microtime(true);
        $recommendation = $this->engine()->analyze($results);
        $elapsedMs = (microtime(true) - $start) * 1000;

        self::assertNotNull($recommendation);
        // Generous budget for a synchronous, in-request DTO-tree walk
        // with no I/O — this is meant to catch regressions of an order
        // of magnitude, not to enforce a tight SLA.
        self::assertLessThan(500, $elapsedMs, "AIRecommendationEngine took {$elapsedMs}ms on a healthy page (budget: 500ms).");
    }

    public function test_engine_completes_within_time_budget_for_a_large_broken_page(): void
    {
        // 500 unlabeled images + 200 paragraphs: worst case for
        // AccessibilityAnalyzer/ContentAnalyzer issue volume, which
        // is what the engine's priority-issue ranking has to sort
        // through.
        $fetch = FetchResultFactory::make(
            html: $this->largePageHtml(imageCount: 500, paragraphCount: 200),
            headers: [], // drop default security headers too, to also maximize SecurityAnalyzer findings
            includeDefaultSecurityHeaders: false,
            wordCount: 200,
        );
        $results = $this->analysisResultsFor($fetch);

        $memBefore = memory_get_usage();
        $start = microtime(true);
        $recommendation = $this->engine()->analyze($results);
        $elapsedMs = (microtime(true) - $start) * 1000;
        $memUsedMb = (memory_get_usage() - $memBefore) / 1_048_576;

        self::assertNotNull($recommendation);
        self::assertLessThan(1500, $elapsedMs, "AIRecommendationEngine took {$elapsedMs}ms on a large broken page (budget: 1500ms).");
        self::assertLessThan(64, $memUsedMb, sprintf('AIRecommendationEngine used %.2fMB on a large broken page (budget: 64MB).', $memUsedMb));
    }

    /**
     * Running the engine back-to-back on many independent audits (as
     * a queue worker would across jobs) should scale roughly linearly
     * — a big jump between the per-run averages of the first and
     * second half would suggest static/shared state leaking across
     * instances (e.g. an accumulating array on a shared property).
     */
    public function test_repeated_runs_do_not_regress_in_average_time(): void
    {
        $fetch = FetchResultFactory::make(wordCount: 900);
        $results = $this->analysisResultsFor($fetch);

        $timings = [];
        for ($i = 0; $i < 20; $i++) {
            $start = microtime(true);
            $this->engine()->analyze($results);
            $timings[] = microtime(true) - $start;
        }

        $firstHalfAvg = array_sum(array_slice($timings, 0, 10)) / 10;
        $secondHalfAvg = array_sum(array_slice($timings, 10, 10)) / 10;

        // Allow generous variance for CI noise; only fail on a
        // multi-fold blowup that indicates accumulating state.
        self::assertLessThan(
            $firstHalfAvg * 5 + 0.05,
            $secondHalfAvg,
            'Average analyze() time grew more than 5x between the first and second batch of 10 runs — possible shared/accumulating state.'
        );
    }
}
