<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\UiUx;

use App\Audit\Enums\UiUxElementStatus;
use App\Audit\UiUx\DTO\UiUxAuditResult;
use App\Audit\UiUx\UiUxAnalyzer;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class UiUxAnalyzerTest extends TestCase
{
    private function analyzer(): UiUxAnalyzer
    {
        return new UiUxAnalyzer;
    }

    public function test_well_formed_page_passes_the_navigation_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $this->assertSame(UiUxElementStatus::PASS, $result->elements['navigation']->status);
        $this->assertSame([], $result->elements['navigation']->issues);
    }

    public function test_page_without_a_nav_element_fails_the_navigation_check(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <h1>Title</h1>
                <p>No navigation landmark on this page at all.</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $navCheck = $result->elements['navigation'];

        $this->assertSame(UiUxElementStatus::FAIL, $navCheck->status);
        $this->assertNotSame([], $navCheck->issues);
        $this->assertNotNull($navCheck->affectedElements);
        $this->assertNull($navCheck->affectedElements[0]['domPath']);
        $this->assertStringContainsString('navigation landmark', $navCheck->affectedElements[0]['detail']);
    }

    public function test_page_without_a_form_passes_the_forms_check(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <p>No forms anywhere on this page.</p>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $this->assertSame(UiUxElementStatus::PASS, $result->elements['forms']->status);
    }

    public function test_form_missing_a_submit_control_fails_and_reports_its_dom_path(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <form id="signup"><input type="text" name="email"></form>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $formsCheck = $result->elements['forms'];

        $this->assertSame(UiUxElementStatus::FAIL, $formsCheck->status);
        $this->assertNotNull($formsCheck->affectedElements);
        $this->assertStringContainsString('form#signup', $formsCheck->affectedElements[0]['domPath']);
        $this->assertSame('Form has no visible submit control', $formsCheck->affectedElements[0]['detail']);
    }

    public function test_generic_cta_text_is_flagged_with_its_dom_path(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <a href="/signup" class="btn">Click Here</a>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $ctaCheck = $result->elements['cta'];

        $this->assertSame(UiUxElementStatus::WARNING, $ctaCheck->status);
        $this->assertNotNull($ctaCheck->affectedElements);
        $this->assertNotNull($ctaCheck->affectedElements[0]['domPath']);
        $this->assertSame('Generic, non-descriptive call-to-action text', $ctaCheck->affectedElements[0]['detail']);
    }

    public function test_zero_spacing_element_is_flagged_with_its_dom_path(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en"><body>
                <nav><a href="/">Home</a></nav>
                <h1>Title</h1>
                <div id="cramped" style="margin: 0; padding: 0;">Cramped content</div>
            </body></html>
            HTML;

        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: $html));

        $spacingCheck = $result->elements['spacing'];

        $this->assertSame(UiUxElementStatus::WARNING, $spacingCheck->status);
        $this->assertNotNull($spacingCheck->affectedElements);
        $this->assertStringContainsString('div#cramped', $spacingCheck->affectedElements[0]['domPath']);
        $this->assertSame('Margin and padding both set to zero', $spacingCheck->affectedElements[0]['detail']);
    }

    public function test_empty_html_returns_a_result_without_throwing(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(html: ''));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('navigation', $result->elements);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['url', 'elements', 'score', 'grade', 'summary', 'prioritized_suggestions', 'analyzed_at'],
            array_keys($decoded),
        );
        $this->assertSame(
            ['element', 'status', 'issues', 'suggestions', 'page_url', 'affected_elements'],
            array_keys($decoded['elements']['navigation']),
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

        $this->assertInstanceOf(UiUxAuditResult::class, $result);
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
    }
}