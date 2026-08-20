<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Phase O2 (Keyword Data Service Layer) — see this table's own
 * migration (database/migrations/2026_08_21_000002_create_keyword_data_cache_table.php)
 * for the full reasoning. Read/written exclusively by
 * App\KeywordData\KeywordDataCacheRepository — no controller or
 * adapter touches this model directly.
 */
final class KeywordDataCache extends Model
{
    protected $table = 'keyword_data_cache';

    protected $fillable = [
        'keyword',
        'country',
        'language',
        'capability',
        'response',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'response' => 'array',
            'expires_at' => 'datetime',
        ];
    }
}