<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One discovered site flagged for follow-up (audit/export/lead-scoring
 * later) — see database/migrations/2026_08_14_000002_create_discovery_watchlist_table.php's
 * own docblock for why this table (and therefore this model) has no
 * uuid or user_id of its own.
 *
 * $table is set explicitly because Eloquent's default convention would
 * guess `discovery_watchlist_items` (the pluralized snake_case of the
 * class name) for the table backing this model, but the actual table
 * — matching the migration's own name and this record's simple
 * "bookmark on a site" nature rather than a plural collection of its
 * own — is `discovery_watchlist`.
 *
 * No getRouteKeyName() override, unlike DiscoveredWebsite/DiscoverySearch/
 * Audit: this table has no uuid column (see the migration's docblock
 * for why), so it uses Eloquent's default numeric-id route binding.
 */
final class DiscoveryWatchlistItem extends Model
{
    use HasFactory;

    protected $table = 'discovery_watchlist';

    protected $fillable = [
        'discovered_website_id',
        'notes',
    ];

    public function discoveredWebsite(): BelongsTo
    {
        return $this->belongsTo(DiscoveredWebsite::class);
    }
}