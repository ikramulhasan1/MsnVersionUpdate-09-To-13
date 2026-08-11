<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * Every issue found across every completed analyzer, sorted most
 * severe first. $items must already be sorted by the engine (by
 * {@see \App\Audit\Enums\SeoSeverity::scoreWeight()} descending) —
 * this DTO trusts that ordering and simply numbers it on output
 * rather than re-sorting, so the engine's sort is the single source
 * of truth for priority order.
 */
final readonly class IssuePriority implements \JsonSerializable
{
    /**
     * @param array<int, PriorityIssue> $items sorted most severe first
     */
    public function __construct(
        public array $items,
        public int $criticalCount,
        public int $warningCount,
        public int $noticeCount,
        public string $summary,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_issues' => count($this->items),
            'critical_count' => $this->criticalCount,
            'warning_count' => $this->warningCount,
            'notice_count' => $this->noticeCount,
            'summary' => $this->summary,
            'items' => array_values(array_map(
                static fn (PriorityIssue $item, int $index): array => $item->toArray() + ['priority' => $index + 1],
                $this->items,
                array_keys($this->items),
            )),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
