<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Seo;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Crawler\DTO\LinkInventoryEntry;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\MetaData;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\Seo\DTO\SeoIssue;
use App\Audit\Seo\SeoAnalyzerService;
use PHPUnit\Framework\TestCase;

final class SeoAnalyzerServiceTest extends TestCase
{
    private function service(): SeoAnalyzerService
    {
        return new SeoAnalyzerService(
            titleMinLength: 30,
            titleMaxLength: 60,
            descriptionMinLength: 70,
            descriptionMaxLength: 160,
            thinContentWordCount: 300,
        );
    }

    /**
     * Builds a CrawledPage that passes every single check by default —
     * individual tests override just the fields they care about.
     *
     * @param array<int, Heading> $headings
     * @param array<int, ImageAsset> $images
     * @param array<int, string> $internalLinkUrls
     * @param array<int, string> $externalLinkUrls
     * @param array<int, SchemaBlock> $schema
     */
    private function page(
        string $url = 'https://example.com/',
        bool $success = true,
        ?string $title = 'A Well Optimized Page Title Here',
        ?string $description = 'This is a meta description that sits comfortably within the recommended length range for search results.',
        ?string $keywords = 'widgets, gadgets, gizmos',
        ?string $canonical = 'https://example.com/',
        bool $noIndex = false,
        bool $noFollow = false,
        array $openGraph = ['og:title' => 'T', 'og:description' => 'D', 'og:image' => 'https://example.com/og.png'],
        array $twitter = ['twitter:card' => 'summary_large_image'],
        array $schema = [],
        array $headings = [],
        array $images = [],
        array $internalLinkUrls = ['https://example.com/about'],
        array $externalLinkUrls = [],
        int $wordCount = 500,
    ): CrawledPage {
        $meta = new MetaData(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonical: $canonical,
            robots: null,
            viewport: null,
            charset: null,
            openGraph: $openGraph,
            twitter: $twitter,
            raw: [],
        );

        if ($schema === []) {
            $schema = [new SchemaBlock(types: ['Article'], data: ['@type' => 'Article'], valid: true)];
        }

        if ($headings === []) {
            $headings = [new Heading(level: 1, text: 'Main Heading')];
        }

        if ($images === []) {
            $images = [new ImageAsset(url: 'https://example.com/img.png', alt: 'A widget', width: 100, height: 100)];
        }

        return new CrawledPage(
            url: $url,
            depth: 0,
            success: $success,
            finalUrl: $url,
            statusCode: $success ? 200 : null,
            redirectChain: [],
            meta: $success ? $meta : null,
            title: $success ? $title : null,
            canonical: $success ? $canonical : null,
            noIndex: $noIndex,
            noFollow: $noFollow,
            anchors: [],
            internalLinkUrls: $internalLinkUrls,
            externalLinkUrls: $externalLinkUrls,
            images: $images,
            cssAssets: [],
            jsAssets: [],
            fontAssets: [],
            headings: $headings,
            schema: $schema,
            wordCount: $wordCount,
            responseTimeMs: 100,
            errors: $success ? [] : ['Fetch failed'],
        );
    }

    /**
     * @param array<int, CrawledPage> $pages
     * @param array<int, LinkInventoryEntry> $brokenLinks
     */
    private function crawlResult(array $pages, array $brokenLinks = [], string $startUrl = 'https://example.com/'): CrawlResult
    {
        return new CrawlResult(
            startUrl: $startUrl,
            origin: 'https://example.com',
            pages: $pages,
            internalPages: [],
            externalLinks: [],
            brokenLinks: $brokenLinks,
            maxDepth: 2,
            maxPages: 25,
            truncated: false,
            durationMs: 1000,
            crawledAt: '2026-01-01T00:00:00+00:00',
        );
    }

    /**
     * @param array<int, SeoIssue> $issues
     * @return array<int, string>
     */
    private function codes(array $issues): array
    {
        return array_map(static fn (SeoIssue $i): string => $i->code, $issues);
    }

    public function test_fully_optimized_page_has_no_critical_issues_and_a_perfect_score(): void
    {
        $result = $this->service()->analyze($this->crawlResult([$this->page()]));

        $this->assertCount(1, $result->pages);
        $this->assertSame([], $result->pages[0]->issues);
        $this->assertSame(0, $result->pages[0]->criticalCount);
        $this->assertSame(100, $result->pages[0]->score);
        $this->assertSame(100, $result->averageScore);
    }

    public function test_missing_title_description_and_h1_are_critical_and_drop_the_score(): void
    {
        $meta = new MetaData(
            title: null,
            description: null,
            keywords: null,
            canonical: null,
            robots: null,
            viewport: null,
            charset: null,
            openGraph: [],
            twitter: [],
            raw: [],
        );

        $page = new CrawledPage(
            url: 'https://example.com/everything-missing',
            depth: 0,
            success: true,
            finalUrl: 'https://example.com/everything-missing',
            statusCode: 200,
            redirectChain: [],
            meta: $meta,
            title: null,
            canonical: null,
            noIndex: false,
            noFollow: false,
            anchors: [],
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: [],
            cssAssets: [],
            jsAssets: [],
            fontAssets: [],
            headings: [],
            schema: [],
            wordCount: 0,
            responseTimeMs: 100,
            errors: [],
        );

        $result = $this->service()->analyze($this->crawlResult([$page]));
        $pageResult = $result->pages[0];
        $codes = $this->codes($pageResult->issues);

        $this->assertContains('title_missing', $codes);
        $this->assertContains('description_missing', $codes);
        $this->assertContains('heading_h1_missing', $codes);
        $this->assertSame(3, $pageResult->criticalCount);
        $this->assertSame(4, $pageResult->warningCount);
        $this->assertSame(3, $pageResult->noticeCount);
        $this->assertSame(25, $pageResult->score);
    }

    public function test_noindex_page_gets_a_critical_robots_issue(): void
    {
        $result = $this->service()->analyze($this->crawlResult([$this->page(noIndex: true)]));

        $issue = $this->findIssue($result->pages[0]->issues, 'robots_noindex');

        $this->assertNotNull($issue);
        $this->assertSame(SeoSeverity::CRITICAL, $issue->severity);
    }

    public function test_multiple_h1_and_skipped_heading_level_are_both_detected(): void
    {
        $page = $this->page(headings: [
            new Heading(level: 1, text: 'First H1'),
            new Heading(level: 1, text: 'Second H1'),
            new Heading(level: 4, text: 'Jumped straight to H4'),
        ]);

        $result = $this->service()->analyze($this->crawlResult([$page]));
        $codes = $this->codes($result->pages[0]->issues);

        $this->assertContains('heading_h1_multiple', $codes);
        $this->assertContains('heading_level_skipped', $codes);
    }

    public function test_duplicate_title_is_detected_across_pages(): void
    {
        $pageA = $this->page(url: 'https://example.com/a', title: 'Shared Duplicate Page Title');
        $pageB = $this->page(url: 'https://example.com/b', title: 'Shared Duplicate Page Title');

        $result = $this->service()->analyze($this->crawlResult([$pageA, $pageB]));

        $issueA = $this->findIssue($result->pages[0]->issues, 'duplicate_title');
        $issueB = $this->findIssue($result->pages[1]->issues, 'duplicate_title');

        $this->assertNotNull($issueA);
        $this->assertNotNull($issueB);
        $this->assertSame('This title is also used on 1 other page.', $issueA->message);
    }

    public function test_broken_links_are_attributed_to_the_specific_page_they_were_found_on(): void
    {
        $pageA = $this->page(url: 'https://example.com/a');
        $pageB = $this->page(url: 'https://example.com/b');

        $broken = new LinkInventoryEntry(
            url: 'https://example.com/missing',
            isInternal: true,
            foundOnPages: ['https://example.com/a'],
            exists: false,
            statusCode: 404,
            error: null,
            checkedVia: 'crawled',
        );

        $result = $this->service()->analyze($this->crawlResult([$pageA, $pageB], brokenLinks: [$broken]));

        $this->assertNotNull($this->findIssue($result->pages[0]->issues, 'broken_links_found'));
        $this->assertNull($this->findIssue($result->pages[1]->issues, 'broken_links_found'));
    }

    public function test_failed_pages_are_excluded_from_analysis_but_counted_as_failed(): void
    {
        $ok = $this->page(url: 'https://example.com/ok');
        $failed = $this->page(url: 'https://example.com/broken-fetch', success: false);

        $result = $this->service()->analyze($this->crawlResult([$ok, $failed]));

        $this->assertSame(1, $result->pagesAnalyzed);
        $this->assertSame(1, $result->pagesFailed);
        $this->assertSame(['https://example.com/broken-fetch'], $result->failedPageUrls);
        $this->assertCount(1, $result->pages);
        $this->assertSame('https://example.com/ok', $result->pages[0]->url);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->service()->analyze($this->crawlResult([$this->page(title: null)]));

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['start_url', 'summary', 'pages', 'failed_page_urls', 'recommendations', 'analyzed_at'],
            array_keys($decoded),
        );
        $this->assertSame(
            ['pages_analyzed', 'pages_failed', 'average_score'],
            array_keys($decoded['summary']),
        );

        $issue = $decoded['pages'][0]['issues'][0];
        $this->assertSame(
            ['check', 'code', 'severity', 'message', 'recommendation'],
            array_keys($issue),
        );
    }

    /**
     * @param array<int, SeoIssue> $issues
     */
    private function findIssue(array $issues, string $code): ?SeoIssue
    {
        foreach ($issues as $issue) {
            if ($issue->code === $code) {
                return $issue;
            }
        }

        return null;
    }
}
