<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Export\Support;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessSignals\DTO\BusinessSignalsResult;
use App\Audit\Contacts\DTO\ContactInfoResult;
use App\Audit\Export\Support\AnalysisResultsToDashboardCategories;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\ReviewPresence\DTO\ReviewPresenceResult;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use PHPUnit\Framework\TestCase;

final class AnalysisResultsToDashboardCategoriesLeadIntelligenceTest extends TestCase
{
    private function mapper(): AnalysisResultsToDashboardCategories
    {
        return new AnalysisResultsToDashboardCategories;
    }

    /**
     * @return array<int, array{key: string, abbr: string, label: string, score: ?int, grade: ?string, summary: string, checks: array<int, array{name: string, status: string, note: ?string}>, recommendations: array<int, string>}>
     */
    private function categories(AnalysisResults $results): array
    {
        return $this->mapper()->categories($results);
    }

    private function leadCard(AnalysisResults $results): ?array
    {
        foreach ($this->categories($results) as $category) {
            if ($category['key'] === 'lead_intelligence') {
                return $category;
            }
        }

        return null;
    }

    public function test_no_lead_intelligence_card_is_rendered_when_nothing_has_run_yet(): void
    {
        $results = new AnalysisResults(url: 'https://example.com/');

        $this->assertNull($this->leadCard($results));
    }

    public function test_a_card_is_rendered_once_at_least_one_lead_intelligence_input_exists(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            reviewPresence: new ReviewPresenceResult(
                url: 'https://example.com/',
                platforms: ['clutch' => null, 'g2' => null, 'goodfirms' => null, 'google' => null],
                analyzedAt: '2026-01-01T00:00:00+00:00',
            ),
        );

        $this->assertNotNull($this->leadCard($results));
    }

    public function test_the_card_uses_the_prospect_qualification_score_grade_and_summary(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            prospectQualification: new ProspectQualificationResult(
                score: 61,
                grade: 'C',
                breakdown: ['website_issues' => 36, 'business_signals' => 15, 'technology_upgrade_opportunities' => 10],
                summary: 'Prospect qualification score 61/100 (grade C).',
            ),
        );

        $card = $this->leadCard($results);

        $this->assertSame(61, $card['score']);
        $this->assertSame('C', $card['grade']);
        $this->assertSame('Prospect qualification score 61/100 (grade C).', $card['summary']);
    }

    public function test_business_signals_are_rendered_as_pass_or_warning_checks_never_fail(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            businessSignals: new BusinessSignalsResult(
                url: 'https://example.com/',
                signals: ['careers' => true, 'hiring' => false],
                signalDetails: ['careers' => 'Careers page found', 'hiring' => null],
                analyzedAt: '2026-01-01T00:00:00+00:00',
            ),
        );

        $checks = $this->leadCard($results)['checks'];
        $statuses = array_column($checks, 'status');

        $this->assertContains('pass', $statuses);
        $this->assertContains('warning', $statuses);
        $this->assertNotContains('fail', $statuses);
    }

    public function test_contact_info_checks_report_the_real_values_found(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            contactInfo: new ContactInfoResult(
                url: 'https://example.com/',
                emails: ['sales@example.com'],
                phones: [],
                socialProfiles: [],
                teamMembers: [],
                analyzedAt: '2026-01-01T00:00:00+00:00',
            ),
        );

        $checks = $this->leadCard($results)['checks'];
        $emailCheck = current(array_filter($checks, static fn (array $c): bool => $c['name'] === 'Emails found'));
        $phoneCheck = current(array_filter($checks, static fn (array $c): bool => $c['name'] === 'Phones found'));

        $this->assertSame('pass', $emailCheck['status']);
        $this->assertSame('sales@example.com', $emailCheck['note']);
        $this->assertSame('warning', $phoneCheck['status']);
        $this->assertNull($phoneCheck['note']);
    }

    public function test_technology_upgrade_opportunities_become_recommendations(): void
    {
        $opportunity = new TechnologyUpgradeOpportunity(
            slug: 'wordpress',
            technology: 'WordPress',
            detectedVersion: '5.9',
            reason: 'WordPress 5.9 is below 6.4',
            suggestedService: 'WordPress core upgrade',
        );

        $results = new AnalysisResults(
            url: 'https://example.com/',
            technologyUpgradeOpportunities: [$opportunity],
        );

        $card = $this->leadCard($results);

        $this->assertCount(1, $card['recommendations']);
        $this->assertStringContainsString('WordPress core upgrade', $card['recommendations'][0]);
        $this->assertStringContainsString('WordPress 5.9 is below 6.4', $card['recommendations'][0]);
    }

    public function test_the_card_key_abbreviation_and_label_are_fixed(): void
    {
        $results = new AnalysisResults(
            url: 'https://example.com/',
            technologyUpgradeOpportunities: [new TechnologyUpgradeOpportunity(
                slug: 'jquery',
                technology: 'jQuery',
                detectedVersion: '2.0',
                reason: 'outdated',
                suggestedService: 'upgrade',
            )],
        );

        $card = $this->leadCard($results);

        $this->assertSame('lead_intelligence', $card['key']);
        $this->assertSame('LEAD', $card['abbr']);
        $this->assertSame('Lead Intelligence', $card['label']);
    }
}
