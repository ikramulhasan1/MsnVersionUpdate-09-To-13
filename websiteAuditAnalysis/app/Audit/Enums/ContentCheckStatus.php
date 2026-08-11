<?php

declare(strict_types=1);

namespace App\Audit\Enums;

enum ContentCheckStatus: string
{
    case GOOD = 'good';
    case WARNING = 'warning';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Good',
            self::WARNING => 'Warning',
            self::CRITICAL => 'Critical',
        };
    }
}
