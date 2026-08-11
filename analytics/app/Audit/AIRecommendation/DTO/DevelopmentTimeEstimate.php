<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Estimated Development Time" recommendation category: a
 * site-wide rollup of {@see QuickWins} and {@see LongTermFixes}'
 * hour totals, kept as a separate DTO built *from* those two rather
 * than duplicating their item-level hour math, so the three
 * categories can never disagree about total hours.
 */
final readonly class DevelopmentTimeEstimate implements \JsonSerializable
{
    public function __construct(
        public int $quickWinsHoursMin,
        public int $quickWinsHoursMax,
        public int $longTermHoursMin,
        public int $longTermHoursMax,
        public int $totalHoursMin,
        public int $totalHoursMax,
        public int $quickWinsCount,
        public int $longTermCount,
        public string $summary,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'quick_wins' => [
                'count' => $this->quickWinsCount,
                'estimated_hours' => [
                    'min' => $this->quickWinsHoursMin,
                    'max' => $this->quickWinsHoursMax,
                ],
            ],
            'long_term_fixes' => [
                'count' => $this->longTermCount,
                'estimated_hours' => [
                    'min' => $this->longTermHoursMin,
                    'max' => $this->longTermHoursMax,
                ],
            ],
            'total_estimated_hours' => [
                'min' => $this->totalHoursMin,
                'max' => $this->totalHoursMax,
            ],
            'summary' => $this->summary,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
