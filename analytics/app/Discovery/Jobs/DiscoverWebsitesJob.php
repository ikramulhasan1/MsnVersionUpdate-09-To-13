<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Discovery\Ingestion\DiscoveryIngestionService;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Runs App\Discovery\Ingestion\DiscoveryIngestionService for one set of
 * search filters — the REAL fix for a 504 Gateway Time-out
 * DiscoveryController::discover() hit in production (see that method's
 * own docblock for the full incident history, including why an
 * earlier fastcgi_finish_request()-based fix wasn't reliable enough on
 * this app's specific host).
 *
 * WHY A QUEUED JOB INSTEAD OF fastcgi_finish_request(): that function
 * only works when PHP is actually running under the FPM SAPI, and even
 * then only if every layer between the browser and PHP (reverse
 * proxies, caching layers) correctly treats "PHP told FastCGI it's
 * done" the same as "the whole request is finished" — on a host mixing
 * nginx with a LiteSpeed backend (see the "x-lscache" response header
 * evidence that surfaced during this same incident), that assumption
 * turned out not to hold. A queued job removes ALL of that uncertainty
 * at the cost of removing the CONTROLLER entirely from the loop: the
 * HTTP request that dispatches this job does no external API work
 * itself — it only writes one row to the `jobs` table — so there is
 * nothing left for nginx's own gateway timeout to time out on.
 *
 * HOW THIS ACTUALLY RUNS WITHOUT A PERSISTENT QUEUE WORKER: this app's
 * hosting has no long-running `php artisan queue:work` process (the
 * same constraint documented throughout this module — see
 * DiscoveryController::bulkAudit()'s own docblock for the same
 * limitation elsewhere). Instead, routes/console.php schedules
 * `queue:work --stop-when-empty` to run every minute via Laravel's own
 * scheduler — the exact same cron infrastructure
 * (`* * * * * php artisan schedule:run`) this module's Phase F4
 * scheduled-search feature already requires to exist for ITS OWN
 * hourly job to fire, so this adds no NEW infrastructure requirement,
 * only a new scheduled entry alongside the one already there.
 * `--stop-when-empty` makes that command drain whatever's queued then
 * exit cleanly, rather than running forever — safe to fire every
 * minute from cron without ever leaving an orphaned process behind.
 *
 * Practical effect: a "Discover More" click is no longer
 * near-instant-then-background — it queues here and waits for the
 * NEXT scheduler tick (up to ~60s) before this job even starts
 * running, then the same 10-30s (typical case) of actual API calls on
 * top of that. See DiscoveryController::discover()'s own docblock for
 * the status message this trade-off is communicated with.
 */
final class DiscoverWebsitesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    /**
     * Comfortably under the --max-time=50 cap
     * routes/console.php's own scheduled `queue:work` invocation uses —
     * see that file's own comment for why 50, not 60: shared-hosting
     * cron itself can be a little late firing, so this leaves a margin
     * rather than assuming the full minute is available.
     */
    public int $timeout = 45;

    /**
     * A dedicated queue name — a real production incident, not an
     * organizational nicety: routes/console.php's own scheduled
     * `queue:work` command (added for THIS job) was originally
     * unscoped, meaning it drained the entire default `jobs` table —
     * including App\Audit\Jobs\AnalyzeChunkJob, Audit's own heavy
     * analysis job, which this app's Audit module had never previously
     * needed a real queue worker for. Once that scheduled command
     * started actually running AnalyzeChunkJob under genuine queue
     * worker conditions, its own $timeout became enforced for real
     * (Illuminate\Queue\TimeoutExceededException) in a way it may never
     * have been exercised under before, and the worker process itself
     * was OOM-killed (exit code 137) partway through — audits that had
     * been working started failing. Scoping this job (and
     * RunScheduledDiscoverySearchJob) to their own 'discovery' queue,
     * and scoping routes/console.php's scheduled `queue:work` to
     * --queue=discovery specifically, means that cron entry can never
     * touch Audit's own queued work again, regardless of how or why
     * AnalyzeChunkJob ends up in the jobs table.
     */
    public string $queue = 'discovery';

    public function __construct(
        private readonly DiscoveryFilterCriteria $criteria,
    ) {
    }

    public function handle(DiscoveryIngestionService $ingestionService): void
    {
        $sources = collect(config('discovery.sources', []))
            ->map(static fn (string $class): DiscoverySourceInterface => app($class))
            ->all();

        $ingestionService->discoverAndIngest($this->criteria, $sources);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }
}