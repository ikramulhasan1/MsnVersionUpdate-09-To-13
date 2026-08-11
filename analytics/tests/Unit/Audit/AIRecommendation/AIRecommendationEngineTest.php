<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\AIRecommendation;

use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
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
 * Runs the real analyzers (not stubs) against a passing and a failing
 * FetchResult, then feeds their real output into AIRecommendationEngine
 * — this exercises the engine the way the queued job pipeline actually
 * will, rather than against hand-built AnalysisResults fixtures whose
 * shape could quietly drift from what the analyzers really produce.
 */
final class AIRecommendationEngineTest extends TestCase
{
    private function engine(): AIRecommendationEngine
    {
        return new AIRecommendationEngine;
    }

    private function analysisResultsFor(FetchResult $fetch): AnalysisResults
    {
        $page = $this->crawledPageFrom($fetch);

        return new AnalysisResults(
            url: $fetch->url,
            security: (new SecurityAnalyzer(new FakeSslInspector(new SslInfo(valid: true, daysUntilExpiry: 200))))->analyze($fetch),
            accessibility: (new AccessibilityAnalyzer)->analyze($fetch),
            content: (new ContentAnalyzer)->analyze($fetch),
            uiUx: (new UiUxAnalyzer)->analyze($fetch),
            performance: (new PerformanceAnalyzer)->analyze($page),
            businessOpportunity: (new BusinessOpportunityAnalyzer)->analyze($fetch),
            technology: (new TechnologyDetector)->detect($fetch),
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

    public function test_a_healthy_page_has_no_critical_issues_and_a_medium_priority(): void
    {
        // Priority is driven purely by severity counts (businessRecommendation():
        // any warning present => 'Medium', regardless of critical count). The
        // "healthy" fixture legitimately carries warnings even when nothing is
        // broken — content_freshness/blog_frequency (no dated schema/meta present)
        // and accessibility contrast/font_size (intentionally Warning absent inline
        // styles, per AccessibilityAnalyzerTest) — so 'Low' was never reachable
        // here; 'Medium' is the correct expectation for a page with zero critical
        // issues but a few honest warnings.
        $results = $this->analysisResultsFor(FetchResultFactory::make());
        $result = $this->engine()->analyze($results);

        $this->assertSame(0, $result->recommendations['executive_summary']['critical_count']);
        $this->assertSame('Medium', $result->recommendations['business_recommendation']['priority']);
    }

    public function test_a_broken_page_produces_critical_issues_and_a_high_priority(): void
    {
        $html = '<html><body><p>Way too little content on this page.</p></body></html>';

        $fetch = FetchResultFactory::make(
            url: 'http://insecure-example.com/',
            html: $html,
            wordCount: 20,
            includeDefaultSecurityHeaders: false,
        );

        $results = $this->analysisResultsFor($fetch);
        $result = $this->engine()->analyze($results);

        $summary = $result->recommendations['executive_summary'];
        $this->assertGreaterThan(0, $summary['critical_count']);
        $this->assertSame('High', $result->recommendations['business_recommendation']['priority']);
    }

    public function test_issue_priority_and_executive_summary_never_disagree_on_counts(): void
    {
        $fetch = FetchResultFactory::make(html: '', wordCount: 10, includeDefaultSecurityHeaders: false);
        $result = $this->engine()->analyze($this->analysisResultsFor($fetch));

        $summary = $result->recommendations['executive_summary'];
        $priority = $result->recommendations['issue_priority'];

        $this->assertSame($summary['total_issues'], $priority['total_issues']);
        $this->assertSame($summary['critical_count'], $priority['critical_count']);
        $this->assertSame($summary['warning_count'], $priority['warning_count']);
    }

    public function test_quick_wins_and_long_term_fixes_hours_sum_to_the_development_time_estimate(): void
    {
        $fetch = FetchResultFactory::make(html: '', wordCount: 10, includeDefaultSecurityHeaders: false);
        $result = $this->engine()->analyze($this->analysisResultsFor($fetch));

        $quickWins = $result->recommendations['quick_wins']['total_estimated_hours'];
        $longTerm = $result->recommendations['long_term_fixes']['total_estimated_hours'];
        $total = $result->recommendations['estimated_development_time']['total_estimated_hours'];

        $this->assertSame($quickWins['min'] + $longTerm['min'], $total['min']);
        $this->assertSame($quickWins['max'] + $longTerm['max'], $total['max']);
    }

    public function test_analyze_to_json_produces_valid_json_containing_every_category(): void
    {
        $json = $this->engine()->analyzeToJson($this->analysisResultsFor(FetchResultFactory::make()));

        $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            [
                'executive_summary',
                'issue_priority',
                'quick_wins',
                'long_term_fixes',
                'estimated_development_time',
                'business_recommendation',
                'estimated_cost',
                'recommended_services',
            ],
            array_keys($decoded['recommendations']),
        );
    }

    public function test_engine_tolerates_every_analyzer_result_being_absent(): void
    {
        $result = $this->engine()->analyze(new AnalysisResults(url: 'https://example.com/'));

        $this->assertSame(0, $result->recommendations['issue_priority']['total_issues']);
        $this->assertNull($result->recommendations['executive_summary']['overall_score']);
    }
}
