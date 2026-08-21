<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * PRODUCTION GAP CLOSED — see this table's own migration
 * (database/migrations/2026_08_25_000001_create_on_page_seo_checks_table.php)
 * for the full reasoning.
 */
final class OnPageSeoCheck extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'url',
        'target_keyword',
        'score',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'result' => 'array',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (self $check): void {
            $check->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * Same reasoning as App\Models\Audit/App\Models\TechnicalSeoScan's
     * own getRouteKeyName() — check URLs should never leak internal
     * database ids.
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