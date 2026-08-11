<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Fetching;

use App\Audit\Fetching\HtmlParser;
use App\Audit\Fetching\WebsiteFetcherService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class WebsiteFetcherServiceTest extends TestCase
{
    private const PAGE_HTML = <<<'HTML'
        <!DOCTYPE html>
        <html>
        <head>
            <title>Example</title>
            <meta name="description" content="Test page.">
            <link rel="stylesheet" href="/app.css">
            <script src="/app.js"></script>
        </head>
        <body><img src="/logo.png" alt="Logo"></body>
        </html>
        HTML;

    private function makeService(MockHandler $mock): WebsiteFetcherService
    {
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        return new WebsiteFetcherService(
            httpClient: $client,
            htmlParser: new HtmlParser(),
            timeoutSeconds: 10,
            userAgent: 'TestBot/1.0',
        );
    }

    public function test_successful_fetch_populates_all_dtos(): void
    {
        // Request order: 1) main page, 2) robots.txt, 3) sitemap.xml
        // (no Sitemap: line in robots body), 4) manifest.json, 5)
        // site.webmanifest (manifest.json misses so it falls through),
        // 6) /feed (no feed link in HTML so it falls back to well-known).
        $mock = new MockHandler([
            new Response(200, [], self::PAGE_HTML),
            new Response(200, ['Content-Type' => 'text/plain'], "User-agent: *\nDisallow:\n"),
            new Response(200, ['Content-Type' => 'application/xml'], '<urlset></urlset>'),
            new Response(404),
            new Response(404),
            new Response(200, ['Content-Type' => 'application/rss+xml'], '<rss></rss>'),
        ]);

        $result = $this->makeService($mock)->fetch('https://example.com/');

        $this->assertTrue($result->success);
        $this->assertSame('https://example.com/', $result->finalUrl);
        $this->assertSame(200, $result->statusCode);
        $this->assertSame('Example', $result->meta?->title);
        $this->assertCount(1, $result->cssLinks);
        $this->assertSame('https://example.com/app.css', $result->cssLinks[0]->url);
        $this->assertCount(1, $result->jsLinks);
        $this->assertCount(1, $result->images);
        $this->assertSame('https://example.com/logo.png', $result->images[0]->url);

        $this->assertTrue($result->robotsTxt->exists);
        $this->assertSame('https://example.com/robots.txt', $result->robotsTxt->url);

        $this->assertTrue($result->sitemap->exists);
        $this->assertSame('https://example.com/sitemap.xml', $result->sitemap->url);
        $this->assertSame('well_known', $result->sitemap->source);

        $this->assertFalse($result->manifest->exists);

        $this->assertCount(1, $result->rssFeeds);
        $this->assertTrue($result->rssFeeds[0]->exists);
        $this->assertSame('https://example.com/feed', $result->rssFeeds[0]->url);
    }

    public function test_sitemap_is_read_from_robots_txt_when_declared(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '<html><head><title>T</title></head><body></body></html>'),
            new Response(200, [], "User-agent: *\nSitemap: https://example.com/custom-sitemap.xml\n"),
            new Response(200, [], '<urlset></urlset>'), // the declared sitemap URL
            new Response(404), // manifest.json
            new Response(404), // site.webmanifest (manifest fallback exhausted)
            new Response(404), // /feed
            new Response(404), // /feed/
            new Response(404), // /rss.xml
            new Response(404), // /rss (feed fallback exhausted)
        ]);

        $result = $this->makeService($mock)->fetch('https://example.com/');

        $this->assertTrue($result->sitemap->exists);
        $this->assertSame('https://example.com/custom-sitemap.xml', $result->sitemap->url);
        $this->assertSame('robots_txt', $result->sitemap->source);
    }

    public function test_total_fetch_failure_short_circuits_with_no_further_calls(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection timed out', new Request('GET', 'https://example.com')),
        ]);

        $result = $this->makeService($mock)->fetch('https://example.com/');

        $this->assertFalse($result->success);
        $this->assertNull($result->html);
        $this->assertNull($result->meta);
        $this->assertSame([], $result->cssLinks);
        $this->assertFalse($result->robotsTxt->exists);
        $this->assertNotEmpty($result->errors);
    }

    public function test_manifest_and_feed_links_declared_in_html_are_probed_directly(): void
    {
        $html = <<<'HTML'
            <html><head>
                <title>T</title>
                <link rel="manifest" href="/site.webmanifest">
                <link rel="alternate" type="application/rss+xml" href="/blog/feed.xml">
            </head><body></body></html>
            HTML;

        $mock = new MockHandler([
            new Response(200, [], $html),
            new Response(404), // robots.txt
            new Response(404), // sitemap.xml (no robots body to read Sitemap: from)
            new Response(200, ['Content-Type' => 'application/manifest+json'], '{}'), // declared manifest
            new Response(200, ['Content-Type' => 'application/rss+xml'], '<rss></rss>'), // declared feed
        ]);

        $result = $this->makeService($mock)->fetch('https://example.com/');

        $this->assertTrue($result->manifest->exists);
        $this->assertSame('https://example.com/site.webmanifest', $result->manifest->url);
        $this->assertSame('html_link', $result->manifest->source);

        $this->assertCount(1, $result->rssFeeds);
        $this->assertSame('https://example.com/blog/feed.xml', $result->rssFeeds[0]->url);
        $this->assertSame('html_link', $result->rssFeeds[0]->source);
    }

    public function test_result_serializes_to_expected_json_shape(): void
    {
        $mock = new MockHandler([
            new Response(200, [], self::PAGE_HTML),
            new Response(404), // robots.txt
            new Response(404), // sitemap.xml
            new Response(404), // manifest.json
            new Response(404), // site.webmanifest
            new Response(404), // /feed
            new Response(404), // /feed/
            new Response(404), // /rss.xml
            new Response(404), // /rss
        ]);

        $result = $this->makeService($mock)->fetch('https://example.com/');
        $decoded = json_decode($result->toJson(), true, flags: JSON_THROW_ON_ERROR);

        foreach ([
            'url', 'success', 'final_url', 'status_code', 'headers', 'html', 'meta',
            'css_links', 'js_links', 'images', 'fonts', 'robots_txt', 'sitemap',
            'rss_feeds', 'manifest', 'response_time_ms', 'errors', 'fetched_at',
        ] as $key) {
            $this->assertArrayHasKey($key, $decoded);
        }
    }
}
