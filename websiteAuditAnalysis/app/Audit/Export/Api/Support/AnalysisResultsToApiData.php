<?php

declare(strict_types=1);

namespace App\Audit\Export\Api\Support;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Api\DTO\AuditApiData;
use App\Audit\Export\DTO\RecommendationRow;
use App\Audit\Export\DTO\ScoreRow;
use App\Audit\Export\Support\AnalysisResultsToRows;
use App\Audit\Export\Support\RecommendationResultToRows;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use App\Models\Audit;

/**
 * Builds the JSON Export API's AuditApiData from an Audit, its
 * AnalysisResults, and an optional AIRecommendationResult — the same
 * three inputs the PDF and Excel exports are already built from (see
 * AnalysisResultsToPdfContentData and AuditReportExport).
 *
 * Every analyzer's section (SEO/Performance/Security/Accessibility/
 * UI-UX/Content/Technology Stack/Business Analysis) is that analyzer's
 * own DTO ->toArray() output, unmodified — this class doesn't reshape
 * or recompute any analyzer's data, only decides which analyzer maps
 * to which named field the API contract exposes.
 *
 * Scores and Recommendations are the exception: those reuse
 * AnalysisResultsToRows::scores() and
 * RecommendationResultToRows::recommendations() — the exact row
 * mappers the Excel/PDF exports already use — so the score list and
 * recommendation list in the JSON response always match the
 * equivalent Excel worksheet / PDF section, never a separately
 * recomputed version.
 *
 * Group U adds the five Lead Intelligence fields
 * (businessSignals/contactInfo/reviewPresence/
 * technologyUpgradeOpportunities/prospectQualification/outreachDraft),
 * following the same "own DTO's ->toArray(), unmodified" rule as every
 * analyzer field above — see AuditApiData's docblock for exactly how
 * each is derived.
 */
final class AnalysisResultsToApiData
{
    public function __construct(
        private readonly AnalysisResultsToRows $analysisMapper = new AnalysisResultsToRows(),
        private readonly RecommendationResultToRows $recommendationMapper = new RecommendationResultToRows(),
    ) {
    }

    public function map(Audit $audit, AnalysisResults $results, ?AIRecommendationResult $recommendationResult = null): AuditApiData
    {
        return new AuditApiData(
            uuid: $audit->uuid,
            url: $results->url,
            status: $audit->status->value,
            seoAnalysis: $results->seo?->toArray(),
            performanceAnalysis: $results->performance?->toArray(),
            securityAnalysis: $results->security?->toArray(),
            accessibilityAnalysis: $results->accessibility?->toArray(),
            uiUxAnalysis: $results->uiUx?->toArray(),
            contentAnalysis: $results->content?->toArray(),
            technologyStack: $results->technology?->toArray(),
            businessAnalysis: $results->businessOpportunity?->toArray(),
            scores: $this->scores($results),
            recommendations: $this->recommendations($recommendationResult),
            businessSignals: $results->businessSignals?->toArray(),
            contactInfo: $results->contactInfo?->toArray(),
            reviewPresence: $results->reviewPresence?->toArray(),
            technologyUpgradeOpportunities: array_map(
                static fn (TechnologyUpgradeOpportunity $opportunity): array => $opportunity->toArray(),
                $results->technologyUpgradeOpportunities,
            ),
            prospectQualification: $results->prospectQualification?->toArray(),
            outreachDraft: $results->outreachDraft?->toArray(),
        );
    }

    /**
     * @return array<int, array{category: string, score: ?int, grade: ?string, analyzed_at: string}>
     */
    private function scores(AnalysisResults $results): array
    {
        return $this->analysisMapper->scores($results)
            ->map(static fn (ScoreRow $row): array => [
                'category' => $row->category,
                'score' => $row->score,
                'grade' => $row->grade,
                'analyzed_at' => $row->analyzedAt,
            ])
            ->all();
    }

    /**
     * Null when no AIRecommendationResult was supplied (the AI
     * Recommendation Engine hasn't run for this audit yet) rather than
     * an empty array, so API consumers can distinguish "not available
     * yet" from "ran, found nothing to recommend" — matching this
     * requirement's "Recommendations, if available".
     *
     * @return ?array<int, array{priority: int, category: string, issue: string, severity: string, status: string, recommendation: ?string, page_url: ?string}>
     */
    private function recommendations(?AIRecommendationResult $recommendationResult): ?array
    {
        if ($recommendationResult === null) {
            return null;
        }

        return $this->recommendationMapper->recommendations($recommendationResult)
            ->map(static fn (RecommendationRow $row): array => [
                'priority' => $row->priority,
                'category' => $row->category,
                'issue' => $row->issue,
                'severity' => $row->severity,
                'status' => $row->status,
                'recommendation' => $row->recommendation,
                'page_url' => $row->pageUrl,
            ])
            ->all();
    }
}