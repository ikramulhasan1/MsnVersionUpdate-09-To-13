<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Accessibility;

use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\Accessibility\DTO\AccessibilityAuditResult;
use App\Audit\Enums\AccessibilityCheckStatus;
use App\Audit\Fetching\DTO\ImageAsset;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class AccessibilityAnalyzerTest extends TestCase
{
    private function analyzer(): AccessibilityAnalyzer
    {
        return new AccessibilityAnalyzer;
    }

    public function test_well_formed_page_scores_perfectly(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        // Contrast and font-size checks intentionally return Warning (60pts each)
        // when the fixture HTML has no inline styles to inspect — 8 checks×100 +
        // 2 checks×60 = 92, so a "well formed" page tops out at 92, not 100.
        $this->assertSame(92, $result->score);
        $this->assertSame('A', $result->grade);
    }

    public function test_image_missing_alt_text_fails_the_alt_check(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <img src="https://example.com/photo.png">
            </body></html>
            HTML;

        $image = new ImageAsset(
            url: 'https://example.com/photo.png',
            alt: null,
            pageUrl: 'https://example.com/',
            domPath: 'html > body > img:nth-child(3)',
        );

        $result = $this->analyzer()->analyze(FetchResultFactory::make(
            html: $html,
            images: [$image],
        ));

        $altCheck = $result->checks['alt'];

        $this->assertSame(AccessibilityCheckStatus::FAIL, $altCheck->status);
        $this->assertLessThan(100, $result->score);
        $this->assertNotNull($altCheck->affectedElements);
        $this->assertSame('https://example.com/photo.png', $altCheck->affectedElements[0]['url']);
        $this->assertSame('html > body > img:nth-child(3)', $altCheck->affectedElements[0]['domPath']);
        $this->assertSame('Missing alt text', $altCheck->affectedElements[0]['detail']);
    }

    public function test_input_without_a_label_fails_the_label_check(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <form><input type="text" name="q"></form>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $labelCheck = $result->checks['label'];

        $this->assertNotSame(AccessibilityCheckStatus::PASS, $labelCheck->status);
        $this->assertNotNull($labelCheck->affectedElements);
        $this->assertSame('No accessible label', $labelCheck->affectedElements[0]['detail']);
        $this->assertNotNull($labelCheck->affectedElements[0]['domPath']);
    }

    public function test_contrast_check_reports_the_dom_path_and_ratio_of_a_failing_element(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <p style="color: #ffffff; background-color: #fefefe;">Low contrast text</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $contrastCheck = $result->checks['contrast'];

        $this->assertSame(AccessibilityCheckStatus::FAIL, $contrastCheck->status);
        $this->assertNotNull($contrastCheck->affectedElements);
        $this->assertNotNull($contrastCheck->affectedElements[0]['domPath']);
        $this->assertStringContainsString('Contrast ratio', $contrastCheck->affectedElements[0]['detail']);
    }

    public function test_duplicate_ids_are_reported_with_their_dom_path(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1 id="main">Title</h1>
                <p id="main">Duplicate id</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $wcagCheck = $result->checks['wcag_compliance'];

        $this->assertSame(AccessibilityCheckStatus::FAIL, $wcagCheck->status);
        $this->assertNotNull($wcagCheck->affectedElements);

        $duplicateEntry = array_values(array_filter(
            $wcagCheck->affectedElements,
            static fn (array $entry): bool => str_contains((string) $entry['detail'], 'Duplicate id'),
        ))[0] ?? null;

        $this->assertNotNull($duplicateEntry);
        $this->assertNotNull($duplicateEntry['domPath']);
    }

    public function test_empty_html_returns_a_result_without_throwing(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: ''));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('aria', $result->checks);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'checks', 'score', 'grade', 'summary', 'analyzed_at'], array_keys($decoded));
        $this->assertSame(
            ['check', 'value', 'status', 'recommendation', 'page_url', 'affected_elements'],
            array_keys($decoded['checks']['aria']),
        );
    }

    public function test_analyze_all_reports_a_result_per_page_and_lists_pages_that_failed_to_fetch(): void
    {
        $ok = FetchResultFactory::make(url: 'https://example.com/ok');
        $failed = FetchResultFactory::make(url: 'https://example.com/broken', success: false, errors: ['Fetch failed']);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/ok' => $ok, 'https://example.com/broken' => $failed],
            'https://example.com/',
        );

        $this->assertInstanceOf(AccessibilityAuditResult::class, $result);
        $this->assertArrayHasKey('https://example.com/ok', $result->pages);
        $this->assertArrayNotHasKey('https://example.com/broken', $result->pages);
        $this->assertSame(['https://example.com/broken'], $result->failedPageUrls);
        $this->assertSame(1, $result->pagesAnalyzed);
        $this->assertSame(1, $result->pagesFailed);
    }

    public function test_analyze_all_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/' => FetchResultFactory::make()],
            'https://example.com/',
        );

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['start_url', 'summary', 'pages', 'failed_page_urls', 'analyzed_at'],
            array_keys($decoded),
        );
        $this->assertSame(
            ['pages_analyzed', 'pages_failed', 'average_score'],
            array_keys($decoded['summary']),
        );
        $this->assertSame(92, $decoded['summary']['average_score']);
    }
}
