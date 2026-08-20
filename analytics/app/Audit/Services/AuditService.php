<?php

declare(strict_types=1);

namespace App\Audit\Services;

use App\Audit\DTO\CreateAuditData;
use App\Audit\Enums\AuditStatus;
use App\Audit\Exceptions\PlanLimitExceededException;
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

    /**
     * @throws PlanLimitExceededException when a LOGGED-IN user's
     *         current plan blocks this — see that exception's own
     *         docblock. Never thrown for an anonymous submission (the
     *         Phase N1.5 Quick Audit Hero,
     *         App\Http\Controllers\AuditController::quickAudit()) —
     *         there's no plan to check against at all when auth()->id()
     *         is null, so this whole block is skipped entirely rather
     *         than blocking the one deliberately-public entry point
     *         this app has.
     */
    public function submit(CreateAuditData $data): Audit
    {
        $user = auth()->user();

        // PRODUCTION GAP CLOSED — see
        // App\Http\Middleware\EnsurePlanAllowsFeature's own identical
        // comment for the full "why": an Admin has no plan/trial
        // limitation at all, by this app's own explicit requirement —
        // ! $user->isAdmin() is what skips this ENTIRE block (both the
        // feature check and the daily limit) for an Admin account,
        // regardless of whether they happen to have a plan assigned.
        if ($user !== null && ! $user->isAdmin()) {
            if (! $user->planAllowsFeature('run-audit')) {
                throw new PlanLimitExceededException(
                    $user->trialExpired()
                        ? 'Your free trial has ended. Upgrade your plan to keep running audits.'
                        : 'Your current plan doesn\'t include running audits.',
                );
            }

            $dailyLimit = $user->plan?->dailyAuditLimit();

            if ($dailyLimit !== null) {
                $todayCount = Audit::query()
                    ->where('user_id', $user->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->count();

                if ($todayCount >= $dailyLimit) {
                    throw new PlanLimitExceededException(
                        "You've reached your plan's limit of {$dailyLimit} audit(s) per day. "
                            .'Upgrade your plan to run more today.',
                    );
                }
            }
        }

        // Duplicate audit prevention: reuse an in-flight audit for the same URL
        // instead of queuing a second one.
        //
        // KNOWN EDGE CASE (Phase N2) — if a SECOND person requests the
        // same URL while a FIRST person's audit for it is still
        // pending, this returns the FIRST person's own Audit row —
        // meaning App\Notifications\AuditCompletedNotification only
        // ever reaches the first person, not the second. Accepted as
        // a real, documented limitation rather than fixed here:
        // properly fixing it means tracking multiple "watchers" per
        // audit (a many-to-many, not the simple belongsTo user_id
        // this phase added), which is more machinery than a rare
        // coincidental-timing edge case warrants right now.
        $pending = $this->auditRepository->findLatestPendingByUrl($data->url);

        if ($pending !== null) {
            return $pending;
        }

        $audit = $this->auditRepository->create([
            'uuid' => (string) Str::uuid(),
            // Phase N2 — see App\Models\Audit's own docblock on this
            // column. auth()->id() rather than requiring a $userId
            // constructor param threaded in from every caller: every
            // route that reaches submit() is already behind the
            // 'auth' middleware (Phase N1), so a real authenticated
            // user is always available here without needing to change
            // this method's own signature.
            'user_id' => auth()->id(),
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