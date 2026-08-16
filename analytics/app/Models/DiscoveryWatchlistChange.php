<?php

declare(strict_types=1);

namespace App\Models;

use App\Discovery\Enums\WatchlistChangeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One detected change (Phase G2) — see database/migrations/
 * 2026_08_15_000005_create_discovery_watchlist_changes_table.php's own
 * docblock for why this references discovered_websites directly (not
 * discovery_watchlist) and why old_value/new_value are plain strings.
 *
 * $table is set explicitly for the same reason
 * DiscoveryWatchlistItem::$table is: Eloquent's default guess
 * (discovery_watchlist_changes, pluralized from the class name) happens
 * to already match the migration's own table name here, but it's set
 * explicitly anyway rather than left implicit, matching this model's
 * sibling DiscoveryWatchlistItem's own convention in this same
 * directory.
 */
final class DiscoveryWatchlistChange extends Model
{
    use HasFactory;

    protected $table = 'discovery_watchlist_changes';

    protected $fillable = [
        'discovered_website_id',
        'change_type',
        'old_value',
        'new_value',
        'detected_at',
    ];

    protected $casts = [
        'change_type' => WatchlistChangeType::class,
        'detected_at' => 'datetime',
    ];

    public function discoveredWebsite(): BelongsTo
    {
        return $this->belongsTo(DiscoveredWebsite::class);
    }
}
