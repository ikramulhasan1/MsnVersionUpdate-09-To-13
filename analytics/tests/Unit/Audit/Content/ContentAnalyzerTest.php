<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Content;

use App\Audit\Content\ContentAnalyzer;
use App\Audit\Content\DTO\ContentAuditResult;
use App\Audit\Enums\ContentCheckStatus;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class ContentAnalyzerTest extends TestCase
{
    private function analyzer(): ContentAnalyzer
    {
        return new ContentAnalyzer;
    }

    public function test_substantial_content_passes_the_word_count_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(wordCount: 800));

        $this->assertSame(ContentCheckStatus::GOOD, $result->checks['word_count']->status);
    }

    public function test_thin_content_fails_the_word_count_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(wordCount: 50));

        $this->assertSame(ContentCheckStatus::CRITICAL, $result->checks['word_count']->status);
        $this->assertLessThan(100, $result->score);
    }

    public function test_borderline_content_warns_on_the_word_count_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(wordCount: 450));

        $this->assertSame(ContentCheckStatus::WARNING, $result->checks['word_count']->status);
    }

    public function test_empty_html_returns_a_result_without_throwing(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: '', wordCount: 0));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('word_count', $result->checks);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'checks', 'score', 'grade', 'summary', 'analyzed_at'], array_keys($decoded));
        $this->assertSame(
            ['metric', 'value', 'status', 'recommendation', 'page_url', 'affected_elements'],
            array_keys($decoded['checks']['word_count']),
        );
    }

    public function test_duplicate_content_check_reports_dom_paths_of_repeated_blocks(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <p>This paragraph is repeated more than once here today.</p>
                <p>Some other unique paragraph content goes here as filler text.</p>
                <p>This paragraph is repeated more than once here today.</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $duplicateCheck = $result->checks['duplicate_content'];

        $this->assertNotSame(ContentCheckStatus::GOOD, $duplicateCheck->status);
        $this->assertNotNull($duplicateCheck->affectedElements);
        $this->assertCount(2, $duplicateCheck->affectedElements);
        $this->assertStringContainsString('Repeated block', $duplicateCheck->affectedElements[0]['detail']);
        $this->assertNotNull($duplicateCheck->affectedElements[0]['domPath']);
        $this->assertNotSame(
            $duplicateCheck->affectedElements[0]['domPath'],
            $duplicateCheck->affectedElements[1]['domPath'],
        );
    }

    public function test_grammar_check_reports_the_location_of_a_flagged_sentence(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <p>Hello world. this sentence starts lowercase and should be flagged.</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $grammarCheck = $result->checks['grammar'];

        $this->assertNotSame(ContentCheckStatus::GOOD, $grammarCheck->status);
        $this->assertNotNull($grammarCheck->affectedElements);
        $this->assertNotEmpty($grammarCheck->affectedElements);
        $this->assertStringContainsString('capital letter', $grammarCheck->affectedElements[0]['detail']);
        $this->assertNotNull($grammarCheck->affectedElements[0]['domPath']);
    }

    public function test_keyword_density_check_reports_block_locations_of_the_overused_keyword(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <p>Widget assembly requires careful widget calibration and widget testing procedures.</p>
                <p>Every widget must pass widget quality control before widget shipping today.</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $keywordCheck = $result->checks['keyword_density'];

        $this->assertSame(ContentCheckStatus::CRITICAL, $keywordCheck->status);
        $this->assertNotNull($keywordCheck->affectedElements);
        $this->assertCount(2, $keywordCheck->affectedElements);
        $this->assertStringContainsString('widget', $keywordCheck->affectedElements[0]['detail']);
        $this->assertNotNull($keywordCheck->affectedElements[0]['domPath']);
    }

    public function test_analyze_all_reports_a_result_per_page_and_lists_pages_that_failed_to_fetch(): void
    {
        $ok = FetchResultFactory::make(url: 'https://example.com/ok');
        $failed = FetchResultFactory::make(url: 'https://example.com/broken', success: false, errors: ['Fetch failed']);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/ok' => $ok, 'https://example.com/broken' => $failed],
            'https://example.com/',
        );

        $this->assertInstanceOf(ContentAuditResult::class, $result);
        $this->assertArrayHasKey('https://example.com/ok', $result->pages);
        $this->assertArrayNotHasKey('https://example.com/broken', $result->pages);
        $this->assertSame(['https://example.com/broken'], $result->failedPageUrls);
        $this->assertSame(1, $result->pagesAnalyzed);
        $this->assertSame(1, $result->pagesFailed);
    }

    public function test_analyze_all_detects_cross_page_duplicate_content(): void
    {
        $sharedBlock = '<p>This exact paragraph appears identically on two different pages.</p>';

        $htmlA = '<!DOCTYPE html><html lang="en"><body><nav><a href="/">Home</a></nav>'
            ."<h1>Page A</h1>{$sharedBlock}</body></html>";
        $htmlB = '<!DOCTYPE html><html lang="en"><body><nav><a href="/">Home</a></nav>'
            ."<h1>Page B</h1>{$sharedBlock}</body></html>";

        $pageA = FetchResultFactory::make(url: 'https://example.com/a', html: $htmlA);
        $pageB = FetchResultFactory::make(url: 'https://example.com/b', html: $htmlB);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/a' => $pageA, 'https://example.com/b' => $pageB],
            'https://example.com/',
        );

        $this->assertCount(1, $result->crossPageDuplicates);

        $group = $result->crossPageDuplicates[0];

        $this->assertEqualsCanonicalizing(
            ['https://example.com/a', 'https://example.com/b'],
            $group->pageUrls,
        );
        $this->assertArrayHasKey('https://example.com/a', $group->domPathsByPage);
        $this->assertArrayHasKey('https://example.com/b', $group->domPathsByPage);
    }

    public function test_analyze_all_does_not_report_short_boilerplate_as_a_cross_page_duplicate(): void
    {
        $shortBlock = '<p>Read More</p>';

        $htmlA = '<!DOCTYPE html><html lang="en"><body><nav><a href="/">Home</a></nav>'
            ."<h1>Page A</h1>{$shortBlock}</body></html>";
        $htmlB = '<!DOCTYPE html><html lang="en"><body><nav><a href="/">Home</a></nav>'
            ."<h1>Page B</h1>{$shortBlock}</body></html>";

        $pageA = FetchResultFactory::make(url: 'https://example.com/a', html: $htmlA);
        $pageB = FetchResultFactory::make(url: 'https://example.com/b', html: $htmlB);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/a' => $pageA, 'https://example.com/b' => $pageB],
            'https://example.com/',
        );

        $this->assertSame([], $result->crossPageDuplicates);
    }

    public function test_analyze_all_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/' => FetchResultFactory::make()],
            'https://example.com/',
        );

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['start_url', 'summary', 'pages', 'cross_page_duplicates', 'failed_page_urls', 'analyzed_at'],
            array_keys($decoded),
        );
        $this->assertSame(
            ['pages_analyzed', 'pages_failed', 'average_score', 'cross_page_duplicate_groups'],
            array_keys($decoded['summary']),
        );
    }
}
