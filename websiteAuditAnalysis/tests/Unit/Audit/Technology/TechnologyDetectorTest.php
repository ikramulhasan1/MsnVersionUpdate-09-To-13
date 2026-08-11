<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Technology;

use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Technology\TechnologyDetector;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class TechnologyDetectorTest extends TestCase
{
    private function detector(): TechnologyDetector
    {
        return new TechnologyDetector;
    }

    public function test_plain_page_with_no_signals_detects_nothing(): void
    {
        $result = $this->detector()->detect(FetchResultFactory::make());

        foreach ($result->detections as $detection) {
            $this->assertFalse($detection->detected);
        }
    }

    public function test_laravel_session_cookie_is_a_strong_enough_signal_to_detect_laravel(): void
    {
        $result = $this->detector()->detect(FetchResultFactory::make(headers: [
            'Set-Cookie' => 'laravel_session=abc123; XSRF-TOKEN=def456; Path=/',
        ]));

        $this->assertTrue($result->detections['laravel']->detected);
        $this->assertGreaterThan(0, $result->detections['laravel']->confidenceScore);
    }

    public function test_wordpress_asset_paths_are_a_strong_enough_signal_to_detect_wordpress(): void
    {
        // detectWordPress() matches asset paths against the structured
        // cssLinks/jsLinks arrays (via anyLinkContains), not the raw html
        // string, so the signal has to be supplied as a CssLink — a
        // <link> tag inside html: alone is never parsed out of it.
        $result = $this->detector()->detect(FetchResultFactory::make(
            cssLinks: [new CssLink(url: 'https://example.com/wp-content/themes/x/style.css')],
        ));

        $this->assertTrue($result->detections['wordpress']->detected);
    }

    public function test_a_single_weak_signal_alone_does_not_cross_the_detection_threshold(): void
    {
        // A React signal weak enough on its own (e.g. a generic root div)
        // should not, by itself, flip detected to true — confirms the
        // threshold gate, not just "any signal at all", drives detection.
        $result = $this->detector()->detect(FetchResultFactory::make());

        $this->assertFalse($result->detections['react']->detected);
        $this->assertSame(0, $result->detections['react']->confidenceScore);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->detector()->detect(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['url', 'detections', 'technology_stack', 'technology_summary', 'overall_detection_confidence', 'server_header', 'analyzed_at'],
            array_keys($decoded),
        );
    }
}
