<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Audit\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\BulkAuditBatch;
use Illuminate\Console\Command;

/**
 * PRODUCTION INCIDENT — see
 * App\Audit\Repositories\AuditRepository::findLatestPendingByUrl()'s
 * own docblock for the full incident this command is the other half
 * of fixing: that method now correctly stops REUSING an abandoned
 * audit for a new request, but the abandoned row itself was still
 * left sitting in the database forever showing a status like
 * "Analyzing..." to anyone who looks at it directly (a dashboard
 * entry, a direct link) — genuinely misleading, since nothing is
 * actually still working on it. This command finds any audit that's
 * been in a non-terminal status for over an hour (the same threshold
 * that method uses) and marks it AuditStatus::FAILED — an honest,
 * final status, rather than a phantom "in progress" that will never
 * resolve on its own.
 *
 * Also covers BulkAuditBatch — this app's own real production data
 * showed the SAME underlying incident (a queue/job loss with no
 * corresponding status update) left one bulk batch permanently
 * "pending" too, from the same day as the affected Audit rows. A
 * batch has no equivalent "reuse an existing pending one" bug the way
 * Audit did (each bulk submission always creates its own new batch
 * row), so this is purely a display-cleanliness fix, not a second
 * instance of the create-blocking bug — bundled here since it's the
 * same root cause and the same fix shape, not because it caused the
 * same downstream damage.
 *
 * Scheduled hourly (see routes/console.php) — frequent enough that a
 * genuinely abandoned audit doesn't sit visibly wrong for long, not so
 * frequent that it adds meaningful load running every minute for
 * something this infrequent.
 */
final class MarkAbandonedAuditsFailedCommand extends Command
{
    protected $signature = 'audits:mark-abandoned-failed';

    protected $description = 'Marks audits and bulk audit batches stuck in a non-terminal status for over an hour as failed';

    public function handle(): int
    {
        $auditCount = Audit::query()
            ->whereNotIn('status', [AuditStatus::COMPLETED->value, AuditStatus::FAILED->value])
            ->where('created_at', '<', now()->subHour())
            ->update(['status' => AuditStatus::FAILED->value]);

        if ($auditCount > 0) {
            $this->info("Marked {$auditCount} abandoned audit(s) as failed.");
        }

        $batchCount = BulkAuditBatch::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->update(['status' => 'failed']);

        if ($batchCount > 0) {
            $this->info("Marked {$batchCount} abandoned bulk audit batch(es) as failed.");
        }

        return self::SUCCESS;
    }
}