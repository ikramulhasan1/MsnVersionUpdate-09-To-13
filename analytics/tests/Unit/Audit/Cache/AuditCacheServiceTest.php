<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Cache;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\AuditCacheService;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Jobs\AnalyzeChunkJob;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

final class AuditCacheServiceTest extends TestCase
{
    private function service(): AuditCacheService
    {
        return new AuditCacheService(new Repository(new ArrayStore()));
    }

    public function test_remember_fetch_result_caches_on_a_miss_and_reuses_it_on_a_hit(): void
    {
        $service = $this->service();
        $calls = 0;

        $callback = function () use (&$calls): FetchResult {
            $calls++;

            return FetchResultFactory::make();
        };

        $first = $service->rememberFetchResult('https://example.com/', $callback);
        $second = $service->rememberFetchResult('https://example.com/', $callback);

        $this->assertSame(1, $calls);
        $this->assertSame($first->url, $second->url);
    }

    public function test_different_urls_get_independent_cache_entries(): void
    {
        $service = $this->service();
        $calls = 0;

        $callback = function () use (&$calls): FetchResult {
            $calls++;

            return FetchResultFactory::make(url: "https://example.com/{$calls}");
        };

        $service->rememberFetchResult('https://example.com/a', $callback);
        $service->rememberFetchResult('https://example.com/b', $callback);

        $this->assertSame(2, $calls);
    }

    public function test_analysis_results_round_trip_through_the_cache(): void
    {
        $service = $this->service();
        $results = new AnalysisResults(url: 'https://example.com/');

        $this->assertNull($service->getAnalysisResults('audit-uuid'));

        $service->putAnalysisResults('audit-uuid', $results);

        $this->assertSame($results, $service->getAnalysisResults('audit-uuid'));
    }

    public function test_recommendations_round_trip_through_the_cache(): void
    {
        $service = $this->service();
        $recommendations = new AIRecommendationResult(
            url: 'https://example.com/',
            recommendations: [],
            summary: 'No AI recommendations have been generated yet.',
            analyzedAt: '2026-01-01T00:00:00+00:00',
        );

        $service->putRecommendations('audit-uuid', $recommendations);

        $this->assertSame($recommendations, $service->getRecommendations('audit-uuid'));
    }

    public function test_analysis_fragments_only_return_keys_that_were_actually_written(): void
    {
        $service = $this->service();

        $service->putAnalysisFragment('audit-uuid', 'security', FetchResultFactory::make());

        $fragments = $service->getAnalysisFragments('audit-uuid');

        $this->assertArrayHasKey('security', $fragments);
        $this->assertCount(1, $fragments);
        $this->assertNotContains('accessibility', array_keys($fragments));
    }

    public function test_forget_fragments_removes_every_analyzer_key_for_that_audit(): void
    {
        $service = $this->service();

        foreach (AnalyzeChunkJob::ANALYZER_KEYS as $key) {
            $service->putAnalysisFragment('audit-uuid', $key, FetchResultFactory::make());
        }

        $service->forgetFragments('audit-uuid');

        $this->assertSame([], $service->getAnalysisFragments('audit-uuid'));
    }

    public function test_forget_removes_results_and_recommendations_but_not_the_url_keyed_fetch_cache(): void
    {
        $service = $this->service();
        $callback = static fn (): FetchResult => FetchResultFactory::make();

        $service->rememberFetchResult('https://example.com/', $callback);
        $service->putAnalysisResults('audit-uuid', new AnalysisResults(url: 'https://example.com/'));

        $service->forget('audit-uuid');

        $this->assertNull($service->getAnalysisResults('audit-uuid'));

        // The URL-keyed fetch cache is untouched by forget() — a second
        // call for the same URL should still be a cache hit, not trigger
        // the callback again.
        $calls = 0;
        $service->rememberFetchResult('https://example.com/', function () use (&$calls) {
            $calls++;

            return FetchResultFactory::make();
        });

        $this->assertSame(0, $calls);
    }
}
