<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf\DTO;

use App\Audit\Export\DTO\RecommendationRow;
use App\Audit\Export\DTO\ScoreRow;
use App\Audit\Export\DTO\SummaryRow;
use Illuminate\Support\Collection;

/**
 * Everything the PDF's main content sections need — Scores, Charts,
 * Recommendations and Summary — bundled into one object so
 * AuditPdfExportService only has to pass a single variable into the
 * view for all four, instead of four separate ones.
 *
 * Deliberately reuses the same row DTOs the Excel export already
 * defines (ScoreRow, SummaryRow, RecommendationRow) rather than
 * inventing PDF-specific equivalents — see AnalysisResultsToPdfContentData,
 * which builds this from the exact same mapper classes
 * (AnalysisResultsToRows, SummaryResultsToRows, RecommendationResultToRows)
 * the Excel export uses, so both exports stay driven by one source of
 * truth and never drift apart.
 *
 * $severityCounts is null whenever no AIRecommendationResult was
 * supplied (the AI Recommendation Engine hasn't run for this audit
 * yet) — the Charts section renders a "not available yet" message for
 * the severity breakdown in that case rather than a chart with zeros.
 */
final readonly class PdfContentData
{
    /**
     * @param Collection<int, ScoreRow> $scoreRows
     * @param Collection<int, SummaryRow> $summaryRows
     * @param Collection<int, RecommendationRow> $recommendationRows
     * @param ?array{critical: int, warning: int, notice: int} $severityCounts
     */
    public function __construct(
        public Collection $scoreRows,
        public Collection $summaryRows,
        public Collection $recommendationRows,
        public ?array $severityCounts,
    ) {
    }
}
