<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ImageJobStatus;
use App\Models\ImageProcessingJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Image Everything (Phase S1) — the ONLY thing that actually deletes
 * image files/job rows in this whole feature. Two distinct cleanup
 * passes:
 *
 *   1. EXPIRED — any job past its own $expires_at, regardless of
 *      status. This is the normal, expected lifecycle end for every
 *      job that ever completes successfully — nothing wrong happened,
 *      the job's own TTL (config('image-processing.job_ttl_hours'))
 *      simply ran out.
 *
 *   2. ABANDONED — a job stuck in PENDING or FAILED for longer than
 *      config('image-processing.abandoned_after_minutes') (default 30),
 *      even if its own expires_at hasn't arrived yet. Covers: someone
 *      started an upload and closed their browser before finishing (a
 *      real PENDING job with no real activity since), or a job that
 *      failed outright (a crashed processing step) — neither should
 *      sit around for the FULL 2-hour TTL just because nothing marked
 *      it as done.
 *
 * Deleting the STORAGE FOLDER and the DATABASE ROWS happens together,
 * in that order (files first, then DB) — see handle()'s own docblock
 * for why this order matters. Never deletes only one half.
 */
final class CleanupExpiredImageJobsCommand extends Command
{
    protected $signature = 'images:cleanup-expired';

    protected $description = 'Deletes expired and abandoned image processing jobs, both their files and database records';

    /**
     * Files are deleted BEFORE the database row — if this command
     * crashed partway through and only got as far as deleting files,
     * the ORPHANED DB ROW is still discoverable and this command
     * will simply try again (and succeed, since Storage::deleteDirectory()
     * on an already-gone directory is a safe no-op) on its next
     * scheduled run. The REVERSE order (DB row deleted first, then
     * files) would be worse: a crash between those two steps would
     * leave real files on disk with NOTHING left in the database
     * pointing at them, silently invisible to this command's own
     * "find expired jobs" query forever.
     */
    public function handle(): int
    {
        $expiredCount = $this->cleanup(
            ImageProcessingJob::query()->where('expires_at', '<', now()),
        );

        $abandonedMinutes = (int) config('image-processing.abandoned_after_minutes', 30);

        $abandonedCount = $this->cleanup(
            ImageProcessingJob::query()
                ->whereIn('status', [ImageJobStatus::PENDING->value, ImageJobStatus::FAILED->value])
                ->where('last_activity_at', '<', now()->subMinutes($abandonedMinutes)),
        );

        $this->info("Cleaned up {$expiredCount} expired and {$abandonedCount} abandoned image job(s).");

        return self::SUCCESS;
    }

    private function cleanup(\Illuminate\Database\Eloquent\Builder $query): int
    {
        $count = 0;

        // chunkById rather than get()->each() — this app's own image
        // jobs could genuinely number in the hundreds/thousands over
        // time; loading them all into memory at once for a routine
        // 10-minute cleanup pass isn't necessary.
        $query->chunkById(50, function ($jobs) use (&$count): void {
            foreach ($jobs as $job) {
                Storage::disk('private-images')->deleteDirectory($job->uuid);
                $job->items()->delete();
                $job->delete();
                $count++;
            }
        });

        return $count;
    }
}