<?php

declare(strict_types=1);

namespace App\OnPageSeo\DTO;

/**
 * Phase R1 (On-Page SEO Checker) — the complete result of analyzing one
 * page's on-page SEO. Built by App\OnPageSeo\OnPageSeoAnalyzer from an
 * App\Audit\Fetching\DTO\FetchResult (this app's own existing, already-
 * built fetch/parse engine — see that analyzer's own class docblock for
 * exactly which fields it reuses from there) plus, optionally, live
 * keyword data from App\KeywordData\KeywordDataService (Phase O2).
 *
 * $issues is a flat list every finding below distills into — the SAME
 * list both resources/views/on-page-seo/index.blade.php's own "Priority
 * Fix List" section displays AND what
 * App\OnPageSeo\OnPageSeoAnalyzer::toSeoAuditResult() converts into
 * App\Audit\Seo\DTO\SeoIssue objects for reuse with this app's own
 * existing App\Audit\AIRecommendation\AIRecommendationEngine — one
 * source of truth for "what's wrong with this page", not two separate
 * lists that could drift out of sync with each other.
 */
final readonly class OnPageSeoResult implements \JsonSerializable
{
    /**
     * @param  array<string, mixed>  $title
     * @param  array<string, mixed>  $metaDescription
     * @param  array<string, mixed>  $headings
     * @param  array<string, mixed>  $content
     * @param  array<string, mixed>  $images
     * @param  array<string, mixed>  $links
     * @param  array<string, mixed>  $urlAnalysis
     * @param  array<string, mixed>  $canonical
     * @param  array<string, mixed>  $social
     * @param  array<string, mixed>  $schema
     * @param  ?array<string, mixed>  $keywordOptimization
     * @param  array<int, OnPageSeoIssue>  $issues
     */
    public function __construct(
        public string $url,
        public array $title,
        public array $metaDescription,
        public array $headings,
        public array $content,
        public array $images,
        public array $links,
        public array $urlAnalysis,
        public array $canonical,
        public array $social,
        public array $schema,
        public ?array $keywordOptimization,
        public array $issues,
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
            'title' => $this->title,
            'meta_description' => $this->metaDescription,
            'headings' => $this->headings,
            'content' => $this->content,
            'images' => $this->images,
            'links' => $this->links,
            'url_analysis' => $this->urlAnalysis,
            'canonical' => $this->canonical,
            'social' => $this->social,
            'schema' => $this->schema,
            'keyword_optimization' => $this->keywordOptimization,
            'issues' => array_map(static fn (OnPageSeoIssue $i): array => $i->toArray(), $this->issues),
            'analyzed_at' => $this->analyzedAt,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}