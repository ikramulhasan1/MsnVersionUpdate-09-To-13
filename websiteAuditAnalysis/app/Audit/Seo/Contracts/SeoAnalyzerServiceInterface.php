<?php

declare(strict_types=1);

namespace App\Audit\Seo\Contracts;

use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Seo\DTO\SeoAuditResult;

interface SeoAnalyzerServiceInterface
{
    /**
     * Run the full SEO checklist (title, description, keywords, canonical,
     * robots, Open Graph, Twitter Card, schema, headings, alt text, image
     * SEO, internal/external/broken links, thin content, and duplicate
     * title/description) against every successfully crawled page, and
     * return a scored result with site-wide recommendations.
     */
    public function analyze(CrawlResult $crawlResult): SeoAuditResult;
}
