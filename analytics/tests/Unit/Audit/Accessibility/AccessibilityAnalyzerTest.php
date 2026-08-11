<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Accessibility;

use App\Audit\Accessibility\AccessibilityAnalyzer;
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

        $result = $this->analyzer()->analyze(FetchResultFactory::make(
            html: $html,
            images: [new ImageAsset(url: 'https://example.com/photo.png', alt: null)],
        ));

        $this->assertSame(AccessibilityCheckStatus::FAIL, $result->checks['alt']->status);
        $this->assertLessThan(100, $result->score);
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

        $this->assertNotSame(AccessibilityCheckStatus::PASS, $result->checks['label']->status);
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
    }
}
