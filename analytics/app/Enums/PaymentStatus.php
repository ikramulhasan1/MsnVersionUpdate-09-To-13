<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase N6 (Multiple Payment Methods) — App\Models\Payment's own
 * $status column. PENDING is set the moment a checkout SESSION/
 * transaction is created (before the person has actually paid
 * anything — see App\Http\Controllers\Payments\CheckoutController),
 * then moved to SUCCEEDED or FAILED once the gateway's own webhook/IPN
 * confirms the real outcome. A row never skips PENDING — even the
 * fastest real payment still passes through it, if only for a moment.
 */
enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUCCEEDED => 'Succeeded',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning-subtle text-warning-emphasis',
            self::SUCCEEDED => 'bg-success-subtle text-success-emphasis',
            self::FAILED => 'bg-danger-subtle text-danger-emphasis',
        };
    }
}