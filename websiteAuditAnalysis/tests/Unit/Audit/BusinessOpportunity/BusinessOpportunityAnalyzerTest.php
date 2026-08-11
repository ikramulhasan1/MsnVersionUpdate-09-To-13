<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\BusinessOpportunity;

use App\Audit\BusinessOpportunity\BusinessOpportunityAnalyzer;
use App\Audit\Enums\BusinessOpportunityCheckStatus;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class BusinessOpportunityAnalyzerTest extends TestCase
{
    private function analyzer(): BusinessOpportunityAnalyzer
    {
        return new BusinessOpportunityAnalyzer();
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
    }

    /**
     * @param array<int, \App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue> $issues
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
}
