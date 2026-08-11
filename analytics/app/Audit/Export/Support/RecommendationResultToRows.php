<?php

declare(strict_types=1);

namespace App\Audit\Export\Support;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\Export\DTO\RecommendationRow;
use Illuminate\Support\Collection;

/**
 * Flattens an AIRecommendationResult into the row shape the
 * "Recommendations" worksheet needs (see RecommendationRow).
 *
 * A separate class from AnalysisResultsToRows rather than an added
 * method on it: AIRecommendationResult is a distinct object produced
 * by the AI Recommendation Engine from an AnalysisResults, not a field
 * on AnalysisResults itself, so a mapper keyed to it needs its own
 * input type.
 *
 * Reads the `issue_priority` category from
 * AIRecommendationResult::$recommendations, since that category
 * already normalizes and ranks every issue across every analyzer
 * (Security, Accessibility, Content, UI/UX, Performance, Business
 * Opportunity, SEO) — the same list Quick Wins and Long-Term Fixes are
 * themselves split from — so exporting it directly avoids re-deriving
 * or duplicating that ranking here. $recommendations is an
 * array<string, mixed> (already-serialized DTOs, per
 * AIRecommendationResult), so this class works against that array
 * shape rather than the original PriorityIssue objects.
 */
final class RecommendationResultToRows
{
    /**
     * @return Collection<int, RecommendationRow>
     */
    public function recommendations(AIRecommendationResult $result): Collection
    {
        $items = $result->recommendations['issue_priority']['items'] ?? [];

        return collect($items)
            ->map(static fn (array $item): RecommendationRow => new RecommendationRow(
                priority: (int) ($item['priority'] ?? 0),
                category: (string) ($item['category'] ?? ''),
                issue: (string) ($item['issue'] ?? ''),
                severity: (string) ($item['severity'] ?? ''),
                status: (string) ($item['status'] ?? ''),
                recommendation: $item['recommendation'] ?? null,
                pageUrl: $item['page_url'] ?? null,
            ))
            ->values();
    }
}
