<?php

declare(strict_types=1);

namespace App\Discovery\Jobs\Concerns;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\TechnologyDetector;

/**
 * The homepage-only "quick scan" plumbing App\Discovery\Jobs\EnrichDiscoveredWebsiteJob
 * (Phase D0) and App\Discovery\Jobs\MonitorWatchlistChangesJob (Phase
 * G2) both need: converting a single FetchResult into the CrawledPage/
 * CrawlResult shapes App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface
 * and App\Audit\Performance\PerformanceAnalyzer expect, plus resolving
 * a TechnologyResult into the same comma-joined display-name strings
 * discovered_websites.cms/framework/ecommerce_platform/cdn already
 * store. Extracted here once both jobs needed the exact same logic,
 * rather than duplicated a second time — see EnrichDiscoveredWebsiteJob's
 * own docblock for the original, fuller explanation of each method's
 * reasoning (unchanged by this extraction, only its location moved).
 */
trait BuildsSinglePageCrawlResult
{
    private function crawledPageFrom(FetchResult $fetch): CrawledPage
    {
        return new CrawledPage(
            url: $fetch->url,
            depth: 0,
            success: $fetch->success,
            finalUrl: $fetch->finalUrl,
            statusCode: $fetch->statusCode,
            redirectChain: $fetch->redirectChain,
            meta: $fetch->meta,
            title: $fetch->meta?->title,
            canonical: $fetch->meta?->canonical,
            noIndex: false,
            noFollow: false,
            anchors: $fetch->anchors,
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: $fetch->images,
            cssAssets: $fetch->cssLinks,
            jsAssets: $fetch->jsLinks,
            fontAssets: $fetch->fonts,
            headings: $fetch->headings,
            schema: $fetch->schema,
            wordCount: $fetch->wordCount,
            responseTimeMs: $fetch->responseTimeMs,
            errors: $fetch->errors,
        );
    }

    private function singlePageCrawlResult(FetchResult $fetch, CrawledPage $page): CrawlResult
    {
        return new CrawlResult(
            startUrl: $fetch->url,
            origin: parse_url($fetch->url, PHP_URL_HOST) ?: null,
            pages: [$page],
            internalPages: [],
            externalLinks: [],
            brokenLinks: [],
            maxDepth: 0,
            maxPages: 1,
            truncated: false,
            durationMs: $fetch->responseTimeMs,
            crawledAt: now()->toAtomString(),
        );
    }

    /**
     * @param  array<int, string>  $categories
     */
    private function technologyColumnValue(TechnologyResult $technology, array $categories): ?string
    {
        $names = [];

        foreach (TechnologyDetector::CATEGORY_MAP as $slug => $category) {
            if (! in_array($category, $categories, true)) {
                continue;
            }

            $detection = $technology->detections[$slug] ?? null;

            if ($detection !== null && $detection->detected) {
                $names[] = TechnologyDetector::TECHNOLOGY_NAMES[$slug] ?? ucfirst($slug);
            }
        }

        return $names === [] ? null : implode(', ', $names);
    }
}
