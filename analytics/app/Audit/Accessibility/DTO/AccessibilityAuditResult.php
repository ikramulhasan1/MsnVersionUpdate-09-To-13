<?php

declare(strict_types=1);

namespace App\Audit\Accessibility\DTO;

/**
 * Site-wide wrapper around one AccessibilityResult per crawled page —
 * mirrors App\Audit\Security\DTO\SecurityAuditResult's shape (a per-page
 * result map plus a rolled-up summary) rather than AccessibilityAnalyzer's
 * older single-page analyze() return value, since accessibility issues
 * (missing alt text, unlabeled controls, low contrast) can differ page to
 * page on the same site.
 */
final readonly class AccessibilityAuditResult implements \JsonSerializable
{
    /**
     * @param  array<string, AccessibilityResult>  $pages  per-page accessibility results, keyed
     *                                                     by page URL, only for pages that were successfully fetched and analyzed
     * @param  array<int, string>  $failedPageUrls  pages that couldn't be fetched, excluded from analysis
     */
    public function __construct(
        public string $startUrl,
        public array $pages,
        public array $failedPageUrls,
        public int $pagesAnalyzed,
        public int $pagesFailed,
        public int $averageScore,
        public string $analyzedAt,
    ) {}

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
            'pages' => array_map(
                static fn (AccessibilityResult $result): array => $result->toArray(),
                $this->pages,
            ),
            'failed_page_urls' => $this->failedPageUrls,
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
