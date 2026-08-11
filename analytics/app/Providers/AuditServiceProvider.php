<?php

declare(strict_types=1);

namespace App\Providers;

use App\Audit\Cache\AuditCacheService;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Crawler\Contracts\LinkCheckerInterface;
use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Crawler\LinkChecker;
use App\Audit\Crawler\WebsiteCrawlerService;
use App\Audit\Export\Pdf\AuditPdfExportService;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Export\Pdf\Support\AuditToPdfHeaderData;
use App\Audit\Fetching\Contracts\HtmlParserInterface;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\HtmlParser;
use App\Audit\Fetching\WebsiteFetcherService;
use App\Audit\Performance\PageSpeedInsightsClient;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\Repositories\AuditRepository;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Seo\SeoAnalyzerService;
use App\Audit\Services\AuditService;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Audit\Validation\Contracts\DnsResolverInterface;
use App\Audit\Validation\Contracts\SslInspectorInterface;
use App\Audit\Validation\Contracts\UrlValidatorServiceInterface;
use App\Audit\Validation\DnsResolver;
use App\Audit\Validation\SslInspector;
use App\Audit\Validation\UrlValidatorService;
use Barryvdh\DomPDF\PDF;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;
use App\Audit\Export\Pdf\Support\AnalysisResultsToPdfContentData;

final class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
        $this->app->bind(AuditServiceInterface::class, AuditService::class);
        $this->app->bind(DnsResolverInterface::class, DnsResolver::class);
        $this->app->bind(SslInspectorInterface::class, SslInspector::class);

        // Explicit binding (rather than relying on autowiring the
        // concrete class) so the cache TTLs are sourced from config
        // instead of the class's own hardcoded constructor defaults —
        // every job in the pipeline (FetchAndCrawlJob, AnalyzeChunkJob,
        // AssembleAnalysisResultsJob) resolves this via the interface.
        $this->app->bind(AuditCacheServiceInterface::class, fn ($app): AuditCacheService => new AuditCacheService(
            cache: $app->make(CacheRepository::class),
            fetchTtlSeconds: (int) config('audit.cache.fetch_ttl_seconds'),
            crawlTtlSeconds: (int) config('audit.cache.crawl_ttl_seconds'),
            resultsTtlSeconds: (int) config('audit.cache.results_ttl_seconds'),
        ));

        $this->app->bind(ClientInterface::class, fn (): Client => new Client([
            'timeout' => config('audit.validation.timeout'),
            'connect_timeout' => config('audit.validation.timeout'),
        ]));

        $this->app->bind(UrlValidatorServiceInterface::class, fn ($app): UrlValidatorService => new UrlValidatorService(
            httpClient: $app->make(ClientInterface::class),
            dnsResolver: $app->make(DnsResolverInterface::class),
            sslInspector: $app->make(SslInspectorInterface::class),
            timeoutSeconds: (int) config('audit.validation.timeout'),
        ));

        $this->app->bind(HtmlParserInterface::class, HtmlParser::class);

        // Deliberately a fresh Guzzle client (not the shared ClientInterface
        // binding above) — the fetcher uses the crawler's timeout/user-agent
        // config, which is tuned independently of URL validation.
        $this->app->bind(WebsiteFetcherServiceInterface::class, fn ($app): WebsiteFetcherService => new WebsiteFetcherService(
            httpClient: new Client([
                'timeout' => (int) config('audit.crawler.timeout'),
                'connect_timeout' => (int) config('audit.crawler.timeout'),
            ]),
            htmlParser: $app->make(HtmlParserInterface::class),
            timeoutSeconds: (int) config('audit.crawler.timeout'),
            userAgent: (string) config('audit.crawler.user_agent'),
        ));

        // Another independent Guzzle client, tuned the same as the fetcher's
        // — the link checker only ever does cheap HEAD/GET probes, but it
        // should still respect the crawler's timeout and identify itself
        // with the same user agent.
        $this->app->bind(LinkCheckerInterface::class, fn ($app): LinkChecker => new LinkChecker(
            httpClient: new Client([
                'timeout' => (int) config('audit.crawler.timeout'),
                'connect_timeout' => (int) config('audit.crawler.timeout'),
            ]),
            timeoutSeconds: (int) config('audit.crawler.timeout'),
            userAgent: (string) config('audit.crawler.user_agent'),
        ));

        $this->app->bind(WebsiteCrawlerServiceInterface::class, fn ($app): WebsiteCrawlerService => new WebsiteCrawlerService(
            fetcher: $app->make(WebsiteFetcherServiceInterface::class),
            linkChecker: $app->make(LinkCheckerInterface::class),
            maxDepth: (int) config('audit.crawler.max_depth'),
            maxPages: (int) config('audit.crawler.max_pages'),
            checkExternalLinks: (bool) config('audit.crawler.check_external_links'),
            concurrency: (int) config('audit.crawler.concurrency'),
        ));

        $this->app->bind(SeoAnalyzerServiceInterface::class, fn (): SeoAnalyzerService => new SeoAnalyzerService(
            titleMinLength: (int) config('audit.seo.title_min_length'),
            titleMaxLength: (int) config('audit.seo.title_max_length'),
            descriptionMinLength: (int) config('audit.seo.description_min_length'),
            descriptionMaxLength: (int) config('audit.seo.description_max_length'),
            thinContentWordCount: (int) config('audit.seo.thin_content_word_count'),
        ));

        $this->app->bind(PageSpeedInsightsClient::class, fn ($app): PageSpeedInsightsClient => new PageSpeedInsightsClient(
            httpClient: new Client(['timeout' => 20, 'connect_timeout' => 20]),
            apiKey: config('audit.pagespeed.api_key'),
        ));

        // Explicit binding (rather than relying on autowiring the
        // concrete class) purely to make PerformanceAnalyzer's optional
        // PageSpeedInsightsClient dependency conditional on
        // config('audit.pagespeed.enabled') — every other constructor
        // parameter (score thresholds etc.) is left to its own class
        // default by not naming it here. Every caller that
        // type-hints PerformanceAnalyzer (currently only
        // AnalyzeChunkJob::handle()) picks this binding up automatically,
        // so the PageSpeed client is passed through to it whenever
        // PageSpeed Insights is enabled, without AnalyzeChunkJob itself
        // needing to know about PageSpeedInsightsClient or config at all.
        $this->app->bind(PerformanceAnalyzer::class, fn ($app): PerformanceAnalyzer => new PerformanceAnalyzer(
            pageSpeedClient: config('audit.pagespeed.enabled') ? $app->make(PageSpeedInsightsClient::class) : null,
        ));

        $this->app->bind(AuditToPdfHeaderData::class, fn (): AuditToPdfHeaderData => new AuditToPdfHeaderData(
            companyName: (string) config('app.name'),
            logoAbsolutePath: public_path((string) config('audit.pdf.logo_path')),
        ));

        $this->app->bind(
            AuditPdfExportServiceInterface::class,
            fn ($app): AuditPdfExportService => new AuditPdfExportService(
                pdf: $app->make(PDF::class),
                headerDataMapper: $app->make(AuditToPdfHeaderData::class),
                contentDataMapper: $app->make(AnalysisResultsToPdfContentData::class),
                paperSize: (string) config('audit.pdf.paper_size'),
                orientation: (string) config('audit.pdf.orientation'),
            )
        );
    }

    public function boot(): void
    {
        //
    }
}
