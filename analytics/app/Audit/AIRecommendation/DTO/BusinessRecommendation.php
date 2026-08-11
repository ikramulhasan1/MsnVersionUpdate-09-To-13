<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Business Recommendation" category: a single priority level and
 * narrative synthesizing what every other category already found —
 * how many quick wins/long-term fixes exist, and which services (from
 * {@see RecommendedServices}) matter most — into the one paragraph a
 * decision-maker would actually read first. Generates no new findings
 * of its own, the same way OutreachMessage in the Business Opportunity
 * analyzer only assembles data computed elsewhere.
 */
final readonly class BusinessRecommendation implements \JsonSerializable
{
    /**
     * @param array<int, string> $topFocusAreas up to three service names, most impacted first
     */
    public function __construct(
        public string $priority,
        public array $topFocusAreas,
        public string $recommendation,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'top_focus_areas' => $this->topFocusAreas,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
