<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Technology;

use App\Audit\Technology\DTO\TechnologyDetectionResult;
use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\TechnologyUpgradeAnalyzer;
use PHPUnit\Framework\TestCase;

final class TechnologyUpgradeAnalyzerTest extends TestCase
{
    private function analyzer(): TechnologyUpgradeAnalyzer
    {
        return new TechnologyUpgradeAnalyzer;
    }

    /**
     * @param  array<string, TechnologyDetectionResult>  $detections
     */
    private function technology(array $detections): TechnologyResult
    {
        return new TechnologyResult(
            url: 'https://example.com/',
            detections: $detections,
            technologyStack: [],
            technologySummary: ['total_detected' => 0, 'total_checked' => 0, 'by_category' => []],
            overallDetectionConfidence: 0,
            serverHeader: null,
            analyzedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    private function detection(string $slug, ?string $version, bool $detected = true): TechnologyDetectionResult
    {
        return new TechnologyDetectionResult(
            technology: $slug,
            detected: $detected,
            version: $version,
            confidenceScore: $detected ? 90 : 0,
            detectionMethod: 'test-fixture',
        );
    }

    public function test_an_outdated_wordpress_version_is_flagged(): void
    {
        $technology = $this->technology(['wordpress' => $this->detection('wordpress', '6.0')]);

        $opportunities = $this->analyzer()->analyze($technology);

        $this->assertCount(1, $opportunities);
        $this->assertSame('wordpress', $opportunities[0]->slug);
        $this->assertSame('6.0', $opportunities[0]->detectedVersion);
        $this->assertNotSame('', $opportunities[0]->reason);
        $this->assertNotSame('', $opportunities[0]->suggestedService);
    }

    public function test_a_version_at_the_threshold_is_not_flagged(): void
    {
        // version_compare(..., '>=') means the threshold itself is
        // treated as up to date, never flagged.
        $technology = $this->technology(['wordpress' => $this->detection('wordpress', '6.4')]);

        $opportunities = $this->analyzer()->analyze($technology);

        $this->assertSame([], $opportunities);
    }

    public function test_a_current_version_above_the_threshold_is_not_flagged(): void
    {
        $technology = $this->technology(['bootstrap' => $this->detection('bootstrap', '5.3')]);

        $opportunities = $this->analyzer()->analyze($technology);

        $this->assertSame([], $opportunities);
    }

    public function test_a_null_version_is_never_flagged_even_when_the_technology_is_detected(): void
    {
        // Silence is correct here — never a guessed "probably outdated".
        $technology = $this->technology(['jquery' => $this->detection('jquery', null)]);

        $opportunities = $this->analyzer()->analyze($technology);

        $this->assertSame([], $opportunities);
    }

    public function test_a_technology_with_no_threshold_defined_is_never_flagged(): void
    {
        // laravel is deliberately excluded from THRESHOLDS since
        // TechnologyDetector never assigns it a public version.
        $technology = $this->technology(['laravel' => $this->detection('laravel', '8.0')]);

        $opportunities = $this->analyzer()->analyze($technology);

        $this->assertSame([], $opportunities);
    }

    public function test_a_technology_missing_from_detections_entirely_is_never_flagged(): void
    {
        $opportunities = $this->analyzer()->analyze($this->technology([]));

        $this->assertSame([], $opportunities);
    }

    public function test_multiple_outdated_technologies_are_all_flagged(): void
    {
        $technology = $this->technology([
            'wordpress' => $this->detection('wordpress', '5.9'),
            'jquery' => $this->detection('jquery', '2.1'),
            'bootstrap' => $this->detection('bootstrap', '5.3'),
        ]);

        $opportunities = $this->analyzer()->analyze($technology);

        $slugs = array_map(static fn ($o) => $o->slug, $opportunities);

        $this->assertContains('wordpress', $slugs);
        $this->assertContains('jquery', $slugs);
        $this->assertNotContains('bootstrap', $slugs);
        $this->assertCount(2, $opportunities);
    }

    public function test_opportunity_serializes_to_the_expected_json_shape(): void
    {
        $technology = $this->technology(['vue' => $this->detection('vue', '2.6')]);

        $opportunities = $this->analyzer()->analyze($technology);

        $decoded = json_decode(json_encode($opportunities[0], JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['slug', 'technology', 'detected_version', 'reason', 'suggested_service'],
            array_keys($decoded),
        );
    }
}
