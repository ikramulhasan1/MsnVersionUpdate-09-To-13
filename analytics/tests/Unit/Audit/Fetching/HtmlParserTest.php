<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Fetching;

use App\Audit\Fetching\DTO\ParsedHtml;
use App\Audit\Fetching\HtmlParser;
use PHPUnit\Framework\TestCase;

final class HtmlParserTest extends TestCase
{
    private const SAMPLE_HTML = <<<'HTML'
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Example Site</title>
            <meta name="description" content="An example page for testing.">
            <meta name="keywords" content="example, test">
            <meta name="robots" content="index, follow">
            <meta property="og:title" content="Example OG Title">
            <meta name="twitter:card" content="summary">
            <link rel="canonical" href="https://example.com/canonical">
            <link rel="stylesheet" href="/css/app.css">
            <link rel="stylesheet" href="https://cdn.example.com/lib.css" media="screen">
            <link rel="preload" href="/fonts/Inter.woff2" as="font" type="font/woff2" crossorigin>
            <link rel="manifest" href="/site.webmanifest">
            <link rel="alternate" type="application/rss+xml" title="Feed" href="/feed.xml">
            <script src="/js/app.js" defer></script>
            <script src="https://cdn.example.com/lib.js"></script>
        </head>
        <body>
            <img src="/images/logo.png" alt="Logo" width="120" height="40">
            <img src="https://cdn.example.com/banner.jpg" alt="Banner">
            <a href="#section">Skip link, not an asset</a>
        </body>
        </html>
        HTML;

    private function parseSample(): ParsedHtml
    {
        return (new HtmlParser)->parse(self::SAMPLE_HTML, 'https://example.com/page');
    }

    public function test_extracts_meta_fields(): void
    {
        $meta = $this->parseSample()->meta;

        $this->assertSame('Example Site', $meta->title);
        $this->assertSame('An example page for testing.', $meta->description);
        $this->assertSame('example, test', $meta->keywords);
        $this->assertSame('index, follow', $meta->robots);
        $this->assertSame('https://example.com/canonical', $meta->canonical);
        $this->assertSame('Example OG Title', $meta->openGraph['og:title'] ?? null);
        $this->assertSame('summary', $meta->twitter['twitter:card'] ?? null);
    }

    public function test_extracts_and_resolves_css_links(): void
    {
        $css = $this->parseSample()->cssLinks;

        $this->assertCount(2, $css);
        $this->assertSame('https://example.com/css/app.css', $css[0]->url);
        $this->assertSame('https://cdn.example.com/lib.css', $css[1]->url);
        $this->assertSame('screen', $css[1]->media);
        $this->assertSame('https://example.com/page', $css[0]->pageUrl);
        $this->assertNotNull($css[0]->domPath);
    }

    public function test_extracts_script_links_with_defer_flag(): void
    {
        $js = $this->parseSample()->jsLinks;

        $this->assertCount(2, $js);
        $this->assertSame('https://example.com/js/app.js', $js[0]->url);
        $this->assertTrue($js[0]->defer);
        $this->assertFalse($js[0]->async);
        $this->assertSame('https://cdn.example.com/lib.js', $js[1]->url);
        $this->assertSame('https://example.com/page', $js[0]->pageUrl);
        $this->assertNotNull($js[0]->domPath);
    }

    public function test_extracts_images_with_dimensions(): void
    {
        $images = $this->parseSample()->images;

        $this->assertCount(2, $images);
        $this->assertSame('https://example.com/images/logo.png', $images[0]->url);
        $this->assertSame('Logo', $images[0]->alt);
        $this->assertSame(120, $images[0]->width);
        $this->assertSame(40, $images[0]->height);
        $this->assertNull($images[1]->width);
        $this->assertSame('https://example.com/page', $images[0]->pageUrl);
        $this->assertNotNull($images[0]->domPath);
    }

    public function test_extracts_preloaded_fonts(): void
    {
        $fonts = $this->parseSample()->fonts;

        $this->assertCount(1, $fonts);
        $this->assertSame('https://example.com/fonts/Inter.woff2', $fonts[0]->url);
        $this->assertSame('woff2', $fonts[0]->format);
        $this->assertSame('https://example.com/page', $fonts[0]->pageUrl);
        $this->assertNotNull($fonts[0]->domPath);
    }

    public function test_dom_path_prefers_id_then_class_then_position(): void
    {
        $html = <<<'HTML'
            <html><body>
                <div id="main">
                    <a href="https://a.example.com">A</a>
                </div>
                <div class="cards first">
                    <a href="https://b.example.com">B</a>
                </div>
                <div>
                    <a href="https://c.example.com">C</a>
                    <a href="https://d.example.com">D</a>
                </div>
            </body></html>
            HTML;

        $anchors = (new HtmlParser)->parse($html, 'https://example.com/page')->anchors;

        $this->assertSame('html > body > div#main > a:nth-child(1)', $anchors[0]->domPath);
        $this->assertSame('html > body > div.cards > a:nth-child(1)', $anchors[1]->domPath);
        $this->assertSame('html > body > div:nth-child(3) > a:nth-child(2)', $anchors[3]->domPath);
    }

    public function test_extracts_manifest_link(): void
    {
        $this->assertSame('https://example.com/site.webmanifest', $this->parseSample()->manifestUrl);
    }

    public function test_extracts_rss_feed_links(): void
    {
        $feeds = $this->parseSample()->feedUrls;

        $this->assertCount(1, $feeds);
        $this->assertSame('https://example.com/feed.xml', $feeds[0]);
    }

    public function test_ignores_anchor_links_as_assets(): void
    {
        // Sanity check: the #section anchor must not appear anywhere as a resolved asset.
        $parsed = $this->parseSample();
        $allUrls = array_merge(
            array_map(static fn ($c) => $c->url, $parsed->cssLinks),
            array_map(static fn ($j) => $j->url, $parsed->jsLinks),
            array_map(static fn ($i) => $i->url, $parsed->images),
        );

        foreach ($allUrls as $url) {
            $this->assertStringNotContainsString('#section', $url);
        }
    }

    public function test_returns_empty_parsed_html_for_blank_input(): void
    {
        $parsed = (new HtmlParser)->parse('', 'https://example.com');

        $this->assertNull($parsed->meta->title);
        $this->assertSame([], $parsed->cssLinks);
        $this->assertSame([], $parsed->images);
        $this->assertNull($parsed->manifestUrl);
    }

    public function test_handles_page_with_no_head_content_gracefully(): void
    {
        $parsed = (new HtmlParser)->parse('<html><body><p>Hi</p></body></html>', 'https://example.com');

        $this->assertNull($parsed->meta->title);
        $this->assertSame([], $parsed->cssLinks);
        $this->assertSame([], $parsed->fonts);
    }
}