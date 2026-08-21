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

// Image Everything (Phase S1) — every 10 minutes: frequent enough that
// an expired/abandoned job's real files don't sit on disk for long
// after they should be gone (this app's own explicit "no image stored
// longer than its TTL" requirement), not so frequent that a routine
// cleanup pass with nothing to do adds meaningful load running every
// minute. See App\Console\Commands\CleanupExpiredImageJobsCommand's
// own docblock for exactly what "expired" vs "abandoned" means here.
Schedule::command('images:cleanup-expired')->everyTenMinutes();

// PRODUCTION INCIDENT — see
// App\Audit\Repositories\AuditRepository::findLatestPendingByUrl()'s
// own docblock and App\Console\Commands\MarkAbandonedAuditsFailedCommand's
// own docblock for the full incident (186 real Audit rows — 70% of
// this app's own production data — permanently stuck in a non-
// terminal status, silently blocking every future audit request for
// the same URLs from ever creating a new, properly-owned row). This
// line was confirmed MISSING on this app's own local environment even
// though the command file itself existed — the command was never
// actually scheduled to run automatically until now.
Schedule::command('audits:mark-abandoned-failed')->hourly();

// Website Discovery — Phase J1 (Discover More). This app has no
// persistent `php artisan queue:work` process (the same hosting
// constraint documented throughout this module — see
// App\Discovery\Jobs\DiscoverWebsitesJob's own docblock for the real
// production incident, a 504 Gateway Time-out, that made a queued job
// necessary here in the first place). Instead of a long-running
// worker, this drains whatever's queued every minute, then exits —
// --stop-when-empty is what makes that safe to fire from cron
// repeatedly rather than risking an accumulating pile of overlapping
// long-running processes. withoutOverlapping() is a second layer of
// the same protection, in case one run is still draining a large
// backlog when the next minute's tick fires. --max-time=50 (not the
// full 60s a minute would allow) leaves a margin for shared-hosting
// cron itself firing a little late, so this command's own process is
// never still running when — and therefore never collides with — the
// NEXT minute's invocation.
//
// --queue=default,discovery — a two-part REAL production incident,
// not a naming preference:
//   1. This command originally had no --queue restriction at all,
//      meaning it drained the entire jobs table indiscriminately —
//      including App\Audit\Jobs\AnalyzeChunkJob (the Audit module's
//      own heavy analysis job, dispatched via Bus::batch() from
//      App\Audit\Jobs\FetchAndCrawlJob), which had never previously
//      run under real, timeout-enforcing queue-worker conditions.
//      AnalyzeChunkJob's own multi-page analysis (five analyzers each
//      re-fetching and re-analyzing up to
//      config('audit.multi_page_analysis.per_page_limit') pages) is
//      genuinely heavy — under real enforcement its own $timeout
//      started throwing Illuminate\Queue\TimeoutExceededException, and
//      the worker PROCESS itself was OOM-killed mid-job (exit code
//      137) on this host's memory cap.
//   2. The fix for (1) — scoping this command to --queue=discovery
//      ONLY — went too far the other way: this app's QUEUE_CONNECTION
//      is 'database' (not 'sync', despite an earlier, incorrect
//      assumption elsewhere in this codebase — see
//      App\Audit\Services\AuditService::run()'s own docblock), so
//      App\Audit\Jobs\FetchAndCrawlJob and its own AnalyzeChunkJob
//      batch are REAL queued dispatches on Laravel's default queue
//      name, same as every AuditJob subclass (none of them override
//      $queue). Nothing else in this app has ever drained that default
//      queue — this schedule, even before it existed, was
//      (unintentionally) the only thing making audits progress past
//      "Queued" at all. Restricting it to --queue=discovery only would
//      have silently stopped the ENTIRE Audit pipeline from ever
//      running again.
//
// --memory=128 is the actual fix for the OOM half of incident (1) —
// this makes the WORKER PROCESS gracefully stop and restart itself
// once its own memory usage approaches that ceiling (checked between
// jobs), rather than letting the OS's own out-of-memory killer end it
// mid-job with no cleanup (exit 137). 128 is a conservative starting
// point for a shared-hosting per-process cap — raise it if this host's
// own limit comfortably allows more, or if audits still hit it.
//
// The other half of incident (1) — AnalyzeChunkJob's own multi-page
// analysis being heavy enough to threaten its 90s timeout even without
// memory pressure — is addressed in .env (AUDIT_MAX_PAGES,
// AUDIT_MULTI_PAGE_ANALYSIS_PER_PAGE_LIMIT, AUDIT_QUEUE_CHUNK_SIZE,
// AUDIT_QUEUE_ANALYZE_CHUNK_TIMEOUT_SECONDS), not here — this schedule
// only controls how work gets picked up, not how much of it a single
// chunk takes on.
//
// 'audit-bulk' (Phase K3) — App\Audit\Services\BulkAuditBatchService
// dispatches every audit it creates onto this dedicated queue (see
// App\Audit\Services\Contracts\AuditServiceInterface::run()'s own
// docblock for why) rather than 'default', so a large bulk submission
// (many URLs queued at once) can't starve out someone submitting a
// single, ordinary ad-hoc audit at the same time — --stop-when-empty
// processes whichever of the three queues actually has work waiting,
// in the order listed, every time this command runs.
//
// REAL PRODUCTION INCIDENT (2026-08-17): this combined command was
// observed crashing with exit code 137 (OOM-kill, hitting --memory=128)
// 15+ times in a single day — almost always while working through
// 'default' (AnalyzeChunkJob, FetchAndCrawlJob) or 'discovery'
// (DiscoverWebsitesJob) jobs that are individually heavy enough to
// approach that memory cap on their own. Because 'audit-bulk' is LAST
// in this list, any run that dies partway through 'default'/'discovery'
// never reaches 'audit-bulk' at all that minute — and if 'default'/
// 'discovery' keep receiving crash-prone jobs faster than they can be
// drained, 'audit-bulk' can be starved indefinitely (a bulk batch
// sitting at "Queued" for 15-20+ minutes with zero progress, even
// though the job itself was queued successfully). Giving 'audit-bulk'
// its own independent scheduled command means a crash while draining
// 'default'/'discovery' can no longer block it — this entry gets its
// own process and its own shot every minute regardless of what
// happens to the other two queues. The 'audit-bulk' entry is also
// listed a second time in the combined command below (harmless -- a
// completed/empty queue is just a no-op for --stop-when-empty) purely
// so a slow minute on the dedicated entry still gets backup coverage
// from the combined one.
Schedule::command('queue:work --queue=audit-bulk --stop-when-empty --max-time=50 --memory=128')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('queue:work --queue=default,discovery,audit-bulk --stop-when-empty --max-time=50 --memory=128')
    ->everyMinute()
    ->withoutOverlapping();