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
        ]);

        // No queue worker in this deployment — the audit pipeline runs
        // directly in this PHP process (see run()) rather than being
        // dispatched onto a queue for a separate `queue:work` process to
        // pick up. AuditController::store() is responsible for actually
        // calling run(), after (where the server supports it) flushing
        // the redirect response to the browser first so the result
        // page's progress bar has something to poll while this process
        // keeps working — see that method for why the call isn't made
        // here.
        return $audit;
    }

    public function run(Audit $audit): void
    {
        // php.ini's max_execution_time (commonly 30-60s under a web
        // SAPI) would otherwise kill this mid-audit — a real queue
        // worker process never hits this because PHP's CLI SAPI
        // defaults max_execution_time to unlimited. Since this now runs
        // as a (possibly long) continuation of a web request instead,
        // it needs its own explicit budget: generous enough to cover a
        // slow/worst-case crawl, matching the timeout FetchAndCrawlJob
        // itself would have been given by a queue worker.
        set_time_limit((int) config('audit.queue.fetch_and_crawl_timeout_seconds', 300) + 30);

        // QUEUE_CONNECTION=sync means this dispatch executes
        // immediately, in this same process — no `queue:work` worker
        // required. Deliberately going through the normal dispatch()
        // rather than calling the job's handle() directly keeps this
        // test-friendly — Queue::fake() / Bus::fake() intercept it
        // exactly as they would any other queued dispatch — and
        // preserves FetchAndCrawlJob's ShouldBeUnique / WithoutOverlapping
        // guards, both of which a direct handle() call would silently
        // skip. The sync driver already calls the job's failed()
        // callback automatically on an uncaught exception, the same as
        // a real worker would.
        FetchAndCrawlJob::dispatch($audit->uuid, $audit->url);
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
