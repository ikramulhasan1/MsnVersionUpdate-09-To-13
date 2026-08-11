<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Recommendations" worksheet: one prioritized
 * issue/recommendation pulled from AIRecommendationResult's
 * `issue_priority` category, which already spans every analyzer
 * (Security, Accessibility, Content, UI/UX, Performance, Business
 * Opportunity, SEO) in one severity-ranked list — the same list Quick
 * Wins and Long-Term Fixes are themselves split from — so this sheet
 * doesn't need to merge multiple recommendation categories itself.
 */
final readonly class RecommendationRow
{
    public function __construct(
        public int $priority,
        public string $category,
        public string $issue,
        public string $severity,
        public string $status,
        public ?string $recommendation,
        public ?string $pageUrl,
    ) {
    }
}
