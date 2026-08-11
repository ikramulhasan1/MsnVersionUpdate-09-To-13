<?php

declare(strict_types=1);

namespace App\Audit\Seo\DTO;

final readonly class SeoAuditResult implements \JsonSerializable
{
    /**
     * @param array<int, PageSeoResult> $pages per-page results, only for pages that were successfully crawled
     * @param array<int, string> $failedPageUrls pages the crawler couldn't fetch, excluded from analysis
     * @param array<int, string> $recommendations site-wide recommendations, most impactful first
     */
    public function __construct(
        public string $startUrl,
        public array $pages,
        public array $failedPageUrls,
        public int $pagesAnalyzed,
        public int $pagesFailed,
        public int $averageScore,
        public array $recommendations,
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
                'pages_failed' => $this->pagesFailed,
                'average_score' => $this->averageScore,
            ],
            'pages' => array_map(static fn (PageSeoResult $p): array => $p->toArray(), $this->pages),
            'failed_page_urls' => $this->failedPageUrls,
            'recommendations' => $this->recommendations,
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
