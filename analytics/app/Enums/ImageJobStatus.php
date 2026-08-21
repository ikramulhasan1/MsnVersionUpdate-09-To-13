<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Image Everything (Phase S1) — App\Models\ImageProcessingJob's own
 * $status column.
 */
enum ImageJobStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-secondary',
            self::PROCESSING => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED], true);
    }
}