<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Export;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\AuditReportExport;
use Maatwebsite\Excel\Concerns\WithTitle;
use PHPUnit\Framework\TestCase;

/**
 * Confirms AuditReportExport::sheets() assembles every expected
 * worksheet for a sample AnalysisResults, identifying each sheet by
 * its WithTitle::title() rather than its class, since title() is what
 * actually appears in the downloaded workbook.
 *
 * A recommendation result is supplied (with an empty $recommendations
 * array) so the Recommendations sheet — which AuditReportExport only
 * adds when $recommendationResult !== null — is present too; every
 * mapper this exercises (AnalysisResultsToRows, RecommendationResultToRows,
 * SummaryResultsToRows) already falls back safely to empty/omitted
 * rows for fields that are null or missing, so this stays a minimal
 * fixture rather than a fully realistic one.
 */
final class AuditReportExportTest extends TestCase
{
    public function test_every_expected_worksheet_is_present_in_the_export(): void
    {
        $results = new AnalysisResults(url: 'https://example.com');

        $recommendationResult = new AIRecommendationResult(
            url: 'https://example.com',
            recommendations: [],
            summary: '',
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );

        $export = new AuditReportExport($results, $recommendationResult);

        $sheets = $export->sheets();

        $titles = collect($sheets)
            ->map(static function (object $sheet): string {
                self::assertInstanceOf(WithTitle::class, $sheet);

                return $sheet->title();
            })
            ->all();

        $expectedTitles = [
            'Analysis',
            'Scores',
            'Recommendations',
            'Technology Stack',
            'Business Analysis',
            'Summary',
            'Charts',
        ];

        foreach ($expectedTitles as $expectedTitle) {
            $this->assertContains(
                $expectedTitle,
                $titles,
                "Expected a worksheet titled \"{$expectedTitle}\" to be present in the export.",
            );
        }

        $this->assertCount(
            count($expectedTitles),
            $titles,
            'Expected exactly one worksheet per expected title, with no extras or duplicates.',
        );
    }

    public function test_the_recommendations_sheet_is_omitted_when_no_recommendation_result_is_supplied(): void
    {
        $results = new AnalysisResults(url: 'https://example.com');

        $export = new AuditReportExport($results);

        $titles = collect($export->sheets())
            ->map(static fn (object $sheet): string => $sheet->title())
            ->all();

        $this->assertNotContains('Recommendations', $titles);
    }
}