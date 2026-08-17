<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Enums\AuditStatus;
use App\Audit\Enums\BulkAuditBatchStatus;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Models\BulkAuditBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Phase K4 (Bulk Audit — parallel fetching). The whole reason a bulk
 * batch is faster than N sequential audits: WebsiteFetcherServiceInterface::fetchMany()
 * already fetches several URLs CONCURRENTLY, via Guzzle promises (see
 * that method's own docblock — built for the crawler's own per-wave
 * page fetching, reused here as-is rather than reimplementing the
 * same concurrent-fetch logic a second time with duplicated
 * timeout/header/User-Agent configuration).
 *
 * Runs ONCE per batch, before any of its audits' own
 * FetchAndCrawlJob — not instead of it. This job only pre-warms the
 * URL-keyed fetch cache (AuditCacheServiceInterface::putFetchResult(),
 * using the exact same cache key rememberFetchResult() itself reads
 * from) for every audit's own homepage, then dispatches each audit's
 * ordinary FetchAndCrawlJob exactly as
 * App\Audit\Services\BulkAuditBatchService used to do directly in its
 * own createBatch() loop. When FetchAndCrawlJob later calls
 * rememberFetchResult() for that same URL, it's a cache HIT — the
 * actual network fetch for that entry page never happens a second
 * time, sequentially, per audit. FetchAndCrawlJob's own CRAWL step
 * (which, for a Quick Scan, has nothing left to do beyond the entry
 * page — see App\Audit\Enums\AuditMode's own docblock — but for a
 * Full Audit still needs to visit further pages) and every analyzer
 * chunk after it are untouched by this job entirely.
 *
 * CONCURRENCY BOUND: fetchMany() itself has no concurrency LIMIT — it
 * fires every URL passed to it as a simultaneous outbound request,
 * which is fine for the crawler's own typical wave size but not
 * appropriate for a bulk batch that could hold up to
 * DiscoveryController::MAX_BULK_AUDIT-many URLs at once (up to 100).
 * self::CHUNK_SIZE bounds it here instead, by calling fetchMany() once
 * per chunk of that many URLs rather than once for the whole batch —
 * concurrent WITHIN a chunk (the actual speed win), bounded ACROSS
 * chunks (so this app's own outbound connection limits, and target
 * servers' own rate limiting/abuse detection, aren't hit by firing 100
 * simultaneous requests from one process).
 */
final class BulkFetchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * See this class's own docblock for why this bounds fetchMany()'s
     * own otherwise-unbounded concurrency — 8 sits within the "5-10"
     * range this whole bulk-audit feature was planned around.
     */
    private const int CHUNK_SIZE = 8;

    public int $tries = 1;

    /**
     * Generous: this job may make MAX_BULK_AUDIT-many HTTP requests,
     * just bounded CHUNK_SIZE at a time rather than all at once — the
     * total wall-clock time is roughly (batch size / CHUNK_SIZE) times
     * one page's own fetch timeout, not one page's timeout alone.
     */
    public int $timeout = 600;

    public string $queue = 'audit-bulk';

    public function __construct(
        private readonly string $batchUuid,
    ) {
    }

    public function handle(
        WebsiteFetcherServiceInterface $fetcher,
        AuditCacheServiceInterface $cache,
    ): void {
        $batch = BulkAuditBatch::query()->where('uuid', $this->batchUuid)->first();

        if ($batch === null) {
            return;
        }

        $audits = $batch->audits()->where('status', AuditStatus::QUEUED->value)->get();

        if ($audits->isEmpty()) {
            return;
        }

        Collection::make($audits)
            ->pluck('url')
            ->unique()
            ->values()
            ->chunk(self::CHUNK_SIZE)
            ->each(function (Collection $urlChunk) use ($fetcher, $cache): void {
                $results = $fetcher->fetchMany($urlChunk->all());

                foreach ($results as $url => $fetchResult) {
                    $cache->putFetchResult($url, $fetchResult);
                }
            });

        foreach ($audits as $audit) {
            $dispatch = FetchAndCrawlJob::dispatch($audit->uuid, $audit->url);
            $dispatch->onQueue($this->queue);
        }
    }

    /**
     * If this job itself fails outright (exhausted its one try — see
     * $tries above), none of this batch's audits ever got their
     * FetchAndCrawlJob dispatched at all, and would otherwise sit at
     * QUEUED forever with no further job ever coming to move them
     * along. Explicitly marks every still-QUEUED audit in the batch
     * FAILED (and rolls the batch's own failed_count/status up to
     * match) rather than leaving them silently stuck — the same "a
     * batch should always reach a real, visible end state" reasoning
     * AssembleAnalysisResultsJob's own finally()-driven completion
     * already follows for an individual audit's own analyzer batch.
     */
    public function failed(Throwable $exception): void
    {
        report($exception);

        $batch = BulkAuditBatch::query()->where('uuid', $this->batchUuid)->first();

        if ($batch === null) {
            return;
        }

        $stillQueued = $batch->audits()->where('status', AuditStatus::QUEUED->value)->get();

        if ($stillQueued->isEmpty()) {
            return;
        }

        foreach ($stillQueued as $audit) {
            app(AuditRepositoryInterface::class)->updateStatus($audit, AuditStatus::FAILED);
        }

        $batch->increment('failed_count', $stillQueued->count());
        $batch->update(['status' => BulkAuditBatchStatus::COMPLETED->value]);
    }
}