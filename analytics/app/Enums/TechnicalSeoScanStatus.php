<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase R2 (Technical SEO Audit) — see technical_seo_scans' own
 * migration docblock for why this is a separate enum from
 * App\Audit\Enums\AuditStatus rather than reusing it directly.
 */
enum TechnicalSeoScanStatus: string
{
    case QUEUED = 'queued';
    case CRAWLING = 'crawling';
    case ANALYZING = 'analyzing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::CRAWLING => 'Crawling Site',
            self::ANALYZING => 'Analyzing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::QUEUED => 'bg-secondary',
            self::CRAWLING, self::ANALYZING => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED], true);
    }
}