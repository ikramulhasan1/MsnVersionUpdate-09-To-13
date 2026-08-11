<?php

declare(strict_types=1);

namespace Tests\Integration\Providers;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Crawler\Contracts\LinkCheckerInterface;
use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Fetching\Contracts\HtmlParserInterface;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Audit\Validation\Contracts\DnsResolverInterface;
use App\Audit\Validation\Contracts\SslInspectorInterface;
use App\Audit\Validation\Contracts\UrlValidatorServiceInterface;
use Tests\TestCase;

/**
 * The queued audit pipeline (FetchAndCrawlJob, AnalyzeChunkJob,
 * AssembleAnalysisResultsJob) resolves every one of these interfaces
 * from the container at job-execution time via method injection —
 * none of them are ever `new`'d up directly. If any is unbound, the
 * container throws when a real queue worker tries to run the job,
 * which a Queue::fake()-based feature test would never catch since it
 * never actually resolves/executes the job class. This test exercises
 * that resolution directly instead.
 */
final class ServiceContainerBindingsTest extends TestCase
{
    /**
     * @return array<string, array{0: class-string}>
     */
    public static function boundInterfaces(): array
    {
        return [
            'AuditRepositoryInterface' => [AuditRepositoryInterface::class],
            'AuditServiceInterface' => [AuditServiceInterface::class],
            'DnsResolverInterface' => [DnsResolverInterface::class],
            'SslInspectorInterface' => [SslInspectorInterface::class],
            'UrlValidatorServiceInterface' => [UrlValidatorServiceInterface::class],
            'AuditCacheServiceInterface' => [AuditCacheServiceInterface::class],
            'HtmlParserInterface' => [HtmlParserInterface::class],
            'WebsiteFetcherServiceInterface' => [WebsiteFetcherServiceInterface::class],
            'LinkCheckerInterface' => [LinkCheckerInterface::class],
            'WebsiteCrawlerServiceInterface' => [WebsiteCrawlerServiceInterface::class],
            'SeoAnalyzerServiceInterface' => [SeoAnalyzerServiceInterface::class],
        ];
    }

    /**
     * @dataProvider boundInterfaces
     */
    public function test_the_container_can_resolve_every_pipeline_interface(string $interface): void
    {
        $this->assertInstanceOf($interface, $this->app->make($interface));
    }

    public function test_the_container_can_resolve_an_analyzer_that_depends_on_a_provider_only_bound_interface(): void
    {
        // SecurityAnalyzer needs SslInspectorInterface, which is only
        // bound in AuditServiceProvider — this fails with a
        // BindingResolutionException unless that provider is registered.
        $this->assertInstanceOf(SecurityAnalyzer::class, $this->app->make(SecurityAnalyzer::class));
    }
}
