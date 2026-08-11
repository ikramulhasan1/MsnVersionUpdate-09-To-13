<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Quick Wins" recommendation category: issues classified as
 * low-effort (config/copy/markup-level fixes — security headers,
 * accessibility attributes, meta tags — rather than structural or
 * content work) by {@see \App\Audit\AIRecommendation\AIRecommendationEngine::classifyEffort()}.
 * $items keeps the severity-first ordering it inherits from the
 * engine's issue list, so the highest-impact quick win still leads.
 */
final readonly class QuickWins implements \JsonSerializable
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
