<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — App\Models\PlanUpgradeRequest's
 * own $status column. FULFILLED is set manually by
 * App\Http\Controllers\Admin\UserManagementController::update() when
 * an Admin actually assigns the requested plan to that user — nothing
 * automatically transitions PENDING to FULFILLED on its own, since
 * there's no payment event (Phase N6) to trigger that yet.
 */
enum PlanUpgradeRequestStatus: string
{
    case PENDING = 'pending';
    case FULFILLED = 'fulfilled';
    case DECLINED = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::FULFILLED => 'Fulfilled',
            self::DECLINED => 'Declined',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning-subtle text-warning-emphasis',
            self::FULFILLED => 'bg-success-subtle text-success-emphasis',
            self::DECLINED => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}