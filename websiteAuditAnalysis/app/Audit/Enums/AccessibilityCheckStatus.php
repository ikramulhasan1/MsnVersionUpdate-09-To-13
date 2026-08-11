<?php

declare(strict_types=1);

namespace App\Audit\Enums;

enum AccessibilityCheckStatus: string
{
    case PASS = 'pass';
    case WARNING = 'warning';
    case FAIL = 'fail';

    public function label(): string
    {
        return match ($this) {
            self::PASS => 'Pass',
            self::WARNING => 'Warning',
            self::FAIL => 'Fail',
        };
    }
}
