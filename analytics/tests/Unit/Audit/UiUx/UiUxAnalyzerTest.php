<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\UiUx;

use App\Audit\Enums\UiUxElementStatus;
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

        $this->assertSame(UiUxElementStatus::FAIL, $result->elements['navigation']->status);
        $this->assertNotSame([], $result->elements['navigation']->issues);
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
    }
}
