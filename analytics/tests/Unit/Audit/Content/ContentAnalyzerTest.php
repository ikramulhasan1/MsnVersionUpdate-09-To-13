<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Content;

use App\Audit\Content\ContentAnalyzer;
use App\Audit\Enums\ContentCheckStatus;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class ContentAnalyzerTest extends TestCase
{
    private function analyzer(): ContentAnalyzer
    {
        return new ContentAnalyzer();
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
    }
}
