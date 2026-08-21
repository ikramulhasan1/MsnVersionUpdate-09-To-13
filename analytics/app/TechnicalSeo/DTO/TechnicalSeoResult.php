<?php

declare(strict_types=1);

namespace App\TechnicalSeo\DTO;

/**
 * Phase R2 (Technical SEO Audit) — the complete result of a site-wide
 * technical scan, built by App\TechnicalSeo\TechnicalSeoAnalyzer.
 * Stored whole as App\Models\TechnicalSeoScan::$result (a JSON column)
 * once the scan completes — see that model's own migration docblock
 * for why this table stores one JSON blob rather than several
 * normalized tables.
 */
final readonly class TechnicalSeoResult implements \JsonSerializable
{
    /**
     * @param  array<string, mixed>  $robotsTxt
     * @param  array<string, mixed>  $sitemap
     * @param  array<string, mixed>  $brokenLinks
     * @param  array<string, mixed>  $redirects
     * @param  array<string, mixed>  $indexability
     * @param  array<string, mixed>  $coreWebVitals
     * @param  array<string, mixed>  $mobileFriendliness
     * @param  array<string, mixed>  $security
     * @param  array<string, mixed>  $crawlDepth
     * @param  array<string, mixed>  $hreflang
     * @param  array<string, mixed>  $structuredData
     * @param  array<int, TechnicalSeoIssue>  $issues
     */
    public function __construct(
        public string $domain,
        public int $pagesCrawled,
        public array $robotsTxt,
        public array $sitemap,
        public array $brokenLinks,
        public array $redirects,
        public array $indexability,
        public array $coreWebVitals,
        public array $mobileFriendliness,
        public array $security,
        public array $crawlDepth,
        public array $hreflang,
        public array $structuredData,
        public array $issues,
        public int $healthScore,
        public string $healthGrade,
        public string $analyzedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'domain' => $this->domain,
            'pages_crawled' => $this->pagesCrawled,
            'robots_txt' => $this->robotsTxt,
            'sitemap' => $this->sitemap,
            'broken_links' => $this->brokenLinks,
            'redirects' => $this->redirects,
            'indexability' => $this->indexability,
            'core_web_vitals' => $this->coreWebVitals,
            'mobile_friendliness' => $this->mobileFriendliness,
            'security' => $this->security,
            'crawl_depth' => $this->crawlDepth,
            'hreflang' => $this->hreflang,
            'structured_data' => $this->structuredData,
            'issues' => array_map(static fn (TechnicalSeoIssue $i): array => $i->toArray(), $this->issues),
            'health_score' => $this->healthScore,
            'health_grade' => $this->healthGrade,
            'analyzed_at' => $this->analyzedAt,
        ];
    }

    /**
     * Reconstructs from the plain array App\Models\TechnicalSeoScan::$result
     * decodes into (its own 'array' cast) — needed since the STORED
     * form is a plain array, not this readonly class; the result page
     * (resources/views/technical-seo/show.blade.php) works with the
     * richer typed object instead of raw array keys everywhere.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['domain'],
            pagesCrawled: $data['pages_crawled'],
            robotsTxt: $data['robots_txt'],
            sitemap: $data['sitemap'],
            brokenLinks: $data['broken_links'],
            redirects: $data['redirects'],
            indexability: $data['indexability'],
            coreWebVitals: $data['core_web_vitals'],
            mobileFriendliness: $data['mobile_friendliness'],
            security: $data['security'],
            crawlDepth: $data['crawl_depth'],
            hreflang: $data['hreflang'],
            structuredData: $data['structured_data'],
            issues: array_map(
                static fn (array $i): TechnicalSeoIssue => new TechnicalSeoIssue($i['check'], $i['severity'], $i['message'], $i['recommendation'] ?? null),
                $data['issues'],
            ),
            healthScore: $data['health_score'],
            healthGrade: $data['health_grade'],
            analyzedAt: $data['analyzed_at'],
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}