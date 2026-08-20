<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase O2 (Keyword Data Service Layer) — see this table's own
 * migration (database/migrations/2026_08_21_000003_create_api_usage_logs_table.php)
 * for the full reasoning, especially the "estimated, not a real
 * invoice" caveat and why this is a DECIMAL column, not integer cents.
 */
final class ApiUsageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_provider_id',
        'capability',
        'keyword_count',
        'estimated_cost_usd',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'keyword_count' => 'integer',
            'estimated_cost_usd' => 'decimal:6',
            'created_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }
}