<?php

declare(strict_types=1);

namespace App\Audit\Performance\DTO;

/**
 * Site-wide wrapper around one PerformanceResult per crawled page —
 * mirrors App\Audit\Seo\DTO\SeoAuditResult's shape (a per-page result
 * map plus a rolled-up summary) rather than PerformanceAnalyzer's
 * older single-page analyze() return value, since a site can have many
 * pages and each one's performance is independently meaningful.
 */
final readonly class PerformanceAuditResult implements \JsonSerializable
{
    /**
     * @param array<string, PerformanceResult> $pages per-page performance results, keyed
     *        by page URL, only for pages that were successfully crawled
     */
    public function __construct(
        public string $startUrl,
        public array $pages,
        public int $pagesAnalyzed,
        public ?int $averageScore,
        public string $analyzedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'start_url' => $this->startUrl,
            'summary' => [
                'pages_analyzed' => $this->pagesAnalyzed,
                'average_score' => $this->averageScore,
            ],
            'pages' => array_map(
                static fn (PerformanceResult $result): array => $result->toArray(),
                $this->pages,
            ),
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