<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Outreach;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\Contacts\DTO\ContactInfoResult;
use App\Audit\Enums\BusinessOpportunityCheckStatus;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Outreach\OutreachDraftGenerator;
use PHPUnit\Framework\TestCase;

final class OutreachDraftGeneratorTest extends TestCase
{
    private function generator(): OutreachDraftGenerator
    {
        return new OutreachDraftGenerator;
    }

    /**
     * @param  array<string, array<int, WebsiteHealthIssue>>  $websiteHealth
     */
    private function businessOpportunity(array $websiteHealth): BusinessOpportunityResult
    {
        return new BusinessOpportunityResult(
            url: 'https://example.com/',
            checks: [],
            score: 50,
            grade: 'C',
            summary: 'test fixture',
            analyzedAt: '2026-01-01T00:00:00+00:00',
            websiteHealth: $websiteHealth,
        );
    }

    private function issue(string $name, SeoSeverity $severity, ?string $recommendation = null): WebsiteHealthIssue
    {
        return new WebsiteHealthIssue(
            issue: $name,
            status: BusinessOpportunityCheckStatus::FAIL,
            severity: $severity,
            recommendation: $recommendation,
        );
    }

    /**
     * @param  array<int, array{name: string, title: ?string, linkedinUrl: ?string, sourceUrl: string}>  $teamMembers
     */
    private function contactInfo(array $teamMembers): ContactInfoResult
    {
        return new ContactInfoResult(
            url: 'https://example.com/',
            emails: [],
            phones: [],
            socialProfiles: [],
            teamMembers: $teamMembers,
            analyzedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    public function test_it_returns_null_when_there_is_no_business_opportunity_result(): void
    {
        $results = new AnalysisResults(url: 'https://example.com/');

        $this->assertNull($this->generator()->generate($results, null));
    }

    public function test_it_returns_null_when_there_are_no_real_issues_to_personalize_against(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([]),
        );

        // A generic, non-personalized template is worse than no draft.
        $this->assertNull($this->generator()->generate($results, null));
    }

    public function test_it_builds_a_draft_referencing_real_issues(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL, 'Add a title tag.')],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertNotNull($draft);
        $this->assertSame(['Missing Title Tag'], $draft->basedOnIssues);
        $this->assertStringContainsString('Missing Title Tag', $draft->body);
        $this->assertStringContainsString('Add a title tag.', $draft->body);
        $this->assertStringContainsString('1 quick issue', $draft->subject);
    }

    public function test_subject_pluralizes_issue_for_more_than_one(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [
                    $this->issue('Missing Title Tag', SeoSeverity::CRITICAL),
                    $this->issue('Missing Meta Description', SeoSeverity::WARNING),
                ],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertStringContainsString('2 quick issues', $draft->subject);
    }

    public function test_issues_are_referenced_most_severe_first_and_capped_at_three(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [
                    $this->issue('Notice Issue', SeoSeverity::NOTICE),
                    $this->issue('Critical Issue', SeoSeverity::CRITICAL),
                    $this->issue('Warning Issue', SeoSeverity::WARNING),
                ],
                'performance_issues' => [
                    $this->issue('Another Critical Issue', SeoSeverity::CRITICAL),
                ],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertCount(3, $draft->basedOnIssues);
        $this->assertContains('Critical Issue', $draft->basedOnIssues);
        $this->assertContains('Another Critical Issue', $draft->basedOnIssues);
        $this->assertContains('Warning Issue', $draft->basedOnIssues);
        $this->assertNotContains('Notice Issue', $draft->basedOnIssues);
    }

    public function test_the_company_label_is_the_audited_urls_host_without_www(): void
    {
        $results = new AnalysisResults(
            url: 'https://www.example.com/pricing',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertStringContainsString('example.com', $draft->subject);
        $this->assertStringNotContainsString('www.example.com', $draft->subject);
    }

    public function test_it_greets_a_known_contact_by_name(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
            contactInfo: $this->contactInfo([
                ['name' => 'Jamie Rivera', 'title' => 'Head of Sales', 'linkedinUrl' => null, 'sourceUrl' => 'https://example.com/team'],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertStringContainsString('Hi Jamie Rivera,', $draft->body);
    }

    public function test_it_falls_back_to_a_generic_greeting_with_no_known_contact(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $this->assertStringContainsString('Hi there,', $draft->body);
    }

    public function test_a_high_priority_qualification_produces_a_call_to_action_closing(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
        );

        $qualification = new ProspectQualificationResult(score: 90, grade: 'A', breakdown: [], summary: 's');

        $draft = $this->generator()->generate($results, $qualification);

        $this->assertStringContainsString('quick call this week', $draft->body);
    }

    public function test_a_non_high_priority_qualification_produces_a_softer_closing(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
        );

        $qualification = new ProspectQualificationResult(score: 20, grade: 'F', breakdown: [], summary: 's');

        $draft = $this->generator()->generate($results, $qualification);

        $this->assertStringNotContainsString('quick call this week', $draft->body);
        $this->assertStringContainsString('Happy to share more detail', $draft->body);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity([
                'seo_issues' => [$this->issue('Missing Title Tag', SeoSeverity::CRITICAL)],
            ]),
        );

        $draft = $this->generator()->generate($results, null);

        $decoded = json_decode(json_encode($draft, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'subject', 'body', 'based_on_issues'], array_keys($decoded));
    }
}
