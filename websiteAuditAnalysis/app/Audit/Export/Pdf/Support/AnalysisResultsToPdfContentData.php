<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf\Support;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Pdf\DTO\PdfContentData;
use App\Audit\Export\Support\AnalysisResultsToRows;
use App\Audit\Export\Support\RecommendationResultToRows;
use App\Audit\Export\Support\SummaryResultsToRows;
use Illuminate\Support\Collection;

/**
 * Builds the PDF's PdfContentData from an AnalysisResults and an
 * optional AIRecommendationResult — the same two inputs
 * AuditReportExport (the Excel export) already takes.
 *
 * This class does not derive any figures itself; it composes the
 * three row mappers the Excel export already uses
 * (AnalysisResultsToRows::scores(), SummaryResultsToRows::summary(),
 * RecommendationResultToRows::recommendations()) so the PDF's Scores,
 * Summary and Recommendations sections are guaranteed to show the same
 * numbers as the equivalent Excel worksheets — "reuse the existing
 * architecture" applied at the data layer, not just the service-class
 * layer.
 *
 * severityCounts() mirrors the private method of the same purpose on
 * AuditReportExport (Critical/Warning/Notice counts from Executive
 * Summary, for the Charts section's severity breakdown) — kept as a
 * small local duplicate rather than extracting a shared class, since
 * Part 1 of this PDF module should not require touching the existing,
 * already-shipped Excel export.
 */
final class AnalysisResultsToPdfContentData
{
    public function __construct(
        private readonly AnalysisResultsToRows $analysisMapper = new AnalysisResultsToRows(),
        private readonly RecommendationResultToRows $recommendationMapper = new RecommendationResultToRows(),
        private readonly SummaryResultsToRows $summaryMapper = new SummaryResultsToRows(),
    ) {
    }

    public function map(AnalysisResults $results, ?AIRecommendationResult $recommendationResult): PdfContentData
    {
        return new PdfContentData(
            scoreRows: $this->analysisMapper->scores($results),
            summaryRows: $this->summaryMapper->summary($results, $recommendationResult),
            recommendationRows: $this->recommendations($recommendationResult),
            severityCounts: $this->severityCounts($recommendationResult),
        );
    }

    /**
     * @return Collection<int, \App\Audit\Export\DTO\RecommendationRow>
     */
    private function recommendations(?AIRecommendationResult $recommendationResult): Collection
    {
        if ($recommendationResult === null) {
            return collect();
        }

        return $this->recommendationMapper->recommendations($recommendationResult);
    }

    /**
     * @return ?array{critical: int, warning: int, notice: int}
     */
    private function severityCounts(?AIRecommendationResult $recommendationResult): ?array
    {
        $executive = $recommendationResult?->recommendations['executive_summary'] ?? null;

        if ($executive === null) {
            return null;
        }

        return [
            'critical' => (int) $executive['critical_count'],
            'warning' => (int) $executive['warning_count'],
            'notice' => (int) $executive['notice_count'],
        ];
    }
}
