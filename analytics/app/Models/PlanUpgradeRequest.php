<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlanUpgradeRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — see this table's own
 * migration (database/migrations/2026_08_19_000008_create_plan_upgrade_requests_table.php)
 * for the full reasoning.
 */
final class PlanUpgradeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'status' => PlanUpgradeRequestStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}