<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

use App\Audit\Enums\SeoSeverity;

/**
 * One issue carried over from {@see PriorityIssue} — same category,
 * severity, status, recommendation, and page URL — plus an estimated
 * hours range to fix it. A separate DTO from PriorityIssue rather than
 * adding hours fields to it: PriorityIssue is shared with Issue
 * Priority (Prompt 14.2), which has no effort dimension, so its shape
 * is left untouched; this DTO is what Quick Wins and Long-Term Fixes
 * both build their items from.
 */
final readonly class TechnicalRecommendationItem implements \JsonSerializable
{
    public function __construct(
        public string $category,
        public string $issue,
        public SeoSeverity $severity,
        public string $status,
        public ?string $recommendation,
        public ?string $pageUrl,
        public int $estimatedHoursMin,
        public int $estimatedHoursMax,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'issue' => $this->issue,
            'severity' => $this->severity->value,
            'status' => $this->status,
            'recommendation' => $this->recommendation,
            'page_url' => $this->pageUrl,
            'estimated_hours' => [
                'min' => $this->estimatedHoursMin,
                'max' => $this->estimatedHoursMax,
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
