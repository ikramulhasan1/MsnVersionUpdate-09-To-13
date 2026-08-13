<?php

declare(strict_types=1);

namespace App\Audit\Content\DTO;

/**
 * One block of text found verbatim on two or more different pages of the
 * same site — the cross-page counterpart to ContentAnalyzer's existing
 * per-page Duplicate Content check, which only ever compared a page
 * against itself. Produced by ContentAnalyzer::analyzeAll(), which is the
 * only place enough pages are available at once to compare across them.
 */
final readonly class CrossPageDuplicateGroup implements \JsonSerializable
{
    /**
     * @param  array<int, string>  $pageUrls  every page URL (2 or more) where this exact block text was found
     * @param  array<string, string>  $domPathsByPage  this block's DOM location on each page, keyed by page URL
     */
    public function __construct(
        public string $text,
        public array $pageUrls,
        public array $domPathsByPage,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'page_urls' => $this->pageUrls,
            'dom_paths_by_page' => $this->domPathsByPage,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
