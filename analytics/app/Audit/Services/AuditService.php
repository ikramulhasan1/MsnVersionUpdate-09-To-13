<?php

declare(strict_types=1);

namespace App\Audit\Services;

use App\Audit\DTO\CreateAuditData;
use App\Audit\Enums\AuditStatus;
use App\Audit\Jobs\FetchAndCrawlJob;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Models\Audit;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AuditService implements AuditServiceInterface
{
    public function __construct(
        private readonly AuditRepositoryInterface $auditRepository,
    ) {
    }

    public function submit(CreateAuditData $data): Audit
    {
        // Duplicate audit prevention: reuse an in-flight audit for the same URL
        // instead of queuing a second one.
        $pending = $this->auditRepository->findLatestPendingByUrl($data->url);

        if ($pending !== null) {
            return $pending;
        }

        $audit = $this->auditRepository->create([
            'uuid' => (string) Str::uuid(),
            'url' => $data->url,
            'status' => AuditStatus::QUEUED->value,
            // Phase K1 — see App\Audit\Enums\AuditMode's own docblock.
            // Persisted on the Audit row itself (not just held in this
            // DTO) so FetchAndCrawlJob/AnalyzeChunkJob can read it back
            // later without needing it threaded through every job
            // constructor a second time.
            'mode' => $data->mode->value,
        ]);

        // The actual pipeline dispatch happens in run() (see this
        // class's own docblock there for what "dispatch" really means
        // on this app's QUEUE_CONNECTION=database setup) —
        // AuditController::store() is responsible for actually calling
        // run(), after (where the server supports it) flushing the
        // redirect response to the browser first so the result page's
        // progress bar has something to poll while the queue worker
        // picks the job up.
        return $audit;
    }

    public function run(Audit $audit, ?string $queue = null): void
    {
        // NOTE: this app's actual QUEUE_CONNECTION is 'database', not
        // 'sync' — the comments below describing FetchAndCrawlJob::dispatch()
        // as executing "immediately, in this same process" were WRONG
        // relative to that real setting (see
        // routes/console.php's own scheduled queue:work comment for the
        // production incident — a 504 Gateway Time-out on a completely
        // different module, plus an OOM-killed worker — that surfaced
        // this same mistaken assumption and corrected it there). The
        // dispatch below is a REAL queued job: it writes one row to the
        // `jobs` table and returns almost immediately; the actual fetch/
        // crawl/analyze work happens later, in whatever process next
        // runs `php artisan queue:work` against that job's own queue.
        //
        // set_time_limit() below is a holdover from when this method's
        // own docblock (incorrectly) assumed synchronous, in-request
        // execution — with a real queued dispatch it has little left to
        // protect against (this method returns almost instantly after
        // the dispatch() call below), but is left in place rather than
        // removed as an out-of-scope cleanup unrelated to what actually
        // needed fixing here (Phase K3's own $queue parameter).
        set_time_limit((int) config('audit.queue.fetch_and_crawl_timeout_seconds', 300) + 30);

        // Deliberately going through the normal dispatch() rather than
        // calling the job's handle() directly keeps this test-friendly
        // — Queue::fake() / Bus::fake() intercept it exactly as they
        // would any other queued dispatch — and preserves
        // FetchAndCrawlJob's ShouldBeUnique / WithoutOverlapping guards,
        // both of which a direct handle() call would silently skip.
        //
        // $queue (Phase K3) — see this method's own interface docblock
        // for why App\Audit\Services\BulkAuditBatchService passes
        // 'audit-bulk' here instead of leaving it null. onQueue() is
        // only called when a queue was actually specified, rather than
        // always calling onQueue($queue) with a possibly-null value —
        // FetchAndCrawlJob's own $queue property then simply stays
        // unset for an ordinary, non-bulk audit, exactly as it already
        // did before this parameter existed.
        $dispatch = FetchAndCrawlJob::dispatch($audit->uuid, $audit->url);

        if ($queue !== null) {
            $dispatch->onQueue($queue);
        }
    }

    public function findOrFail(string $uuid): Audit
    {
        $audit = $this->auditRepository->findByUuid($uuid);

        if ($audit === null) {
            throw new NotFoundHttpException('Audit not found.');
        }

        return $audit;
    }
}