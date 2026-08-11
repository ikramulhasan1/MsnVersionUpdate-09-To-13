<?php

declare(strict_types=1);

namespace App\Audit\Performance\DTO;

final readonly class PerformanceResult implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $metrics per-metric results, keyed by metric name.
     *        Left empty until each metric check is implemented.
     * @param ?string $grade letter grade (A-F) derived from score; null when score is null.
     * @param string $summary human-readable overview of the performance result.
     */
    public function __construct(
        public string $url,
        public ?int $score,
        public ?string $grade,
        public string $summary,
        public array $metrics,
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
            'score' => $this->score,
            'grade' => $this->grade,
            'summary' => $this->summary,
            'metrics' => $this->metrics,
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
