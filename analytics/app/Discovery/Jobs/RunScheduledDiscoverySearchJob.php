<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Models\DiscoverySearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Re-runs one saved search (App\Models\DiscoverySearch — built in
 * Phase A1/A2, wired into a real feature in Phase F3) and records how
 * many matching discovered_websites are NEW since the last time this
 * job ran it — the "N New Websites Found" notification
 * resources/views/discovery/saved.blade.php displays per saved search.
 *
 * Follows App\Audit\Jobs\AnalyzeChunkJob's own conventions: a final
 * ShouldQueue job carrying its subject model in the constructor
 * (SerializesModels re-hydrates it from the queue payload), method-
 * injected dependencies in handle() rather than the constructor, and a
 * failed() hook that just reports the exception — the same shape
 * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob (Phase D0) already
 * established for this module's own jobs.
 *
 * "New" means discovered_at is strictly after this search's own
 * last_run_at at the START of this run — not new_results_count itself,
 * which this job overwrites with the freshly computed value every time
 * it runs, and not "unseen by a person", since this module has no
 * concept of a saved search's notification being read/dismissed yet.
 * On a search's very first run (last_run_at is still null), every
 * currently-matching site counts as "new" — there is no earlier run to
 * compare against, so nothing the search matches today has been seen
 * through it before.
 *
 * Dispatched by php artisan discovery:run-scheduled-searches (see
 * app/Console/Commands/RunScheduledDiscoverySearchesCommand.php), which
 * routes/console.php schedules to run periodically — not dispatched
 * directly by anything else in this module today.
 */
final class RunScheduledDiscoverySearchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly DiscoverySearch $search,
    ) {
    }

    public function handle(WebsiteSearchService $websiteSearchService): void
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($this->search->filters);
        $previousRunAt = $this->search->last_run_at;

        $query = $websiteSearchService->query($criteria);

        if ($previousRunAt !== null) {
            $query->where('discovered_at', '>', $previousRunAt);
        }

        $newResultsCount = $query->count();

        $this->search->update([
            'new_results_count' => $newResultsCount,
            'last_run_at' => now(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }
}