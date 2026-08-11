<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Estimated Cost" category: a dollar range derived purely from
 * {@see QuickWins} and {@see LongTermFixes}' hour totals (built from
 * those two rather than duplicating hour math), multiplied by an
 * assumed hourly rate range — the same min/max-band convention
 * {@see \App\Audit\BusinessOpportunity\DTO\SalesOpportunity} already
 * uses for Estimated Deal Potential.
 */
final readonly class EstimatedCost implements \JsonSerializable
{
    public function __construct(
        public int $quickWinsCostMin,
        public int $quickWinsCostMax,
        public int $longTermCostMin,
        public int $longTermCostMax,
        public int $totalCostMin,
        public int $totalCostMax,
        public int $hourlyRateMin,
        public int $hourlyRateMax,
        public string $summary,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'quick_wins_cost' => [
                'min' => $this->quickWinsCostMin,
                'max' => $this->quickWinsCostMax,
            ],
            'long_term_cost' => [
                'min' => $this->longTermCostMin,
                'max' => $this->longTermCostMax,
            ],
            'total_cost' => [
                'min' => $this->totalCostMin,
                'max' => $this->totalCostMax,
            ],
            'hourly_rate' => [
                'min' => $this->hourlyRateMin,
                'max' => $this->hourlyRateMax,
            ],
            'summary' => $this->summary,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
