<?php

declare(strict_types=1);

namespace App\Audit\Export;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Sheets\AnalysisSheetExport;
use App\Audit\Export\Sheets\BusinessAnalysisSheetExport;
use App\Audit\Export\Sheets\ChartsSheetExport;
use App\Audit\Export\Sheets\RecommendationsSheetExport;
use App\Audit\Export\Sheets\ScoresSheetExport;
use App\Audit\Export\Sheets\SummarySheetExport;
use App\Audit\Export\Sheets\TechnologySheetExport;
use App\Audit\Export\Support\AnalysisResultsToRows;
use App\Audit\Export\Support\RecommendationResultToRows;
use App\Audit\Export\Support\SummaryResultsToRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Complete Excel export workbook for a single audit report — every
 * worksheet from Parts 1-3 combined into one downloadable .xlsx file
 * via Excel::download().
 *
 * Part 1 (Prompt 16.1) exported only Scores and Analysis, each on its
 * own worksheet — see ScoresSheetExport / AnalysisSheetExport and
 * AnalysisResultsToRows::scores()/analysis(), none of which changed
 * here.
 *
 * Part 2 (Prompt 16.2) added three further worksheets, each backed by
 * its own mapper method so Part 1's methods never had to change:
 *   - Technology Stack and Business Analysis are read straight off the
 *     same AnalysisResults, via
 *     AnalysisResultsToRows::technologyStack()/businessAnalysis().
 *   - Recommendations comes from a separate AIRecommendationResult — a
 *     distinct object the AI Recommendation Engine produces from an
 *     AnalysisResults, not a field on AnalysisResults itself — mapped
 *     by the dedicated RecommendationResultToRows.
 * None of Part 2's methods changed here either.
 *
 * Part 3 (Prompt 16.3) adds the final two worksheets, plus formatting:
 *   - Summary is a single Metric/Value overview built by
 *     SummaryResultsToRows purely from data every other worksheet
 *     already exports — no new figures are computed for it.
 *   - Charts is a bar chart of category scores (always present) plus,
 *     when $recommendationResult is supplied, a pie chart of the
 *     Critical/Warning/Notice breakdown from Executive Summary — see
 *     ChartsSheetExport for how those small backing data tables are
 *     written.
 *   - Every worksheet (Parts 1-3 alike) now mixes in
 *     Support\Concerns\FormatsWorksheet for a consistent bold header
 *     row, borders, frozen header, auto-filter, wrapped text, and a
 *     capped column width — applied via WithEvents so it never touches
 *     any sheet's collection()/array()/headings()/title() logic.
 *
 * Group U added a "Lead Intelligence" worksheet (prospect score/grade/
 * priority, business signals, contacts found, and technology upgrade
 * opportunities — via AnalysisResultsToRows::leadIntelligence()).
 *
 * PRODUCTION INCIDENT (Phase M2) — that worksheet was removed on
 * request: Lead Intelligence and Prospect Qualification data (the
 * SAME leadIntelligence() method covered both together — prospect
 * score/grade/priority IS the Prospect Qualification data, folded into
 * that one method rather than living in a separate one) don't belong
 * in a report meant to hand to a prospect or client, only on the
 * in-app dashboard (audit/partials/dashboard-components.blade.php's
 * own "Lead Intelligence" category card, and
 * audit/partials/full-report.blade.php's own dedicated "Prospect
 * Qualification" block — see that partial's own docblock — are both
 * UNCHANGED by this: this fix only touches what gets EXPORTED, not
 * what the app itself shows). Outreach Draft was never exported here
 * to begin with — it has no corresponding sheet/mapper method and
 * needed no removal. leadIntelligence() itself, LeadIntelligenceRow,
 * and LeadIntelligenceSheetExport are left in place as dead code
 * rather than deleted outright — removing an unused class carries more
 * risk of missing some other caller than leaving it unreferenced does,
 * and this class's own docblock plus this one together make clear
 * why it's no longer called from here.
 *
 * $recommendationResult stays nullable throughout: Recommendations,
 * the recommendation-derived rows on Summary, and the severity pie
 * chart on Charts are simply omitted when the AI Recommendation Engine
 * hasn't been run for this report — every other worksheet still
 * exports normally from AnalysisResults alone.
 *
 * Usage:
 *
 *     return Excel::download(
 *         new AuditReportExport($analysisResults, $recommendationResult),
 *         'audit-report.xlsx',
 *     );
 */
final class AuditReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly AnalysisResults $results,
        private readonly ?AIRecommendationResult $recommendationResult = null,
        private readonly AnalysisResultsToRows $mapper = new AnalysisResultsToRows(),
        private readonly RecommendationResultToRows $recommendationMapper = new RecommendationResultToRows(),
        private readonly SummaryResultsToRows $summaryMapper = new SummaryResultsToRows(),
    ) {
    }

    /**
     * @return array<int, \Maatwebsite\Excel\Concerns\FromCollection|\Maatwebsite\Excel\Concerns\FromArray>
     */
    public function sheets(): array
    {
        $scoreRows = $this->mapper->scores($this->results);

        $sheets = [
            new SummarySheetExport($this->summaryMapper->summary($this->results, $this->recommendationResult)),
            new ScoresSheetExport($scoreRows),
            new AnalysisSheetExport($this->mapper->analysis($this->results)),
            new TechnologySheetExport($this->mapper->technologyStack($this->results)),
            new BusinessAnalysisSheetExport($this->mapper->businessAnalysis($this->results)),
        ];

        if ($this->recommendationResult !== null) {
            $sheets[] = new RecommendationsSheetExport(
                $this->recommendationMapper->recommendations($this->recommendationResult),
            );
        }

        // Phase M2 — Lead Intelligence sheet deliberately removed here.
        // See this class's own docblock for the full incident: that
        // sheet also carried Prospect Qualification data, so removing
        // it satisfies both "no Lead Intelligence" and "no Prospect
        // Qualification" in the exported file at once.

        $sheets[] = new ChartsSheetExport($scoreRows, $this->severityCounts());

        return $sheets;
    }

    /**
     * The Critical/Warning/Notice issue counts ChartsSheetExport's
     * severity pie chart needs, read from Executive Summary — the same
     * counts SummaryResultsToRows already surfaces — or null when no
     * recommendation result is available, in which case Charts skips
     * that chart entirely.
     *
     * @return ?array{critical: int, warning: int, notice: int}
     */
    private function severityCounts(): ?array
    {
        $executive = $this->recommendationResult?->recommendations['executive_summary'] ?? null;

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