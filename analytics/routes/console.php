<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Website Discovery — Phase F4 (Scheduled Search + New Website
// Detection). Laravel 11's console scheduler lives directly in this
// file (no app/Console/Kernel.php in this app's structure — see
// bootstrap/app.php's own ->withRouting(commands: ...) wiring).
// Hourly is a reasonable default cadence for "check for newly
// discovered sites matching my saved search" — frequent enough to be
// useful, not so frequent that it re-runs every saved search's full
// query needlessly often. app/Console/Commands/RunScheduledDiscoverySearchesCommand.php
// is the actual dispatch logic; this line only schedules it.
Schedule::command('discovery:run-scheduled-searches')->hourly();

// Website Discovery — Phase J1 (Discover More). This app has no
// persistent `php artisan queue:work` process (the same hosting
// constraint documented throughout this module — see
// App\Discovery\Jobs\DiscoverWebsitesJob's own docblock for the real
// production incident, a 504 Gateway Time-out, that made a queued job
// necessary here in the first place). Instead of a long-running
// worker, this drains whatever's queued on the 'discovery' queue
// (App\Discovery\Jobs\DiscoverWebsitesJob, RunScheduledDiscoverySearchJob)
// every minute, then exits — --stop-when-empty is what makes that
// safe to fire from cron repeatedly rather than risking an
// accumulating pile of overlapping long-running processes.
// withoutOverlapping() is a second layer of the same protection, in
// case one run is still draining a large backlog when the next
// minute's tick fires. --max-time=50 (not the full 60s a minute would
// allow) leaves a margin for shared-hosting cron itself firing a
// little late, so this command's own process is never still running
// when — and therefore never collides with — the NEXT minute's
// invocation.
//
// --queue=discovery — a REAL production incident, not a naming
// preference: this command originally had no --queue restriction,
// meaning it drained the ENTIRE default jobs table — including
// App\Audit\Jobs\AnalyzeChunkJob, the Audit module's own heavy
// analysis job, which had never previously run under real queue-
// worker conditions (no queue worker existed before this Discovery
// feature needed one). Once this schedule started actually processing
// AnalyzeChunkJob for real, its own $timeout became genuinely enforced
// (Illuminate\Queue\TimeoutExceededException) and the worker process
// itself was OOM-killed mid-job (exit code 137) — audits that used to
// complete started failing. Restricting this command to
// --queue=discovery, and scoping every Discovery job to that same
// queue name (see DiscoverWebsitesJob::$queue's own docblock), means
// this scheduled command can never touch Audit's own queued work
// again, regardless of how or why an Audit job ends up in the jobs
// table.
//
// This piggybacks on the exact same cron entry
// (`* * * * * php artisan schedule:run`) the line above already
// requires to exist for ITS OWN hourly job to fire at all — no new
// infrastructure requirement, just a second scheduled entry.
Schedule::command('queue:work --queue=discovery --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();