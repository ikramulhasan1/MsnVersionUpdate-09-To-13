<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

/**
 * The "Recommended Services" category: one {@see ServiceRecommendation}
 * per issue category present in the engine's issue list, ranked by
 * total weighted severity (most impacted category first) so the
 * top-ranked service is the most defensible one to lead with.
 */
final readonly class RecommendedServices implements \JsonSerializable
{
    /**
     * @param array<int, ServiceRecommendation> $items ranked most impacted category first
     */
    public function __construct(
        public array $items,
        public string $summary,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_services' => count($this->items),
            'summary' => $this->summary,
            'items' => array_map(
                static fn (ServiceRecommendation $item): array => $item->toArray(),
                $this->items,
            ),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
