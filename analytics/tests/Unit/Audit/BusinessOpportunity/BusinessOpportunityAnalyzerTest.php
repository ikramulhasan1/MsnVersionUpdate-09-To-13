<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\BusinessOpportunity;

use App\Audit\BusinessOpportunity\BusinessOpportunityAnalyzer;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\Enums\BusinessOpportunityCheckStatus;
use App\Audit\Fetching\DTO\ImageAsset;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class BusinessOpportunityAnalyzerTest extends TestCase
{
    private function analyzer(): BusinessOpportunityAnalyzer
    {
        return new BusinessOpportunityAnalyzer;
    }

    public function test_healthy_page_has_no_website_problem_failures(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        foreach ($result->websiteHealth['website_problems'] as $issue) {
            $this->assertNotSame(BusinessOpportunityCheckStatus::FAIL, $issue->status);
        }
    }

    public function test_page_fetch_errors_fail_the_page_fetch_errors_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(errors: ['Connection timed out']));

        $issue = $this->findIssue($result->websiteHealth['website_problems'], 'Page Fetch Errors');

        $this->assertNotNull($issue);
        $this->assertSame(BusinessOpportunityCheckStatus::FAIL, $issue->status);
    }

    public function test_non_200_status_code_fails_the_http_status_code_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(statusCode: 500));

        $issue = $this->findIssue($result->websiteHealth['website_problems'], 'HTTP Status Code');

        $this->assertNotNull($issue);
        $this->assertSame(BusinessOpportunityCheckStatus::FAIL, $issue->status);
    }

    public function test_redirect_status_code_only_warns(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(statusCode: 301));

        $issue = $this->findIssue($result->websiteHealth['website_problems'], 'HTTP Status Code');

        $this->assertNotNull($issue);
        $this->assertSame(BusinessOpportunityCheckStatus::WARNING, $issue->status);
    }

    public function test_checks_are_intentionally_empty_so_score_and_grade_stay_null(): void
    {
        // BusinessOpportunityAnalyzer's own `checks` (as opposed to
        // `websiteHealth`) has no entries implemented yet — score/grade
        // are null rather than a fabricated number until that changes.
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $this->assertSame([], $result->checks);
        $this->assertNull($result->score);
        $this->assertNull($result->grade);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['url', 'checks', 'score', 'grade', 'summary', 'analyzed_at', 'website_health', 'business_opportunity_score', 'sales_opportunity', 'outreach_message'],
            array_keys($decoded),
        );
        $websiteProblemIssue = $decoded['website_health']['website_problems'][0];
        $this->assertSame(
            ['issue', 'status', 'severity', 'recommendation', 'page_url', 'element_url'],
            array_keys($websiteProblemIssue),
        );
    }

    public function test_every_website_health_issue_carries_the_page_url_it_was_found_on(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(url: 'https://example.com/pricing'));

        foreach ($result->websiteHealth as $issues) {
            foreach ($issues as $issue) {
                $this->assertSame('https://example.com/pricing', $issue->pageUrl);
            }
        }
    }

    public function test_image_dimensions_issue_reports_the_offending_images_url(): void
    {
        $image = new ImageAsset(url: 'https://example.com/hero.png', alt: 'Hero', width: null, height: null);

        $result = $this->analyzer()->analyze(FetchResultFactory::make(images: [$image]));

        $issue = $this->findIssue($result->websiteHealth['performance_issues'], 'Image Dimensions');

        $this->assertNotNull($issue);
        $this->assertSame(BusinessOpportunityCheckStatus::WARNING, $issue->status);
        $this->assertSame('https://example.com/hero.png', $issue->elementUrl);
    }

    public function test_analyze_all_merges_website_problems_seo_and_performance_issues_across_pages_with_their_page_urls(): void
    {
        $pageA = FetchResultFactory::make(url: 'https://example.com/a', statusCode: 500);
        $pageB = FetchResultFactory::make(url: 'https://example.com/b');

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/a' => $pageA, 'https://example.com/b' => $pageB],
            'https://example.com/a',
        );

        $statusIssues = array_values(array_filter(
            $result->websiteHealth['website_problems'],
            static fn ($issue): bool => $issue->issue === 'HTTP Status Code',
        ));

        // One "HTTP Status Code" issue per analyzed page.
        $this->assertCount(2, $statusIssues);

        $pageUrls = array_map(static fn ($issue) => $issue->pageUrl, $statusIssues);
        $this->assertEqualsCanonicalizing(['https://example.com/a', 'https://example.com/b'], $pageUrls);

        $failingIssue = $this->findIssueForPage($statusIssues, 'https://example.com/a');
        $this->assertSame(BusinessOpportunityCheckStatus::FAIL, $failingIssue->status);

        $passingIssue = $this->findIssueForPage($statusIssues, 'https://example.com/b');
        $this->assertSame(BusinessOpportunityCheckStatus::PASS, $passingIssue->status);
    }

    public function test_analyze_all_skips_pages_that_failed_to_fetch(): void
    {
        $ok = FetchResultFactory::make(url: 'https://example.com/ok');
        $failed = FetchResultFactory::make(url: 'https://example.com/broken', success: false, errors: ['Fetch failed']);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/ok' => $ok, 'https://example.com/broken' => $failed],
            'https://example.com/broken',
        );

        $pageUrls = array_map(
            static fn ($issue) => $issue->pageUrl,
            $result->websiteHealth['website_problems'],
        );

        $this->assertContains('https://example.com/ok', $pageUrls);
        $this->assertNotContains('https://example.com/broken', $pageUrls);
    }

    public function test_analyze_all_bases_score_and_outreach_on_the_entry_page(): void
    {
        $pageA = FetchResultFactory::make(url: 'https://example.com/a');
        $pageB = FetchResultFactory::make(url: 'https://example.com/b');

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/a' => $pageA, 'https://example.com/b' => $pageB],
            'https://example.com/a',
        );

        $this->assertSame('https://example.com/a', $result->url);
    }

    /**
     * @param  array<int, WebsiteHealthIssue>  $issues
     */
    private function findIssue(array $issues, string $check): ?object
    {
        foreach ($issues as $issue) {
            if ($issue->issue === $check) {
                return $issue;
            }
        }

        return null;
    }

    /**
     * @param  array<int, WebsiteHealthIssue>  $issues
     */
    private function findIssueForPage(array $issues, string $pageUrl): object
    {
        foreach ($issues as $issue) {
            if ($issue->pageUrl === $pageUrl) {
                return $issue;
            }
        }

        throw new \RuntimeException("No issue found for page {$pageUrl}");
    }
}
