<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Lead;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\BusinessSignals\DTO\BusinessSignalsResult;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Lead\ProspectQualificationScorer;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use PHPUnit\Framework\TestCase;

final class ProspectQualificationScorerTest extends TestCase
{
    private function scorer(): ProspectQualificationScorer
    {
        return new ProspectQualificationScorer;
    }

    private function businessOpportunity(?int $score): BusinessOpportunityResult
    {
        return new BusinessOpportunityResult(
            url: 'https://example.com/',
            checks: [],
            score: $score,
            grade: null,
            summary: 'test fixture',
            analyzedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    /**
     * @param  array<string, bool>  $signals
     */
    private function businessSignals(array $signals): BusinessSignalsResult
    {
        return new BusinessSignalsResult(
            url: 'https://example.com/',
            signals: $signals,
            signalDetails: array_map(static fn (): ?string => null, $signals),
            analyzedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    private function upgradeOpportunity(string $slug): TechnologyUpgradeOpportunity
    {
        return new TechnologyUpgradeOpportunity(
            slug: $slug,
            technology: $slug,
            detectedVersion: '1.0',
            reason: 'test fixture',
            suggestedService: 'test upgrade',
        );
    }

    public function test_it_returns_null_when_there_is_no_business_opportunity_result(): void
    {
        $results = new AnalysisResults(url: 'https://example.com/');

        $this->assertNull($this->scorer()->score($results));
    }

    public function test_a_perfectly_healthy_site_with_no_signals_or_opportunities_scores_zero(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(100),
        );

        $result = $this->scorer()->score($results);

        $this->assertInstanceOf(ProspectQualificationResult::class, $result);
        $this->assertSame(0, $result->score);
        $this->assertSame('F', $result->grade);
        $this->assertSame('Low', $result->priority());
    }

    public function test_the_breakdown_always_sums_to_the_total_score(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(40),
            businessSignals: $this->businessSignals(['careers' => true, 'hiring' => false, 'blog_update' => true]),
            technologyUpgradeOpportunities: [$this->upgradeOpportunity('wordpress')],
        );

        $result = $this->scorer()->score($results);

        $this->assertSame(array_sum($result->breakdown), $result->score);
        $this->assertSame(
            ['website_issues', 'business_signals', 'technology_upgrade_opportunities'],
            array_keys($result->breakdown),
        );
    }

    public function test_website_issues_points_are_inverted_from_the_health_score(): void
    {
        // A lower health score (more real problems) is a *stronger*
        // sales lead, so points go up as health goes down.
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(40),
        );

        $result = $this->scorer()->score($results);

        // round((100 - 40) * (60 / 100)) = 36
        $this->assertSame(36, $result->breakdown['website_issues']);
    }

    public function test_a_null_health_score_contributes_zero_website_issue_points(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(null),
        );

        $result = $this->scorer()->score($results);

        $this->assertSame(0, $result->breakdown['website_issues']);
    }

    public function test_business_signals_points_scale_with_the_fraction_detected(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(100),
            businessSignals: $this->businessSignals([
                'careers' => true, 'hiring' => true, 'blog_update' => false, 'funding' => false, 'new_product' => false,
            ]),
        );

        $result = $this->scorer()->score($results);

        // round((2 / 5) * 25) = 10
        $this->assertSame(10, $result->breakdown['business_signals']);
    }

    public function test_missing_business_signals_contribute_zero_points(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(100),
        );

        $result = $this->scorer()->score($results);

        $this->assertSame(0, $result->breakdown['business_signals']);
    }

    public function test_technology_upgrade_points_are_five_per_opportunity(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(100),
            technologyUpgradeOpportunities: [
                $this->upgradeOpportunity('wordpress'),
                $this->upgradeOpportunity('jquery'),
            ],
        );

        $result = $this->scorer()->score($results);

        $this->assertSame(10, $result->breakdown['technology_upgrade_opportunities']);
    }

    public function test_technology_upgrade_points_are_capped_at_fifteen(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(100),
            technologyUpgradeOpportunities: [
                $this->upgradeOpportunity('wordpress'),
                $this->upgradeOpportunity('jquery'),
                $this->upgradeOpportunity('bootstrap'),
                $this->upgradeOpportunity('vue'),
            ],
        );

        $result = $this->scorer()->score($results);

        $this->assertSame(15, $result->breakdown['technology_upgrade_opportunities']);
    }

    public function test_priority_high_for_a_score_of_at_least_seventy_five(): void
    {
        $result = new ProspectQualificationResult(score: 75, grade: 'B', breakdown: [], summary: 's');

        $this->assertSame('High', $result->priority());
    }

    public function test_priority_medium_for_a_score_between_forty_five_and_seventy_four(): void
    {
        $result = new ProspectQualificationResult(score: 45, grade: 'D', breakdown: [], summary: 's');

        $this->assertSame('Medium', $result->priority());
    }

    public function test_priority_low_below_forty_five(): void
    {
        $result = new ProspectQualificationResult(score: 44, grade: 'F', breakdown: [], summary: 's');

        $this->assertSame('Low', $result->priority());
    }

    public function test_result_serializes_to_the_expected_json_shape_including_priority(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessOpportunity: $this->businessOpportunity(50),
        );

        $result = $this->scorer()->score($results);

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['score', 'grade', 'breakdown', 'summary', 'priority'], array_keys($decoded));
    }
}
