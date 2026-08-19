<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Audit;
use Illuminate\Notifications\Notification;

/**
 * Phase N2 (Dynamic Notification System) — dispatched from
 * App\Audit\Jobs\AssembleAnalysisResultsJob::handle() once a
 * STANDALONE audit (bulk_audit_batch_id is null — a bulk audit's own
 * individual completions are deliberately silent; see
 * BulkAuditBatchCompletedNotification's own docblock for why the
 * BATCH's single completion notification covers that case instead)
 * reaches a final status, success or failure alike — a failed audit is
 * exactly the kind of thing someone would want to know about without
 * having to keep the result page open and polling it themselves.
 *
 * 'database' channel only for now — no email/broadcast channel yet;
 * this class's own via() would be the only place to add one later
 * (e.g. ['database', 'mail']), nothing else in this app would need to
 * change.
 */
final class AuditCompletedNotification extends Notification
{
    public function __construct(
        private readonly Audit $audit,
        private readonly bool $succeeded,
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
        return [
            'title' => $this->succeeded ? 'Audit completed' : 'Audit failed',
            'message' => $this->succeeded
                ? "Your audit of {$this->audit->url} is ready to view."
                : "Your audit of {$this->audit->url} couldn't be completed.",
            'url' => route('audits.show', $this->audit),
            'icon' => $this->succeeded ? 'check-circle' : 'alert-circle',
        ];
    }
}