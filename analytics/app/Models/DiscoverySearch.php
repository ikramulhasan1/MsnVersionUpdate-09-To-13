<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A saved Website Discovery search — see database/migrations/
 * 2026_08_14_000001_create_discovery_searches_table.php's own
 * docblock for the full rationale behind `filters` being JSON and
 * `user_id` being nullable.
 *
 * Follows Audit's uuid route-key-binding convention. No url_hash-style
 * derived column exists here, so — unlike DiscoveredWebsite/Audit —
 * this model needs no booted() hook of its own.
 */
final class DiscoverySearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'filters',
        'is_scheduled',
        'last_run_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_scheduled' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    /**
     * Use the UUID for route model binding instead of the numeric id,
     * mirroring Audit::getRouteKeyName() — so a saved search's URL
     * never leaks internal database ids.
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