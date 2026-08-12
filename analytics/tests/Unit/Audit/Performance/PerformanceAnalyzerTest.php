<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Performance;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Performance\DTO\PerformanceAuditResult;
use App\Audit\Performance\PageSpeedInsightsClient;
use App\Audit\Performance\PerformanceAnalyzer;
use PHPUnit\Framework\TestCase;

final class PerformanceAnalyzerTest extends TestCase
{
    private function analyzer(?PageSpeedInsightsClient $pageSpeedClient = null): PerformanceAnalyzer
    {
        return new PerformanceAnalyzer(pageSpeedClient: $pageSpeedClient);
    }

    private function page(
        string $url = 'https://example.com/',
        bool $success = true,
        int $wordCount = 500,
        ?int $responseTimeMs = 300,
    ): CrawledPage {
        return new CrawledPage(
            url: $url,
            depth: 0,
            success: $success,
            finalUrl: $url,
            statusCode: $success ? 200 : null,
            redirectChain: [],
            meta: null,
            title: 'Example',
            canonical: $url,
            noIndex: false,
            noFollow: false,
            anchors: [],
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: [],
            cssAssets: [],
            jsAssets: [],
            fontAssets: [],
            headings: [],
            schema: [],
            wordCount: $wordCount,
            responseTimeMs: $responseTimeMs,
            errors: $success ? [] : ['Fetch failed'],
        );
    }

    /**
     * @param array<int, CrawledPage> $pages
     */
    private function crawlResult(array $pages, string $startUrl = 'https://example.com/'): CrawlResult
    {
        return new CrawlResult(
            startUrl: $startUrl,
            origin: 'https://example.com',
            pages: $pages,
            internalPages: [],
            externalLinks: [],
            brokenLinks: [],
            maxDepth: 2,
            maxPages: 25,
            truncated: false,
            durationMs: 1000,
            crawledAt: '2026-01-01T00:00:00+00:00',
        );
    }

    public function test_lightweight_fast_page_scores_well(): void
    {
        $result = $this->analyzer()->analyze($this->page(wordCount: 400, responseTimeMs: 200));

        $this->assertSame('good', $result->metrics['html_size']['status']);
        $this->assertSame('good', $result->metrics['ttfb']['status']);
        $this->assertGreaterThanOrEqual(60, $result->score);
    }

    public function test_very_heavy_html_and_slow_ttfb_are_flagged_critical(): void
    {
        $result = $this->analyzer()->analyze($this->page(wordCount: 5000, responseTimeMs: 3000));

        $this->assertSame('critical', $result->metrics['html_size']['status']);
        $this->assertSame('critical', $result->metrics['ttfb']['status']);
        $this->assertLessThan(100, $result->score);
    }

    public function test_metrics_not_determinable_from_crawled_page_are_reported_as_unknown(): void
    {
        $result = $this->analyzer()->analyze($this->page());

        $this->assertSame('unknown', $result->metrics['compression']['status']);
        $this->assertSame('unknown', $result->metrics['caching']['status']);
        $this->assertNull($result->metrics['compression']['value']);
    }

    public function test_missing_response_time_does_not_crash_the_ttfb_check(): void
    {
        $result = $this->analyzer()->analyze($this->page(responseTimeMs: null));

        $this->assertArrayHasKey('ttfb', $result->metrics);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze($this->page());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'score', 'grade', 'summary', 'metrics', 'analyzed_at'], array_keys($decoded));
    }

    public function test_lcp_metric_carries_the_affected_resource_when_pagespeed_reports_one(): void
    {
        $pageSpeedMetrics = [
            'lcp_ms' => 3200.0,
            'cls' => 0.05,
            'tbt_ms' => 90.0,
            'lcp_resource' => 'https://example.com/hero.jpg',
            'cls_resource' => 'html > body > div.banner',
            'tbt_resource' => 'https://example.com/analytics.js',
        ];

        $lcp = $this->invokeCheck('checkLcp', $pageSpeedMetrics);
        $cls = $this->invokeCheck('checkCls', $pageSpeedMetrics);
        $fid = $this->invokeCheck('checkFid', $pageSpeedMetrics);

        $this->assertSame('https://example.com/hero.jpg', $lcp['affected_resource']);
        $this->assertSame('html > body > div.banner', $cls['affected_resource']);
        $this->assertSame('https://example.com/analytics.js', $fid['affected_resource']);
    }

    public function test_lcp_metric_affected_resource_is_null_when_pagespeed_did_not_report_one(): void
    {
        $pageSpeedMetrics = [
            'lcp_ms' => 3200.0,
            'cls' => 0.05,
            'tbt_ms' => 90.0,
            'lcp_resource' => null,
            'cls_resource' => null,
            'tbt_resource' => null,
        ];

        $lcp = $this->invokeCheck('checkLcp', $pageSpeedMetrics);

        $this->assertArrayHasKey('affected_resource', $lcp);
        $this->assertNull($lcp['affected_resource']);
    }

    public function test_lcp_metric_has_no_affected_resource_key_when_pagespeed_data_is_unavailable(): void
    {
        $lcp = $this->invokeCheck('checkLcp', null);

        $this->assertSame('unknown', $lcp['status']);
        $this->assertArrayNotHasKey('affected_resource', $lcp);
    }

    /**
     * Calls one of PerformanceAnalyzer's private checkLcp/checkCls/checkFid
     * methods directly via reflection, bypassing analyze() (and therefore
     * the config('audit.pagespeed.enabled') gate inside
     * fetchPageSpeedMetrics(), which these plain PHPUnit unit tests have
     * no Laravel application container to resolve) so the metric-shaping
     * logic itself can be tested in isolation.
     *
     * @param ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float, lcp_resource: ?string, cls_resource: ?string, tbt_resource: ?string} $pageSpeedMetrics
     * @return array<string, mixed>
     */
    private function invokeCheck(string $method, ?array $pageSpeedMetrics): array
    {
        $reflection = new \ReflectionMethod(PerformanceAnalyzer::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($this->analyzer(), $pageSpeedMetrics);
    }

    public function test_analyze_all_runs_every_successful_page_and_skips_failed_ones(): void
    {
        $ok = $this->page(url: 'https://example.com/ok', wordCount: 400, responseTimeMs: 200);
        $failed = $this->page(url: 'https://example.com/broken', success: false);

        $result = $this->analyzer()->analyzeAll($this->crawlResult([$ok, $failed]));

        $this->assertInstanceOf(PerformanceAuditResult::class, $result);
        $this->assertCount(1, $result->pages);
        $this->assertArrayHasKey('https://example.com/ok', $result->pages);
        $this->assertArrayNotHasKey('https://example.com/broken', $result->pages);
        $this->assertSame(1, $result->pagesAnalyzed);
    }

    public function test_analyze_all_computes_the_average_score_across_pages(): void
    {
        $light = $this->page(url: 'https://example.com/light', wordCount: 400, responseTimeMs: 200);
        $heavy = $this->page(url: 'https://example.com/heavy', wordCount: 5000, responseTimeMs: 3000);

        $result = $this->analyzer()->analyzeAll($this->crawlResult([$light, $heavy]));

        $lightScore = $result->pages['https://example.com/light']->score;
        $heavyScore = $result->pages['https://example.com/heavy']->score;

        $this->assertNotNull($lightScore);
        $this->assertNotNull($heavyScore);
        $this->assertSame(
            (int) round(($lightScore + $heavyScore) / 2),
            $result->averageScore,
        );
    }

    public function test_analyze_all_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyzeAll($this->crawlResult([$this->page()]));

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['start_url', 'summary', 'pages', 'analyzed_at'], array_keys($decoded));
        $this->assertSame(['pages_analyzed', 'average_score'], array_keys($decoded['summary']));
        $this->assertArrayHasKey('https://example.com/', $decoded['pages']);
    }
}