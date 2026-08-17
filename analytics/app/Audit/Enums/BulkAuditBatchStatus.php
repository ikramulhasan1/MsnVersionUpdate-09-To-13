<?php

declare(strict_types=1);

namespace App\Audit\Enums;

/**
 * Phase K2 (Bulk Audit Batch) — where one BulkAuditBatch (many URLs
 * submitted together) currently stands. Deliberately smaller than
 * AuditStatus's own seven-case pipeline-stage vocabulary: a batch's
 * own status is a coarse rollup of its child Audits' individual
 * statuses, not a mirror of any single audit's own fine-grained
 * progress (each child Audit already tracks that itself — see
 * App\Models\Audit::$status).
 */
enum BulkAuditBatchStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-secondary',
            self::PROCESSING => 'bg-primary',
            self::COMPLETED => 'bg-success',
        };
    }
}