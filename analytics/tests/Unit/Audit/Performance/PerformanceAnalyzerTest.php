<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Performance;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Performance\PerformanceAnalyzer;
use PHPUnit\Framework\TestCase;

final class PerformanceAnalyzerTest extends TestCase
{
    private function analyzer(): PerformanceAnalyzer
    {
        return new PerformanceAnalyzer();
    }

    private function page(int $wordCount = 500, ?int $responseTimeMs = 300): CrawledPage
    {
        return new CrawledPage(
            url: 'https://example.com/',
            depth: 0,
            success: true,
            finalUrl: 'https://example.com/',
            statusCode: 200,
            redirectChain: [],
            meta: null,
            title: 'Example',
            canonical: 'https://example.com/',
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
            errors: [],
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
}
