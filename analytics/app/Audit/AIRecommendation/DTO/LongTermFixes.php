<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Long-Term Fixes" recommendation category: issues classified as
 * higher-effort structural or content work (content rewrites,
 * performance overhauls, navigation/hero/mobile-design rebuilds) by
 * {@see \App\Audit\AIRecommendation\AIRecommendationEngine::classifyEffort()}.
 * Same shape as {@see QuickWins} — the two are deliberately parallel
 * so a caller can render them with the same component — but kept as a
 * separate DTO rather than a shared one so each category's own
 * docblock can describe what it represents.
 */
final readonly class LongTermFixes implements \JsonSerializable
{
    /**
     * @param array<int, TechnicalRecommendationItem> $items
     */
    public function __construct(
        public array $items,
        public int $totalEstimatedHoursMin,
        public int $totalEstimatedHoursMax,
        public string $summary,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_items' => count($this->items),
            'total_estimated_hours' => [
                'min' => $this->totalEstimatedHoursMin,
                'max' => $this->totalEstimatedHoursMax,
            ],
            'summary' => $this->summary,
            'items' => array_map(
                static fn (TechnicalRecommendationItem $item): array => $item->toArray(),
                $this->items,
            ),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
