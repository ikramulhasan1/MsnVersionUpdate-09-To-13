<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The top-level "Executive Summary" recommendation category: an
 * overall score/grade averaged across every completed analyzer that
 * produces a numeric score, a count of how many analyzer categories
 * actually ran, an issue-count rollup shared with {@see IssuePriority}
 * so the two categories never disagree, and a short human-readable
 * narrative tying it together.
 *
 * $overallScore/$overallGrade are nullable because a caller may not
 * have run any score-producing analyzer yet, the same reasoning
 * AnalysisResults uses to make every analyzer result nullable.
 */
final readonly class ExecutiveSummary implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?int $overallScore,
        public ?string $overallGrade,
        public int $categoriesAnalyzed,
        public int $totalIssues,
        public int $criticalCount,
        public int $warningCount,
        public int $noticeCount,
        public string $narrative,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'overall_score' => $this->overallScore,
            'overall_grade' => $this->overallGrade,
            'categories_analyzed' => $this->categoriesAnalyzed,
            'total_issues' => $this->totalIssues,
            'critical_count' => $this->criticalCount,
            'warning_count' => $this->warningCount,
            'notice_count' => $this->noticeCount,
            'narrative' => $this->narrative,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
