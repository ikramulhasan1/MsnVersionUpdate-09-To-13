<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Audit\Crawler\DTO\LinkCheckResult;
use App\Audit\Crawler\WebsiteCrawlerService;
use App\Audit\Fetching\DTO\AnchorLink;
use App\Audit\Fetching\DTO\DiscoveredResource;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\MetaData;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeLinkChecker;
use Tests\Support\FakeWebsiteFetcherService;

/**
 * WebsiteCrawlerService::crawl() is the one piece of the pipeline
 * whose cost scales with the *target* site, not with our own code —
 * a large or densely-linked site can make the BFS visit/enqueue
 * bookkeeping (visited[], enqueued[], the link inventory map) the
 * dominant cost. Real HTTP is replaced with FakeWebsiteFetcherService
 * so these tests measure the crawler's own bookkeeping, not network
 * latency.
 */
final class WebsiteCrawlerServicePerformanceTest extends TestCase
{
    private function anchor(string $url): AnchorLink
    {
        return new AnchorLink(url: $url, text: null, rel: null, nofollow: false);
    }

    private function page(string $url, array $anchors): FetchResult
    {
        return new FetchResult(
            url: $url,
            success: true,
            finalUrl: $url,
            statusCode: 200,
            headers: [],
            html: '<html></html>',
            meta: new MetaData(
                title: $url,
                description: null,
                keywords: null,
                canonical: $url,
                robots: 'index, follow',
                viewport: null,
                charset: null,
                openGraph: [],
                twitter: [],
                raw: [],
            ),
            cssLinks: [],
            jsLinks: [],
            images: [],
            fonts: [],
            anchors: $anchors,
            headings: [],
            schema: [],
            wordCount: 0,
            robotsTxt: DiscoveredResource::notFound(),
            sitemap: DiscoveredResource::notFound(),
            rssFeeds: [],
            manifest: DiscoveredResource::notFound(),
            redirectChain: [],
            responseTimeMs: 10,
            errors: [],
            fetchedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    /**
     * A dense mesh: page N links to every one of the next
     * $fanOut pages plus one external link, so the crawler's
     * visited/enqueued deduplication is under real pressure
     * (every page after the first is discoverable from several
     * already-queued pages).
     *
     * @return array<string, FetchResult>
     */
    private function meshSite(int $pageCount, int $fanOut): array
    {
        $site = [];

        for ($i = 0; $i < $pageCount; $i++) {
            $url = "https://example.com/page-{$i}";
            $anchors = [$this->anchor('https://external.com/shared-page')];

            for ($j = 1; $j <= $fanOut; $j++) {
                $target = $i + $j;
                if ($target < $pageCount) {
                    $anchors[] = $this->anchor("https://example.com/page-{$target}");
                }
            }

            $site[$url] = $this->page($url, $anchors);
        }

        return $site;
    }

    public function test_crawl_of_a_large_dense_site_completes_within_time_and_memory_budget(): void
    {
        $pageCount = 300;
        $site = $this->meshSite(pageCount: $pageCount, fanOut: 8);

        $fetcher = new FakeWebsiteFetcherService($site);
        $linkChecker = new FakeLinkChecker([
            'https://external.com/shared-page' => new LinkCheckResult(exists: true, statusCode: 200, error: null),
        ]);

        // maxPages above the mesh size so the crawler actually visits
        // every page rather than being bounded by the page cap —
        // this exercises the full BFS queue/inventory machinery.
        // maxPages above the mesh size so the crawler actually visits
        // every page rather than being bounded by the page cap —
        // this exercises the full BFS queue/inventory machinery.
        //
        // maxDepth must be deep enough to actually reach page 299: page i
        // only links forward to i+1..i+8 (fanOut=8), so the shortest path
        // from page 0 to page 299 is ceil(299 / 8) = 38 hops — a maxDepth
        // of 10 (the previous value) only reaches page 80, so only 81 of
        // 300 pages were ever crawled and this assertion could never pass.
        $fanOut = 8;
        $maxDepth = (int) ceil($pageCount / $fanOut) + 1;
        $crawler = new WebsiteCrawlerService($fetcher, $linkChecker, maxDepth: $maxDepth, maxPages: $pageCount + 10, checkExternalLinks: true);

        $memBefore = memory_get_usage();
        $start = microtime(true);
        $result = $crawler->crawl('https://example.com/page-0');
        $elapsedMs = (microtime(true) - $start) * 1000;
        $memUsedMb = (memory_get_usage() - $memBefore) / 1_048_576;

        self::assertCount($pageCount, $result->pages);
        self::assertLessThan(2000, $elapsedMs, "Crawling {$pageCount} pages took {$elapsedMs}ms (budget: 2000ms).");
        self::assertLessThan(96, $memUsedMb, sprintf('Crawling %d pages used %.2fMB (budget: 96MB).', $pageCount, $memUsedMb));

        // The external link is shared by every page — the link
        // checker should be memoized per-URL, not called once per
        // referring page, or this count would be ~$pageCount instead
        // of 1.
        self::assertCount(
            1,
            $linkChecker->checkedUrls,
            'External link checker was called once per referring page instead of once per unique URL — link-check memoization regressed.'
        );
    }

    public function test_max_pages_cap_bounds_work_on_an_effectively_unbounded_site(): void
    {
        // fanOut high enough, and pageCount high enough, that without
        // the maxPages cap this would enqueue far more than the cap —
        // proves the cap actually bounds crawl work, not just the
        // returned page count.
        $pageCount = 5000;
        $site = $this->meshSite(pageCount: $pageCount, fanOut: 20);

        $fetcher = new FakeWebsiteFetcherService($site);
        $linkChecker = new FakeLinkChecker([]);
        $crawler = new WebsiteCrawlerService($fetcher, $linkChecker, maxDepth: 50, maxPages: 25, checkExternalLinks: false);

        $start = microtime(true);
        $result = $crawler->crawl('https://example.com/page-0');
        $elapsedMs = (microtime(true) - $start) * 1000;

        self::assertTrue($result->truncated);
        self::assertLessThanOrEqual(25, count($result->pages));
        self::assertLessThan(500, $elapsedMs, "Capped crawl took {$elapsedMs}ms against a 5000-page site (budget: 500ms) — maxPages cap may not be bounding fetch work.");
        self::assertLessThanOrEqual(
            25,
            count($fetcher->fetchedUrls),
            'Crawler fetched more pages than maxPages allows before stopping — the cap should be checked before dequeuing, not only before enqueuing.'
        );
    }
}
