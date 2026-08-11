<?php

declare(strict_types=1);

namespace App\Audit\Crawler\Contracts;

use App\Audit\Crawler\DTO\CrawlResult;

interface WebsiteCrawlerServiceInterface
{
    /**
     * Breadth-first crawl of a site starting from $startUrl, staying within
     * the same host. Internal pages are fetched (via the Website Fetcher
     * Service) up to $maxDepth levels deep and $maxPages total; external
     * links and internal links beyond that budget are reachability-checked
     * but not fetched. Returns one structured result covering internal
     * pages, external links, broken links, redirect chains, canonical /
     * noindex / nofollow signals, anchors, images and assets.
     */
    /**
     * @param (callable(int $pagesCrawled, int $maxPages): void)|null $onProgress
     *        optional, called after each concurrently-fetched wave of
     *        pages with the running total — lets the caller (e.g. the
     *        audit job pipeline) report granular "page 7 of 25" progress
     *        instead of only a single "crawling" status for the whole
     *        phase.
     */
    public function crawl(string $startUrl, ?int $maxDepth = null, ?int $maxPages = null, ?callable $onProgress = null): CrawlResult;
}
