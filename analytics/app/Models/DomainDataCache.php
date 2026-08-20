<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Phase Q1 (Domain Data Service Layer) — see this table's own
 * migration (database/migrations/2026_08_23_000001_create_domain_data_cache_table.php).
 * Read/written exclusively by App\DomainData\DomainDataCacheRepository.
 */
final class DomainDataCache extends Model
{
    protected $table = 'domain_data_cache';

    protected $fillable = [
        'domain',
        'country',
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