<?php

declare(strict_types=1);

namespace App\Audit\Enums;

enum SeoSeverity: string
{
    case CRITICAL = 'critical';
    case WARNING = 'warning';
    case NOTICE = 'notice';

    public function label(): string
    {
        return match ($this) {
            self::CRITICAL => 'Critical',
            self::WARNING => 'Warning',
            self::NOTICE => 'Notice',
        };
    }

    /**
     * Points deducted from a page's SEO score for each issue of this
     * severity. Deliberately steep for critical issues (things that can
     * keep a page out of the index entirely) versus notices (things worth
     * fixing but with no direct ranking/indexing consequence).
     */
    public function scoreWeight(): int
    {
        return match ($this) {
            self::CRITICAL => 15,
            self::WARNING => 6,
            self::NOTICE => 2,
        };
    }
}
