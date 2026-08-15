<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Discovery\Jobs\RunScheduledDiscoverySearchJob;
use App\Models\DiscoverySearch;
use Illuminate\Console\Command;

/**
 * Dispatches App\Discovery\Jobs\RunScheduledDiscoverySearchJob for
 * every DiscoverySearch with is_scheduled=true — the console entry
 * point routes/console.php's Schedule::command() calls periodically
 * (Phase F4). Kept as its own command (rather than a raw
 * Schedule::call() closure directly in routes/console.php) so it's
 * independently runnable/testable via `php artisan
 * discovery:run-scheduled-searches` — e.g. to trigger it manually
 * without waiting for the schedule, or to write a feature test against
 * it — the same reason this app's other scheduled work is expected to
 * live in a dedicated class rather than an inline closure.
 */
final class RunScheduledDiscoverySearchesCommand extends Command
{
    protected $signature = 'discovery:run-scheduled-searches';

    protected $description = 'Dispatch RunScheduledDiscoverySearchJob for every scheduled saved Discovery search.';

    public function handle(): int
    {
        $searches = DiscoverySearch::query()->where('is_scheduled', true)->get();

        foreach ($searches as $search) {
            RunScheduledDiscoverySearchJob::dispatch($search);
        }

        $this->info(sprintf(
            'Dispatched %d scheduled discovery search job(s).',
            $searches->count(),
        ));

        return self::SUCCESS;
    }
}