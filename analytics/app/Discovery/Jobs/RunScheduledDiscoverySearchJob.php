<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Discovery\Ingestion\DiscoveryIngestionService;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
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
 * Phase J1: before recounting, this job now ALSO actually goes and
 * looks for brand new candidates — via every source in
 * config('discovery.sources') (today: GooglePlacesSource,
 * YelpBusinessSource, InternalCrawlSource), through the exact same
 * App\Discovery\Ingestion\DiscoveryIngestionService the search panel's
 * own "Discover More" button uses. Before this phase, "re-run" only
 * ever meant "recount whatever discovered_websites already happened to
 * gain from some OTHER search's own activity" — a saved search could
 * sit at "0 new websites" forever even with a real, growing pool of
 * matching businesses out there it never actually asked about. One
 * ingestion failure (an API outage, a missing API key) is caught and
 * reported rather than aborting the recount that follows — a saved
 * search should still get an accurate LOCAL count even on a run where
 * fresh discovery itself didn't work.
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
 * directly by anything else in this module today. Running real
 * ingestion (Google Places API calls) on an HOURLY schedule for every
 * scheduled search is a genuine API-quota/billing cost to keep in mind
 * if the number of scheduled searches grows large — see
 * GooglePlacesSource's own docblock for its per-call cost (1 search
 * call + up to MAX_DETAIL_LOOKUPS detail lookups, each of which is
 * separately billed).
 */
final class RunScheduledDiscoverySearchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * See App\Discovery\Jobs\DiscoverWebsitesJob::$queue's own docblock
     * for the real production incident (Audit's own AnalyzeChunkJob
     * getting timeout/OOM-killed) this scoping fixes — this job needs
     * the exact same isolation for the exact same reason.
     */
    public function __construct(
        public readonly DiscoverySearch $search,
    ) {
        $this->onQueue('discovery');
    }

    public function handle(WebsiteSearchService $websiteSearchService, DiscoveryIngestionService $ingestionService): void
    {
        $criteria = DiscoveryFilterCriteria::fromRequestFilters($this->search->filters);

        try {
            $sources = collect(config('discovery.sources', []))
                ->map(static fn (string $class): DiscoverySourceInterface => app($class))
                ->all();

            // PRODUCTION INCIDENT — see
            // App\Discovery\Ingestion\DiscoveryIngestionService::discoverAndIngest()'s
            // own docblock: a scheduled run has no browser/request
            // involved at all, but the saved DiscoverySearch this job
            // runs FOR already has its own real owner
            // ($this->search->user_id) — that's who any newly-
            // discovered website from this automated run belongs to.
            $ingestionService->discoverAndIngest($criteria, $sources, $this->search->user_id);
        } catch (Throwable $exception) {
            report($exception);
            // See this class's own docblock — a failed discovery pass
            // still deserves an accurate recount of whatever this
            // search already matches locally, so execution continues
            // rather than aborting the rest of this method.
        }

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