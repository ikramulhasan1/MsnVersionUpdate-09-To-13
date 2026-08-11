<?php

declare(strict_types=1);

namespace App\Audit\Content\DTO;

final readonly class ContentResult implements \JsonSerializable
{
    /**
     * @param array<string, ContentCheckResult> $checks keyed by metric name (word_count, reading_time, grammar,
     *        duplicate_content, ai_generated_probability, keyword_density, content_freshness, blog_frequency)
     * @param string $grade letter grade (A-F) derived from score
     * @param string $summary human-readable overview of the content result
     */
    public function __construct(
        public string $url,
        public array $checks,
        public int $score,
        public string $grade,
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
            'checks' => array_map(
                static fn (ContentCheckResult $check): array => $check->toArray(),
                $this->checks,
            ),
            'score' => $this->score,
            'grade' => $this->grade,
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
