<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Enums\AuditStatus;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Jobs\Concerns\HasAuditUniqueness;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Entry point of the audit pipeline. Fetches and crawls $url (reusing
 * whatever is already cached for that URL via AuditCacheService — see
 * that class for why fetch/crawl are cached per-URL rather than
 * per-audit), then dispatches the eight single-purpose analyzer jobs
 * as a chunked, parallel {@see \Illuminate\Bus\Batch}.
 *
 * Chunking here means grouping the fixed set of analyzer jobs into a
 * configurable number of AnalyzeChunkJob instances (audit.queue.chunk_size
 * each) rather than dispatching one job per analyzer unconditionally:
 * it bounds how many analyzer jobs — and therefore how much of the
 * cached FetchResult/CrawlResult — a single worker process may be
 * holding in memory concurrently, and keeps the batch a fixed, small
 * size regardless of how many analyzers the pipeline grows to.
 */
final class FetchAndCrawlJob extends AuditJob implements ShouldBeUnique
{
    use HasAuditUniqueness;

    public function __construct(string $auditUuid, private readonly string $url)
    {
        parent::__construct();

        $this->auditUuid = $auditUuid;
    }

    /**
     * Generous relative to the other jobs: this is the only one doing
     * real network I/O across potentially audit.crawler.max_pages
     * pages, each allowed up to audit.crawler.timeout seconds, plus
     * link-reachability checks — so it legitimately needs the most
     * headroom of any job in the pipeline.
     */
    protected function defaultTimeoutSeconds(): int
    {
        return (int) config('audit.queue.fetch_and_crawl_timeout_seconds', 300);
    }

    public function handle(
        AuditRepositoryInterface $auditRepository,
        AuditCacheServiceInterface $cache,
        WebsiteFetcherServiceInterface $fetcher,
        WebsiteCrawlerServiceInterface $crawler,
    ): void {
        $audit = $auditRepository->findByUuid($this->auditUuid);

        if ($audit === null || $audit->status->isFinished()) {
            // Audit was deleted, or a duplicate/late attempt arrived
            // after the audit already finished — nothing to do.
            return;
        }

        $auditRepository->updateStatus($audit, AuditStatus::FETCHING);
        $cache->putProgress($this->auditUuid, 5, 'Fetching the page…');

        $cache->rememberFetchResult(
            $this->url,
            fn (): FetchResult => $fetcher->fetch($this->url),
        );

        $auditRepository->updateStatus($audit, AuditStatus::CRAWLING);
        $cache->putProgress($this->auditUuid, 10, 'Crawling pages…');

        $auditUuid = $this->auditUuid;

        $cache->rememberCrawlResult(
            $this->url,
            fn (): CrawlResult => $crawler->crawl(
                $this->url,
                onProgress: function (int $pagesCrawled, int $maxPages) use ($cache, $auditUuid): void {
                    // Crawling is the slowest phase by far (real, if now
                    // concurrent, network I/O across every page) so it
                    // gets the widest percentage band — 10-70% — with
                    // per-wave granularity instead of jumping straight
                    // from "crawling" to "analyzing".
                    $share = $maxPages > 0 ? min(1.0, $pagesCrawled / $maxPages) : 1.0;
                    $percent = 10 + (int) round($share * 60);

                    $cache->putProgress(
                        $auditUuid,
                        $percent,
                        "Crawling pages ({$pagesCrawled} of {$maxPages})…",
                    );
                },
            ),
        );

        $auditRepository->updateStatus($audit, AuditStatus::ANALYZING);
        $cache->putProgress($this->auditUuid, 70, 'Running analyzers…');

        $url = $this->url;

        $chunkSize = max(1, (int) config('audit.queue.chunk_size', 3));

        $chunks = Collection::make(AnalyzeChunkJob::ANALYZER_KEYS)
            ->chunk($chunkSize)
            ->map(fn (Collection $keys): AnalyzeChunkJob => new AnalyzeChunkJob($auditUuid, $url, $keys->values()->all()))
            ->all();

        Bus::batch($chunks)
            ->name("audit-analysis:{$auditUuid}")
            ->allowFailures()
            // Analyzer chunks each report a fraction of the fixed 70-90%
            // band as they complete — Laravel's Batch already tracks
            // processed-vs-total job counts, so this just maps that
            // ratio onto our percentage scale rather than reinventing it.
            //
            // Deliberately resolved from the container inside the
            // closure (not captured via `use ($cache, ...)`): batch
            // progress/then/catch/finally callbacks are serialized into
            // the job_batches table so they still work when triggered
            // later by a different job's completion, regardless of
            // queue driver. $cache wraps Laravel's CacheRepository,
            // which — for the database/file drivers this app uses —
            // holds a live PDO/file-handle underneath, and PHP refuses
            // to serialize a PDO object at all ("Serialization of 'PDO'
            // is not allowed"), which crashed every audit at this step.
            // Only serializable scalars ($auditUuid) may be captured.
            ->progress(function ($batch) use ($auditUuid): void {
                $percent = 70 + (int) round($batch->progress() * 0.2);

                app(AuditCacheServiceInterface::class)->putProgress($auditUuid, $percent, 'Running analyzers…');
            })
            // finally() (not then()) always runs exactly once, whether
            // every chunk succeeded or some exhausted their retries —
            // AssembleAnalysisResultsJob is responsible for deciding,
            // from which analyzer fragments actually made it into the
            // cache, whether the audit is COMPLETED or FAILED.
            ->finally(function () use ($auditUuid, $url): void {
                AssembleAnalysisResultsJob::dispatch($auditUuid, $url);
            })
            ->dispatch();
    }

    public function failed(Throwable $e): void
    {
        $this->markAuditFailedIfNotFinished($e);
    }
}
