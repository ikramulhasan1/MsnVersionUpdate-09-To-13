<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Audit\Fetching\HtmlParser;
use PHPUnit\Framework\TestCase;

/**
 * HtmlParser runs DOMDocument + DOMXPath over every page fetched or
 * crawled — it is on the hot path for every single audit, and its
 * cost is entirely a function of page size, which the app does not
 * control (arbitrary websites). A pathological real-world page
 * (thousands of images/links) should degrade gracefully rather than
 * blow up memory or time.
 */
final class HtmlParserPerformanceTest extends TestCase
{
    private function parser(): HtmlParser
    {
        return new HtmlParser();
    }

    private function largeHtml(int $imageCount, int $linkCount, int $paragraphCount): string
    {
        $images = '';
        for ($i = 0; $i < $imageCount; $i++) {
            $images .= "<img src=\"https://example.com/img-{$i}.jpg\" alt=\"Image {$i}\">";
        }

        $links = '';
        for ($i = 0; $i < $linkCount; $i++) {
            $links .= "<a href=\"https://example.com/page-{$i}\">Link {$i}</a>";
        }

        $paragraphs = str_repeat('<p>' . str_repeat('lorem ipsum dolor sit amet ', 20) . '</p>', $paragraphCount);

        return '<!DOCTYPE html><html><head><title>Large Page</title>'
            . '<meta name="description" content="A large test page">'
            . '</head><body><nav>' . $links . '</nav>' . $images . $paragraphs . '</body></html>';
    }

    public function test_parses_a_large_realistic_page_within_time_and_memory_budget(): void
    {
        // Roughly comparable to a large e-commerce category or sitemap
        // page: 2,000 images, 2,000 links, 500 paragraphs.
        $html = $this->largeHtml(imageCount: 2000, linkCount: 2000, paragraphCount: 500);

        $memBefore = memory_get_usage();
        $start = microtime(true);
        $parsed = $this->parser()->parse($html, 'https://example.com/');
        $elapsedMs = (microtime(true) - $start) * 1000;
        $memUsedMb = (memory_get_usage() - $memBefore) / 1_048_576;

        self::assertCount(2000, $parsed->images);
        self::assertCount(2000, $parsed->anchors);
        self::assertLessThan(1000, $elapsedMs, "Parsing a large page took {$elapsedMs}ms (budget: 1000ms).");
        self::assertLessThan(64, $memUsedMb, sprintf('Parsing a large page used %.2fMB (budget: 64MB).', $memUsedMb));
    }

    public function test_parse_time_scales_roughly_linearly_not_quadratically_with_image_count(): void
    {
        $small = $this->largeHtml(imageCount: 200, linkCount: 0, paragraphCount: 0);
        $large = $this->largeHtml(imageCount: 2000, linkCount: 0, paragraphCount: 0); // 10x the images

        $parser = $this->parser();

        $start = microtime(true);
        $parser->parse($small, 'https://example.com/');
        $smallMs = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        $parser->parse($large, 'https://example.com/');
        $largeMs = (microtime(true) - $start) * 1000;

        // A 10x input should cost roughly 10x, not ~100x (quadratic).
        // Generous multiplier to absorb fixed DOM-parse overhead and
        // CI noise at these small absolute timings.
        self::assertLessThan(
            $smallMs * 25 + 50,
            $largeMs,
            "Parsing scaled from {$smallMs}ms (200 images) to {$largeMs}ms (2000 images) — looks worse than linear."
        );
    }

    public function test_handles_deeply_nested_html_without_excessive_memory(): void
    {
        // Deep nesting (rather than wide) stresses the DOM tree walk
        // differently than a flat list of siblings.
        $depth = 500;
        $html = '<html><body>' . str_repeat('<div>', $depth) . 'content' . str_repeat('</div>', $depth) . '</body></html>';

        $memBefore = memory_get_usage();
        $start = microtime(true);
        $this->parser()->parse($html, 'https://example.com/');
        $elapsedMs = (microtime(true) - $start) * 1000;
        $memUsedMb = (memory_get_usage() - $memBefore) / 1_048_576;

        self::assertLessThan(500, $elapsedMs, "Parsing deeply nested HTML took {$elapsedMs}ms (budget: 500ms).");
        self::assertLessThan(16, $memUsedMb, sprintf('Parsing deeply nested HTML used %.2fMB (budget: 16MB).', $memUsedMb));
    }
}
