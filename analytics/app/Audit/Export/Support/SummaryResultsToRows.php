<?php

declare(strict_types=1);

namespace App\Audit\Export\Support;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\DTO\SummaryRow;
use Illuminate\Support\Collection;

/**
 * Builds the "Summary" worksheet's label/value rows from whatever data
 * is already available on AnalysisResults and (optionally)
 * AIRecommendationResult — no new figures are computed here, only
 * existing ones assembled for a single at-a-glance overview:
 *
 *   - Executive Summary (overall score/grade, issue counts, narrative)
 *   - Business Recommendation (priority, top focus areas, narrative)
 *   - Estimated Development Time / Estimated Cost totals
 *   - Business Opportunity Score (lead score, priority, opportunity score)
 *   - Sales Opportunity (estimated deal potential, suggested service)
 *
 * The AIRecommendationResult-derived rows are skipped entirely when no
 * recommendation result is supplied, since the AI Recommendation
 * Engine may not have run; the Business Opportunity Score / Sales
 * Opportunity rows come straight from AnalysisResults and are included
 * whenever that analyzer has run, independent of the recommendation
 * engine.
 */
final class SummaryResultsToRows
{
    /**
     * @return Collection<int, SummaryRow>
     */
    public function summary(AnalysisResults $results, ?AIRecommendationResult $recommendationResult): Collection
    {
        $rows = collect([new SummaryRow('Audit URL', $results->url)]);

        if ($recommendationResult !== null) {
            $this->appendExecutiveSummary($rows, $recommendationResult);
            $this->appendBusinessRecommendation($rows, $recommendationResult);
            $this->appendEffortTotals($rows, $recommendationResult);
        }

        $this->appendBusinessOpportunity($rows, $results);

        return $rows->values();
    }

    private function appendExecutiveSummary(Collection $rows, AIRecommendationResult $result): void
    {
        $executive = $result->recommendations['executive_summary'] ?? null;

        if ($executive === null) {
            return;
        }

        $rows->push(new SummaryRow(
            'Overall Score',
            $executive['overall_score'] !== null ? "{$executive['overall_score']}/100" : 'N/A',
        ));
        $rows->push(new SummaryRow('Overall Grade', $executive['overall_grade'] ?? 'N/A'));
        $rows->push(new SummaryRow('Categories Analyzed', (string) $executive['categories_analyzed']));
        $rows->push(new SummaryRow('Total Issues', (string) $executive['total_issues']));
        $rows->push(new SummaryRow('Critical Issues', (string) $executive['critical_count']));
        $rows->push(new SummaryRow('Warning Issues', (string) $executive['warning_count']));
        $rows->push(new SummaryRow('Notice Issues', (string) $executive['notice_count']));
        $rows->push(new SummaryRow('Executive Narrative', (string) $executive['narrative']));
    }

    private function appendBusinessRecommendation(Collection $rows, AIRecommendationResult $result): void
    {
        $businessRecommendation = $result->recommendations['business_recommendation'] ?? null;

        if ($businessRecommendation === null) {
            return;
        }

        $rows->push(new SummaryRow('Recommendation Priority', (string) $businessRecommendation['priority']));
        $rows->push(new SummaryRow(
            'Top Focus Areas',
            implode(', ', $businessRecommendation['top_focus_areas']),
        ));
        $rows->push(new SummaryRow(
            'Business Recommendation',
            (string) $businessRecommendation['recommendation'],
        ));
    }

    /**
     * PRODUCTION INCIDENT (PDF Summary feedback) — read before adding
     * "Estimated Cost" back to this method, or before reverting
     * "Estimated Development Time" back to raw hours: this row USED to
     * also push an "Estimated Cost" row (from
     * $result->recommendations['estimated_cost']) and displayed
     * Estimated Development Time in raw hours (e.g. "191-487 hours") —
     * both removed/changed on explicit request against the actual PDF
     * Summary page. "Estimated Cost" is gone outright (not merely
     * hidden by CSS — the row is simply never pushed here at all), and
     * "Estimated Development Time" now shows the SAME underlying hour
     * range converted to weeks, via the identical conversion
     * AIRecommendationEngine::weeksRangeLabel() already applies to its
     * own generated sentences (that class's own docblock explains the
     * rounding rule) — kept as its own small, duplicated private
     * method here rather than a shared dependency, since this class
     * lives in a different namespace and the conversion itself is
     * simple/stateless enough not to warrant one.
     */
    private function appendEffortTotals(Collection $rows, AIRecommendationResult $result): void
    {
        $devTime = $result->recommendations['estimated_development_time'] ?? null;

        if ($devTime !== null) {
            $rows->push(new SummaryRow(
                'Estimated Development Time',
                $this->weeksRangeLabel(
                    $devTime['total_estimated_hours']['min'],
                    $devTime['total_estimated_hours']['max'],
                ),
            ));
        }
    }

    private function weeksRangeLabel(int $hoursMin, int $hoursMax): string
    {
        // 6 productive hours/day, 5-day business week — matches
        // AIRecommendationEngine::PRODUCTIVE_HOURS_PER_DAY exactly
        // (see that class's own weeksRangeLabel() docblock for why
        // this small conversion is duplicated here rather than shared).
        $hoursPerWeek = 6 * 5;

        $weeksMin = $hoursMin > 0 ? max(1, (int) ceil($hoursMin / $hoursPerWeek)) : 0;
        $weeksMax = $hoursMax > 0 ? max(1, (int) ceil($hoursMax / $hoursPerWeek)) : 0;

        if ($weeksMin === 0 && $weeksMax === 0) {
            return '0 week(s)';
        }

        return $weeksMin === $weeksMax
            ? "{$weeksMin} week(s)"
            : "{$weeksMin}-{$weeksMax} week(s)";
    }

    private function appendBusinessOpportunity(Collection $rows, AnalysisResults $results): void
    {
        $score = $results->businessOpportunity?->businessOpportunityScore;

        if ($score !== null) {
            $rows->push(new SummaryRow('Lead Score', (string) $score->leadScore));
            $rows->push(new SummaryRow('Opportunity Priority', $score->priority));
            $rows->push(new SummaryRow('Opportunity Score', (string) $score->opportunityScore));
        }

        $sales = $results->businessOpportunity?->salesOpportunity;

        if ($sales !== null) {
            $rows->push(new SummaryRow('Estimated Deal Potential', $sales->estimatedDealPotential));
            $rows->push(new SummaryRow('Suggested Service', $sales->suggestedService));
        }
    }
}