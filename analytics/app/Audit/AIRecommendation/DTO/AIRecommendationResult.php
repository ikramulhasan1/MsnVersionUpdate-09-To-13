<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

final readonly class AIRecommendationResult implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $recommendations structured recommendation output, keyed by
     *        category slug (e.g. executive_summary, issue_priority, quick_wins, and further
     *        categories in later phases: long_term_fixes, cost_estimation,
     *        business_recommendations). Left empty until each category is implemented.
     * @param string $summary human-readable overview of the recommendation result
     */
    public function __construct(
        public string $url,
        public array $recommendations,
        public string $summary,
        public string $analyzedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'recommendations' => $this->recommendations,
            'summary' => $this->summary,
            'analyzed_at' => $this->analyzedAt,
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
