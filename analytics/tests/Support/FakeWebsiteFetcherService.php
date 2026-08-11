<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\DiscoveredResource;
use App\Audit\Fetching\DTO\FetchResult;

final class FakeWebsiteFetcherService implements WebsiteFetcherServiceInterface
{
    /** @var array<int, string> */
    public array $fetchedUrls = [];

    /**
     * @param array<string, FetchResult> $results keyed by the exact URL that will be requested
     */
    public function __construct(private readonly array $results)
    {
    }

    public function fetch(string $url): FetchResult
    {
        $this->fetchedUrls[] = $url;

        return $this->results[$url] ?? $this->notFound($url);
    }

    /**
     * @param array<int, string> $urls
     * @return array<string, FetchResult>
     */
    public function fetchMany(array $urls): array
    {
        $results = [];

        foreach ($urls as $url) {
            $results[$url] = $this->fetch($url);
        }

        return $results;
    }

    private function notFound(string $url): FetchResult
    {
        return new FetchResult(
            url: $url,
            success: false,
            finalUrl: null,
            statusCode: null,
            headers: [],
            html: null,
            meta: null,
            cssLinks: [],
            jsLinks: [],
            images: [],
            fonts: [],
            anchors: [],
            headings: [],
            schema: [],
            wordCount: 0,
            robotsTxt: DiscoveredResource::notFound(),
            sitemap: DiscoveredResource::notFound(),
            rssFeeds: [],
            manifest: DiscoveredResource::notFound(),
            redirectChain: [],
            responseTimeMs: null,
            errors: ['No fake response registered for this URL'],
            fetchedAt: '2026-01-01T00:00:00+00:00',
        );
    }
}
