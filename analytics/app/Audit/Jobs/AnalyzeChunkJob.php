<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\BusinessOpportunity\BusinessOpportunityAnalyzer;
use App\Audit\BusinessSignals\BusinessSignalsDetector;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Contacts\ContactInfoExtractor;
use App\Audit\Content\ContentAnalyzer;
use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Jobs\Concerns\HasAuditUniqueness;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\ReviewPresence\ReviewPresenceScanner;
use App\Audit\Security\DTO\SecurityAuditResult;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Technology\TechnologyDetector;
use App\Audit\UiUx\UiUxAnalyzer;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use InvalidArgumentException;
use Throwable;

/**
 * One chunk of the analyzer fan-out. Each instance is responsible for
 * a subset of ANALYZER_KEYS (grouped by FetchAndCrawlJob's chunk_size)
 * and runs them sequentially within itself — parallelism comes from
 * multiple chunk jobs (and, for the seven single-page analyzers,
 * multiple analyzer keys within later chunks) being picked up by
 * different queue workers at the same time, not from anything inside
 * a single chunk.
 *
 * Does not modify or wrap any analyzer's logic — each analyzer is
 * called exactly as its existing analyze()/detect() signature expects.
 * This job only decides *which* analyzers run in *this* process and
 * *where* their output goes (a cache fragment, not a return value),
 * which is orchestration, not analysis.
 */
final class AnalyzeChunkJob extends AuditJob implements ShouldBeUnique
{
    use Batchable;
    use HasAuditUniqueness;

    /**
     * @var array<int, string>
     */
    public const array ANALYZER_KEYS = [
        'security',
        'accessibility',
        'content',
        'ui_ux',
        'performance',
        'business_opportunity',
        'technology',
        'seo',
        'business_signals',
        'contact_info',
        'review_presence',
    ];

    /**
     * @param array<int, string> $analyzerKeys subset of ANALYZER_KEYS this chunk runs
     */
    public function __construct(
        string $auditUuid,
        private readonly string $url,
        private readonly array $analyzerKeys,
    ) {
        parent::__construct();

        $this->auditUuid = $auditUuid;
    }

    /**
     * A chunk only reads from cache (fetch/crawl results should
     * already be warm — see handle()) and runs a bounded number of
     * in-process analyzers, so it needs far less headroom than
     * FetchAndCrawlJob. The one edge case — a cold/expired cache
     * forcing a chunk to re-fetch or re-crawl — is rare enough not to
     * size this around, and simply retries via backoff() like any
     * other timeout.
     */
    protected function defaultTimeoutSeconds(): int
    {
        return (int) config('audit.queue.analyze_chunk_timeout_seconds', 90);
    }

    public function handle(
        AuditCacheServiceInterface $cache,
        WebsiteFetcherServiceInterface $fetcher,
        WebsiteCrawlerServiceInterface $crawler,
        SecurityAnalyzer $securityAnalyzer,
        AccessibilityAnalyzer $accessibilityAnalyzer,
        ContentAnalyzer $contentAnalyzer,
        UiUxAnalyzer $uiUxAnalyzer,
        PerformanceAnalyzer $performanceAnalyzer,
        BusinessOpportunityAnalyzer $businessOpportunityAnalyzer,
        TechnologyDetector $technologyDetector,
        SeoAnalyzerServiceInterface $seoAnalyzerService,
        BusinessSignalsDetector $businessSignalsDetector,
        ContactInfoExtractor $contactInfoExtractor,
        ReviewPresenceScanner $reviewPresenceScanner,
    ): void {
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Both calls are cache hits by the time a chunk runs (populated
        // by FetchAndCrawlJob) — remember() is reused here rather than a
        // plain cache get() only so a chunk that somehow runs before the
        // cache is warm (or after it has expired) still gets a correct
        // result instead of null, at the cost of one possible re-fetch.
        $fetchResult = $cache->rememberFetchResult(
            $this->url,
            fn (): FetchResult => $fetcher->fetch($this->url),
        );

        $crawlResult = $cache->rememberCrawlResult(
            $this->url,
            fn (): CrawlResult => $crawler->crawl($this->url),
        );

        foreach ($this->analyzerKeys as $key) {
            $result = match ($key) {
                // Security now analyzes every successfully crawled page
                // (up to config('audit.security.per_page_limit')), not just
                // the entry page — see analyzeSecurity() below, which
                // fetches the extra pages via fetchMany() and reuses
                // $fetchResult for the entry page rather than re-fetching
                // it. Returns a SecurityAuditResult keyed by page URL.
                'security' => $this->analyzeSecurity($crawlResult, $fetchResult, $fetcher, $securityAnalyzer),
                'accessibility' => $accessibilityAnalyzer->analyze($fetchResult),
                'content' => $contentAnalyzer->analyze($fetchResult),
                'ui_ux' => $uiUxAnalyzer->analyze($fetchResult),
                'business_opportunity' => $businessOpportunityAnalyzer->analyze($fetchResult),
                'technology' => $technologyDetector->detect($fetchResult),
                // Performance now analyzes every successfully crawled page
                // (not just the entry page) and returns a
                // PerformanceAuditResult keyed by page URL — see
                // PerformanceAnalyzer::analyzeAll(). AssembleAnalysisResultsJob
                // is responsible for picking the entry page's individual
                // PerformanceResult back out of this wrapper for
                // AnalysisResults, which still carries a single-page result
                // for backward compatibility with the AI recommendation
                // engine and existing exports.
                'performance' => $crawlResult->pages === [] ? null : $performanceAnalyzer->analyzeAll($crawlResult),
                'seo' => $seoAnalyzerService->analyze($crawlResult),
                // Same empty-pages guard as performance above, and for the
                // same reason: BusinessSignalsDetector needs a concrete
                // entry page plus the full crawled-page list to scan
                // careers/hiring/blog signals across the site.
                'business_signals' => $crawlResult->pages === [] ? null : $businessSignalsDetector->analyze($crawlResult->pages[0], $crawlResult->pages),
                // Same empty-pages guard again: ContactInfoExtractor scans
                // every crawled page (mailto:/tel: links, social anchors,
                // team-page schema.org Person markup) rather than a single
                // entry page, so it needs a non-empty page list to do
                // anything meaningful.
                'contact_info' => $crawlResult->pages === [] ? null : $contactInfoExtractor->extract($crawlResult->pages),
                // Same empty-pages guard again: ReviewPresenceScanner
                // scans every crawled page's anchors for a review-platform
                // profile link.
                'review_presence' => $crawlResult->pages === [] ? null : $reviewPresenceScanner->scan($crawlResult->pages),
                default => throw new InvalidArgumentException("Unknown analyzer key [{$key}]."),
            };

            if ($result !== null) {
                $cache->putAnalysisFragment($this->auditUuid, $key, $result);
            }
        }
    }

    public function failed(Throwable $e): void
    {
        // A single chunk exhausting its retries does not, by itself,
        // fail the whole audit — AssembleAnalysisResultsJob (always run
        // via the batch's finally() callback) checks which analyzer
        // fragments actually made it into the cache and marks the audit
        // FAILED only if some are missing. Still report the exception
        // so a real failure isn't silently swallowed.
        report($e);
    }

    /**
     * Runs SecurityAnalyzer across every successfully crawled page (up to
     * config('audit.security.per_page_limit'), taken in crawl order so
     * the entry page and the pages closest to it are always included),
     * fetching whichever of those pages weren't already fetched via
     * WebsiteFetcherServiceInterface::fetchMany(). The entry page's
     * FetchResult ($fetchResult, fetched once at the top of handle() for
     * every other single-page analyzer here) is reused instead of being
     * fetched a second time.
     */
    private function analyzeSecurity(
        CrawlResult $crawlResult,
        FetchResult $entryPageFetchResult,
        WebsiteFetcherServiceInterface $fetcher,
        SecurityAnalyzer $securityAnalyzer,
    ): ?SecurityAuditResult {
        $successfulPages = array_values(array_filter(
            $crawlResult->pages,
            static fn (CrawledPage $page): bool => $page->success,
        ));

        if ($successfulPages === []) {
            return null;
        }

        $limit = max(1, (int) config('audit.security.per_page_limit', 20));
        $limitedPages = array_slice($successfulPages, 0, $limit);

        $fetchResults = [];
        $urlsToFetch = [];

        foreach ($limitedPages as $page) {
            if ($page->url === $this->url) {
                $fetchResults[$page->url] = $entryPageFetchResult;

                continue;
            }

            $urlsToFetch[] = $page->url;
        }

        if ($urlsToFetch !== []) {
            $fetchResults += $fetcher->fetchMany($urlsToFetch);
        }

        return $securityAnalyzer->analyzeAll($fetchResults, $crawlResult->startUrl);
    }

    /**
     * Distinguishes this chunk's uniqueness lock from every other
     * chunk of the same audit — without this, ShouldBeUnique would key
     * every chunk of the same audit identically and drop all but the
     * first one dispatched.
     */
    protected function uniqueIdSuffix(): string
    {
        return ':' . md5(implode(',', $this->analyzerKeys));
    }

    protected function overlapKey(): string
    {
        return static::class . ':' . $this->auditUuid . $this->uniqueIdSuffix();
    }
}