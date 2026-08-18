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
use App\Audit\Enums\AuditMode;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Jobs\Concerns\HasAuditUniqueness;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\ReviewPresence\ReviewPresenceScanner;
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
     * @param  array<int, string>  $analyzerKeys  subset of ANALYZER_KEYS this chunk runs
     * @param  AuditMode  $mode  Phase K1 — see that enum's own docblock.
     *         Defaults to FULL so any pre-Phase-K1 caller (there are
     *         none left in this codebase, but a queued job payload
     *         serialized before this parameter existed could still be
     *         waiting to run) unserializes into the same behavior this
     *         job already had.
     */
    public function __construct(
        string $auditUuid,
        private readonly string $url,
        private readonly array $analyzerKeys,
        private readonly AuditMode $mode = AuditMode::FULL,
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

        // Security, Accessibility, Content, UI/UX, and Business
        // Opportunity all now analyze several crawled pages (up to
        // config('audit.multi_page_analysis.per_page_limit')) rather
        // than just the entry page, and all five need the exact same
        // set of fetched FetchResults to do it. Resolved lazily — only
        // fetched once, on whichever of
        // 'security'/'accessibility'/'content'/'ui_ux'/'business_opportunity'
        // is processed first below — and reused for the others if this
        // chunk happens to run more than one of them, so a chunk
        // containing several of these keys never fetches the same extra
        // pages twice.
        $multiPageFetchResults = null;
        $resolveMultiPageFetchResults = function () use (&$multiPageFetchResults, $crawlResult, $fetchResult, $fetcher): array {
            if ($multiPageFetchResults === null) {
                $multiPageFetchResults = $this->fetchPagesForMultiPageAnalysis($crawlResult, $fetchResult, $fetcher);
            }

            return $multiPageFetchResults;
        };

        foreach ($this->analyzerKeys as $key) {
            $result = match ($key) {
                // Security now analyzes every successfully crawled page
                // (up to config('audit.multi_page_analysis.per_page_limit')),
                // not just the entry page — see resolveMultiPageFetchResults()
                // above and fetchPagesForMultiPageAnalysis() below. Returns
                // a SecurityAuditResult keyed by page URL.
                'security' => $crawlResult->pages === []
                    ? null
                    : $securityAnalyzer->analyzeAll($resolveMultiPageFetchResults(), $crawlResult->startUrl),
                // Same multi-page treatment as security above, sharing the
                // exact same fetched page set via resolveMultiPageFetchResults()
                // rather than fetching it again. Returns an
                // AccessibilityAuditResult keyed by page URL.
                'accessibility' => $crawlResult->pages === []
                    ? null
                    : $accessibilityAnalyzer->analyzeAll($resolveMultiPageFetchResults(), $crawlResult->startUrl),
                // Content now analyzes every successfully crawled page too
                // (same multi-page treatment as security/accessibility
                // above, sharing the same fetched page set) and additionally
                // detects cross-page duplicate content — see
                // ContentAnalyzer::analyzeAll(). Returns a ContentAuditResult
                // keyed by page URL.
                'content' => $crawlResult->pages === []
                    ? null
                    : $contentAnalyzer->analyzeAll($resolveMultiPageFetchResults(), $crawlResult->startUrl),
                // Same multi-page treatment again, sharing the same
                // fetched page set. Returns a UiUxAuditResult keyed by
                // page URL.
                'ui_ux' => $crawlResult->pages === []
                    ? null
                    : $uiUxAnalyzer->analyzeAll($resolveMultiPageFetchResults(), $crawlResult->startUrl),
                // Business Opportunity now runs its Website Problems/SEO
                // Issues/Performance Issues checks across every
                // successfully crawled page too (same multi-page
                // treatment as above, sharing the same fetched page set)
                // — each resulting WebsiteHealthIssue records which page
                // it came from via pageUrl. The remaining website_health
                // categories and the derived score/lead/outreach data
                // stay scoped to the entry page — see
                // BusinessOpportunityAnalyzer::analyzeAll(). Still
                // returns a single BusinessOpportunityResult (not a
                // per-page wrapper), so no change is needed downstream
                // in AssembleAnalysisResultsJob/AnalysisResults.
                'business_opportunity' => $crawlResult->pages === []
                    ? null
                    : $businessOpportunityAnalyzer->analyzeAll($resolveMultiPageFetchResults(), $crawlResult->startUrl),
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
                //
                // Phase K1 (Quick Scan Mode): a QUICK audit never calls
                // PageSpeed Insights, regardless of whether
                // config('audit.pagespeed.enabled') is on globally —
                // that's the single biggest source of audit latency
                // (a real Lighthouse run on Google's own
                // infrastructure), and skipping it is QUICK mode's
                // whole point (see App\Audit\Enums\AuditMode's own
                // docblock). $performanceAnalyzer (method-injected
                // above, whatever PageSpeedInsightsClient the
                // container bound it with) is only used for FULL —
                // for QUICK, a throwaway PerformanceAnalyzer is
                // constructed here with pageSpeedClient explicitly
                // null via a named argument, which skips every
                // OTHER (numeric-threshold) constructor parameter's
                // own default entirely. This reuses
                // PerformanceAnalyzer's own existing "no PSI client
                // configured" fallback path (see
                // fetchPageSpeedMetrics()'s own docblock) rather than
                // adding a second, parallel code path for "PSI
                // disabled" — QUICK mode and "PSI simply isn't
                // configured on this install" produce identical
                // Performance output by construction.
                'performance' => $crawlResult->pages === [] ? null : (
                    $this->mode === AuditMode::QUICK
                        ? (new PerformanceAnalyzer(pageSpeedClient: null))->analyzeAll($crawlResult)
                        : $performanceAnalyzer->analyzeAll($crawlResult)
                ),
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
     * Resolves the FetchResult for every page that Security's,
     * Accessibility's, Content's, UI/UX's, and Business Opportunity's
     * analyzeAll() need — every successfully crawled page, up to
     * config('audit.multi_page_analysis.per_page_limit') taken in crawl
     * order so the entry page and the pages closest to it are always
     * included — fetching whichever of those pages weren't already
     * fetched via WebsiteFetcherServiceInterface::fetchMany(). The entry
     * page's FetchResult ($entryPageFetchResult, fetched once at the top
     * of handle() for every other single-page analyzer here) is reused
     * instead of being fetched a second time.
     *
     * PRODUCTION INCIDENT — read before changing the $successfulPages
     * === [] branch below: this used to `return []` there (no crawled
     * page came back successful — e.g. the site was unreachable, or
     * every request in that wave failed for some other reason). Every
     * one of the five multi-page analyzers that consume this method's
     * own return value (Security/Accessibility/Content/UiUx/
     * BusinessOpportunity — see each one's own analyzeAll(
     * array $fetchResults, ...)) reads its entry page back via
     * `$fetchResults[$startUrl] ?? reset($fetchResults)`. reset() on an
     * EMPTY array returns bool(false), not null — and PHP happily lets
     * that flow through the `??` into a parameter typed FetchResult,
     * because `??` only short-circuits on an actually-MISSING array
     * key, not on a present-but-false-ish one; the TypeError only
     * surfaces later, deep inside that analyzer's own logic
     * (BusinessOpportunityAnalyzer::checkWebsiteModernization() is
     * where it was actually caught, but every one of these five
     * analyzers had the exact same exposure). A real, uncontrolled
     * external website simply being unreachable is an entirely
     * ordinary, expected outcome — not something that should be able
     * to crash five analyzers' worth of a chunk job outright.
     *
     * @return array<string, FetchResult> keyed by page URL
     */
    private function fetchPagesForMultiPageAnalysis(
        CrawlResult $crawlResult,
        FetchResult $entryPageFetchResult,
        WebsiteFetcherServiceInterface $fetcher,
    ): array {
        $successfulPages = array_values(array_filter(
            $crawlResult->pages,
            static fn (CrawledPage $page): bool => $page->success,
        ));

        if ($successfulPages === []) {
            // See this method's own docblock — always return the entry
            // page's own FetchResult rather than an empty array, even
            // though it may itself represent a failed fetch
            // ($entryPageFetchResult->success === false is a normal,
            // well-typed state every analyzer already knows how to
            // handle; a literal bool(false) in its place is not).
            return [$this->url => $entryPageFetchResult];
        }

        $limit = max(1, (int) config('audit.multi_page_analysis.per_page_limit', 20));
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

        return $fetchResults;
    }

    /**
     * Distinguishes this chunk's uniqueness lock from every other
     * chunk of the same audit — without this, ShouldBeUnique would key
     * every chunk of the same audit identically and drop all but the
     * first one dispatched.
     */
    protected function uniqueIdSuffix(): string
    {
        return ':'.md5(implode(',', $this->analyzerKeys));
    }

    protected function overlapKey(): string
    {
        return self::class.':'.$this->auditUuid.$this->uniqueIdSuffix();
    }
}