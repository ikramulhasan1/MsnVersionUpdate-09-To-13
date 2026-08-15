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