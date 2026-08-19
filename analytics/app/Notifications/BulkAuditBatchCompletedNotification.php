<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\BulkAuditBatch;
use Illuminate\Notifications\Notification;

/**
 * Phase N2 (Dynamic Notification System) — dispatched from
 * App\Audit\Jobs\AssembleAnalysisResultsJob's own
 * updateBulkAuditBatchIfAny() once
 * $batch->completed_count + $batch->failed_count reaches
 * $batch->total_count (the batch's own existing "is it actually done"
 * check — this notification piggybacks on that SAME condition rather
 * than introducing a second one). Deliberately the ONLY notification a
 * bulk audit produces — see AuditCompletedNotification's own docblock
 * for why each individual audit inside a batch stays silent: a 200-
 * website bulk batch would otherwise flood someone with 200 separate
 * "audit completed" notifications, exactly the kind of noise that
 * trains a person to ignore notifications altogether.
 */
final class BulkAuditBatchCompletedNotification extends Notification
{
    public function __construct(
        private readonly BulkAuditBatch $batch,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $label = $this->batch->name ?: "Bulk audit ({$this->batch->total_count} website(s))";

        return [
            'title' => 'Bulk audit finished',
            'message' => "{$label} is done — {$this->batch->completed_count} succeeded, "
                ."{$this->batch->failed_count} failed.",
            'url' => route('bulk-audits.show', $this->batch),
            'icon' => 'list-checks',
        ];
    }
}