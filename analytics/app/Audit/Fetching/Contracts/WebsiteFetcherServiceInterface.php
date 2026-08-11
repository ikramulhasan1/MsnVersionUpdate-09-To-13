<?php

declare(strict_types=1);

namespace App\Audit\Fetching\Contracts;

use App\Audit\Fetching\DTO\FetchResult;

interface WebsiteFetcherServiceInterface
{
    /**
     * Fetch a single page and everything discoverable about it: HTML,
     * headers, meta, asset links, images, fonts, and well-known resources
     * (robots.txt, sitemap, RSS feed, manifest). Does not analyze or score
     * anything — that's a later phase.
     */
    public function fetch(string $url): FetchResult;

    /**
     * Concurrent version of fetch() for several URLs at once — used by the
     * crawler to fetch a wave of same-depth pages in parallel instead of
     * one at a time, which is the largest single contributor to overall
     * audit time on multi-page crawls.
     *
     * @param array<int, string> $urls
     * @return array<string, FetchResult> keyed by the original URL
     */
    public function fetchMany(array $urls): array;
}
