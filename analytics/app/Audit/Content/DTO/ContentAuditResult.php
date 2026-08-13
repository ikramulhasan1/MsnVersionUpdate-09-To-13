<?php

declare(strict_types=1);

namespace App\Audit\Content\DTO;

/**
 * Site-wide wrapper around one ContentResult per crawled page — mirrors
 * App\Audit\Security\DTO\SecurityAuditResult's shape (a per-page result
 * map plus a rolled-up summary), plus one thing neither Security nor
 * Accessibility need: crossPageDuplicates, since "duplicate content" is
 * only meaningfully a site-wide concept once more than one page is being
 * looked at together — ContentResult's own Duplicate Content check (see
 * ContentAnalyzer::checkDuplicateContent()) only ever compares a page
 * against itself.
 */
final readonly class ContentAuditResult implements \JsonSerializable
{
    /**
     * @param array<string, ContentResult> $pages per-page content results, keyed
     *        by page URL, only for pages that were successfully fetched and analyzed
     * @param array<int, string> $failedPageUrls pages that couldn't be fetched, excluded from analysis
     * @param array<int, CrossPageDuplicateGroup> $crossPageDuplicates text blocks
     *        found verbatim on two or more of the analyzed pages
     */
    public function __construct(
        public string $startUrl,
        public array $pages,
        public array $failedPageUrls,
        public int $pagesAnalyzed,
        public int $pagesFailed,
        public int $averageScore,
        public array $crossPageDuplicates,
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
                'cross_page_duplicate_groups' => count($this->crossPageDuplicates),
            ],
            'pages' => array_map(
                static fn (ContentResult $result): array => $result->toArray(),
                $this->pages,
            ),
            'cross_page_duplicates' => array_map(
                static fn (CrossPageDuplicateGroup $group): array => $group->toArray(),
                $this->crossPageDuplicates,
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