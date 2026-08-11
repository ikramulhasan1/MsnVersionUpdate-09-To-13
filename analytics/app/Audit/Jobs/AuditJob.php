<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Enums\AuditStatus;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Common retry/backoff/overlap policy for every job in the audit
 * pipeline (fetch+crawl, analyzer chunks, assembly). Concrete jobs
 * only add their own handle() and, where duplicate *dispatch* also
 * needs to be prevented (not just concurrent *execution*), implement
 * {@see \Illuminate\Contracts\Queue\ShouldBeUnique} via the
 * {@see \App\Audit\Jobs\Concerns\HasAuditUniqueness} trait.
 *
 * Two independent duplicate-processing guards are layered here on
 * purpose: ShouldBeUnique stops a second dispatch for the same audit
 * (or the same chunk) from ever being queued, while WithoutOverlapping
 * additionally stops two already-queued attempts (e.g. a slow retry
 * racing a manual re-dispatch) from *executing* at the same time —
 * the overlapping one is released back onto the queue with a short
 * delay instead of running concurrently.
 */
abstract class AuditJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;

    /**
     * Maximum seconds this job may run before the worker kills it.
     * Without this, a job stuck on a slow/unresponsive website (or a
     * deadlocked HTTP call) would occupy a worker indefinitely, and —
     * combined with the database queue's retry_after — could let a
     * second worker pick up the same job while the first is still
     * running. Concrete jobs override defaultTimeoutSeconds() rather
     * than this property directly, since the safe value differs a lot
     * between e.g. FetchAndCrawlJob (does real HTTP work) and
     * AssembleAnalysisResultsJob (only reads cache/DB).
     *
     * A timed-out attempt is treated the same as any other exception:
     * it consumes one of $tries and is retried via backoff() rather
     * than failing the audit immediately, since a slow response is
     * often transient.
     */
    public int $timeout;

    /**
     * One uncaught exception is enough to trigger a retry via backoff();
     * we don't want a burst of exceptions within a single attempt to
     * burn through retries faster than intended.
     */
    public int $maxExceptions = 1;

    protected string $auditUuid;

    public function __construct()
    {
        $this->tries = (int) config('audit.queue.tries', 3);
        $this->timeout = $this->defaultTimeoutSeconds();
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return array_map('intval', (array) config('audit.queue.backoff_seconds', [10, 30, 90]));
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->overlapKey()))
                ->releaseAfter(5)
                ->expireAfter((int) config('audit.queue.overlap_lock_seconds', 600)),
        ];
    }

    /**
     * Seconds this job type is allowed to run. Overridden per job
     * class; the base default is deliberately conservative since it
     * only applies to a job that forgets to declare its own.
     */
    protected function defaultTimeoutSeconds(): int
    {
        return (int) config('audit.queue.default_timeout_seconds', 120);
    }

    /**
     * Scopes the overlap lock. Base implementation locks per-audit;
     * jobs that run several independent units of work per audit (e.g.
     * analyzer chunks, which must be allowed to run in parallel with
     * each other) override this to scope the lock more narrowly.
     */
    protected function overlapKey(): string
    {
        return static::class . ':' . $this->auditUuid;
    }

    /**
     * Marks the audit FAILED once retries are exhausted — but only if
     * it isn't already in a finished state, so a slow/duplicate failure
     * callback can never resurrect or overwrite a COMPLETED audit.
     */
    protected function markAuditFailedIfNotFinished(Throwable $e): void
    {
        $auditRepository = app(AuditRepositoryInterface::class);
        $audit = $auditRepository->findByUuid($this->auditUuid);

        if ($audit !== null && ! $audit->status->isFinished()) {
            $auditRepository->updateStatus($audit, AuditStatus::FAILED);
            app(AuditCacheServiceInterface::class)->putProgress($this->auditUuid, 100, 'Audit failed.');
        }

        report($e);
    }
}
