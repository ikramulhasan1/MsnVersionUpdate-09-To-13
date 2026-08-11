<?php

declare(strict_types=1);

namespace App\Audit\Enums;

enum AuditStatus: string
{
    case QUEUED = 'queued';
    case FETCHING = 'fetching';
    case CRAWLING = 'crawling';
    case ANALYZING = 'analyzing';
    case GENERATING_REPORT = 'generating_report';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::FETCHING => 'Fetching Website',
            self::CRAWLING => 'Crawling Pages',
            self::ANALYZING => 'Analyzing',
            self::GENERATING_REPORT => 'Generating Report',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::QUEUED => 'bg-secondary',
            self::FETCHING, self::CRAWLING, self::ANALYZING, self::GENERATING_REPORT => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED], true);
    }
}
