<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Crawler;

use App\Audit\Crawler\DTO\LinkCheckResult;
use App\Audit\Crawler\WebsiteCrawlerService;
use App\Audit\Fetching\DTO\AnchorLink;
use App\Audit\Fetching\DTO\DiscoveredResource;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\MetaData;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeLinkChecker;
use Tests\Support\FakeWebsiteFetcherService;

final class WebsiteCrawlerServiceTest extends TestCase
{
    /**
     * A small fixed site graph used across tests:
     *
     *   / (depth 0) --> /about, /broken, https://external.com/page (x2, deduped)
     *   /about (depth 1) --> /contact, https://external.com/page
     *   /broken (depth 1) --> 404, no links
     *   /contact (depth 2) --> /deep (would be depth 3, beyond max_depth=2),
     *                          https://external.com/other, back to / (cycle)
     *
     * @return array<string, FetchResult>
     */
    private function siteGraph(): array
    {
        return [
            'https://example.com/' => $this->page(
                url: 'https://example.com/',
                title: 'Home',
                canonical: 'https://example.com/',
                robots: 'index, follow',
                anchors: [
                    $this->anchor('https://example.com/about'),
                    $this->anchor('https://example.com/broken'),
                    $this->anchor('https://external.com/page'),
                    $this->anchor('https://external.com/page'), // duplicate on same page
                ],
            ),
            'https://example.com/about' => $this->page(
                url: 'https://example.com/about',
                title: 'About',
                canonical: 'https://example.com/about',
                robots: 'index, follow',
                anchors: [
                    $this->anchor('https://example.com/contact'),
                    $this->anchor('https://external.com/page'),
                ],
            ),
            'https://example.com/broken' => $this->page(
                url: 'https://example.com/broken',
                title: null,
                canonical: null,
                robots: null,
                anchors: [],
                statusCode: 404,
            ),
            'https://example.com/contact' => $this->page(
                url: 'https://example.com/contact',
                title: 'Contact',
                canonical: 'https://example.com/contact',
                robots: 'noindex, nofollow',
                anchors: [
                    $this->anchor('https://example.com/deep'),
                    $this->anchor('https://external.com/other'),
                    $this->anchor('https://example.com/'),
                ],
            ),
        ];
    }

    private function makeService(
        FakeWebsiteFetcherService $fetcher,
        FakeLinkChecker $linkChecker,
        int $maxDepth = 2,
        int $maxPages = 25,
        bool $checkExternalLinks = true,
    ): WebsiteCrawlerService {
        return new WebsiteCrawlerService(
            fetcher: $fetcher,
            linkChecker: $linkChecker,
            maxDepth: $maxDepth,
            maxPages: $maxPages,
            checkExternalLinks: $checkExternalLinks,
        );
    }

    public function test_crawls_internal_pages_breadth_first_within_depth_budget(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker)->crawl('https://example.com/');

        // BFS order: depth 0, then depth 1 (about, broken), then depth 2 (contact).
        $this->assertSame(
            ['https://example.com/', 'https://example.com/about', 'https://example.com/broken', 'https://example.com/contact'],
            array_map(static fn ($page) => $page->url, $result->pages),
        );
        $this->assertSame([0, 1, 1, 2], array_map(static fn ($page) => $page->depth, $result->pages));

        // /deep would be depth 3, past max_depth=2, so it's discovered but never fetched.
        $this->assertNotContains('https://example.com/deep', $fetcher->fetchedUrls);
        $this->assertFalse($result->truncated);
    }

    public function test_classifies_internal_pages_and_discovers_unfetched_ones(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker)->crawl('https://example.com/');

        $byUrl = [];
        foreach ($result->internalPages as $entry) {
            $byUrl[$entry->url] = $entry;
        }

        // 4 crawled + /deep (discovered but beyond depth budget) = 5.
        $this->assertCount(5, $result->internalPages);
        $this->assertSame('crawled', $byUrl['https://example.com/']->checkedVia);
        $this->assertSame('head_check', $byUrl['https://example.com/deep']->checkedVia);
        $this->assertTrue($byUrl['https://example.com/deep']->exists);
        // /contact was first seen as a link on /about, then crawled itself —
        // both should be recorded as sources.
        $this->assertEqualsCanonicalizing(
            ['https://example.com/about', 'https://example.com/contact'],
            $byUrl['https://example.com/contact']->foundOnPages,
        );
        // The cycle back to "/" from /contact should merge into the existing
        // "crawled" entry for "/", not overwrite its status or duplicate it.
        $this->assertContains('https://example.com/contact', $byUrl['https://example.com/']->foundOnPages);
        $this->assertSame('crawled', $byUrl['https://example.com/']->checkedVia);
        $this->assertTrue($byUrl['https://example.com/']->exists);
    }

    public function test_classifies_external_links_and_dedupes_sources(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker)->crawl('https://example.com/');

        $this->assertCount(2, $result->externalLinks);

        $byUrl = [];
        foreach ($result->externalLinks as $entry) {
            $byUrl[$entry->url] = $entry;
        }

        // Linked from both "/" and "/about", and duplicated on "/" itself —
        // should appear once with both distinct source pages, not three times.
        $this->assertEqualsCanonicalizing(
            ['https://example.com/', 'https://example.com/about'],
            $byUrl['https://external.com/page']->foundOnPages,
        );
        $this->assertTrue($byUrl['https://external.com/page']->exists);
        $this->assertFalse($byUrl['https://external.com/other']->exists);
    }

    public function test_reports_broken_links_from_both_crawled_pages_and_checked_links(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker)->crawl('https://example.com/');

        $brokenUrls = array_map(static fn ($e) => $e->url, $result->brokenLinks);

        // /broken was crawled successfully (success=true) but returned 404 —
        // that must still count as broken, not just outright fetch failures.
        $this->assertContains('https://example.com/broken', $brokenUrls);
        $this->assertContains('https://external.com/other', $brokenUrls);
        $this->assertCount(2, $result->brokenLinks);
    }

    public function test_surfaces_canonical_noindex_nofollow_and_redirect_chain_per_page(): void
    {
        $graph = $this->siteGraph();
        $graph['https://example.com/'] = $this->page(
            url: 'https://example.com/',
            title: 'Home',
            canonical: 'https://example.com/',
            robots: 'index, follow',
            anchors: [
                $this->anchor('https://example.com/about'),
                $this->anchor('https://example.com/broken'),
                $this->anchor('https://external.com/page'),
                $this->anchor('https://external.com/page'), // duplicate on same page
            ],
            redirectChain: ['http://example.com/', 'https://example.com/'],
        );

        $fetcher = new FakeWebsiteFetcherService($graph);
        $linkChecker = new FakeLinkChecker([]);

        $result = $this->makeService($fetcher, $linkChecker)->crawl('https://example.com/');

        $home = $result->pages[0];
        $contact = $result->pages[array_search('https://example.com/contact', array_map(static fn ($p) => $p->url, $result->pages), true)] ?? null;

        $this->assertSame(['http://example.com/', 'https://example.com/'], $home->redirectChain);
        $this->assertSame('https://example.com/', $home->canonical);
        $this->assertFalse($home->noIndex);
        $this->assertFalse($home->noFollow);

        $this->assertNotNull($contact);
        $this->assertTrue($contact->noIndex);
        $this->assertTrue($contact->noFollow);
    }

    public function test_stops_at_max_pages_and_reports_truncated(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker, maxPages: 2)->crawl('https://example.com/');

        $this->assertCount(2, $result->pages);
        $this->assertTrue($result->truncated);
    }

    public function test_skips_link_checking_when_disabled(): void
    {
        $fetcher = new FakeWebsiteFetcherService($this->siteGraph());
        $linkChecker = new FakeLinkChecker([
            'https://example.com/deep' => new LinkCheckResult(true, 200, null),
            'https://external.com/page' => new LinkCheckResult(true, 200, null),
            'https://external.com/other' => new LinkCheckResult(false, 404, null),
        ]);

        $result = $this->makeService($fetcher, $linkChecker, checkExternalLinks: false)->crawl('https://example.com/');

        $this->assertSame([], $linkChecker->checkedUrls);

        $external = $result->externalLinks[0];
        $this->assertSame('not_checked', $external->checkedVia);
        $this->assertNull($external->exists);
        // Nothing marked broken without having been checked.
        $this->assertSame([], array_filter($result->brokenLinks, static fn ($e) => ! $e->isInternal));
    }

    private function anchor(string $url): AnchorLink
    {
        return new AnchorLink(url: $url, text: null, rel: null, nofollow: false);
    }

    /**
     * @param  array<int, AnchorLink>  $anchors
     * @param  array<int, string>  $redirectChain
     */
    private function page(
        string $url,
        ?string $title,
        ?string $canonical,
        ?string $robots,
        array $anchors,
        int $statusCode = 200,
        array $redirectChain = [],
    ): FetchResult {
        return new FetchResult(
            url: $url,
            success: true,
            finalUrl: $url,
            statusCode: $statusCode,
            headers: [],
            html: '<html></html>',
            meta: new MetaData(
                title: $title,
                description: null,
                keywords: null,
                canonical: $canonical,
                robots: $robots,
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
            redirectChain: $redirectChain,
            responseTimeMs: 42,
            errors: [],
            fetchedAt: '2026-01-01T00:00:00+00:00',
        );
    }
}
