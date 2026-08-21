<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TechnicalSeoScanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Phase R2 (Technical SEO Audit) — see this table's own migration
 * (database/migrations/2026_08_24_000001_create_technical_seo_scans_table.php).
 */
final class TechnicalSeoScan extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'domain',
        'status',
        'health_score',
        'health_grade',
        'result',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'status' => TechnicalSeoScanStatus::class,
            'health_score' => 'integer',
            'result' => 'array',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (self $scan): void {
            $scan->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * Same reasoning as App\Models\Audit's own getRouteKeyName() —
     * scan URLs should never leak internal database ids.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}