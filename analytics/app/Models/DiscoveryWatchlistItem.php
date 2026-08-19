<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One discovered site flagged for follow-up (audit/export/lead-scoring
 * later) — see database/migrations/2026_08_14_000002_create_discovery_watchlist_table.php's
 * own docblock for this table's original design (a genuinely global
 * watchlist, before real per-account ownership existed at all).
 *
 * Phase N4 (User Dashboard) — see
 * database/migrations/2026_08_19_000005_add_user_id_to_discovery_watchlist_table.php's
 * own docblock for why user_id was added and why the table's own
 * uniqueness rule changed from "one row per website" to "one row per
 * (website, user) pair": two different people can now each
 * independently watch the same site, and this app's own dashboard
 * (App\Http\Controllers\DashboardController) can answer "how many
 * websites has THIS user personally watched" for the first time.
 *
 * $table is set explicitly because Eloquent's default convention would
 * guess `discovery_watchlist_items` (the pluralized snake_case of the
 * class name) for the table backing this model, but the actual table
 * — matching the migration's own name and this record's simple
 * "bookmark on a site" nature rather than a plural collection of its
 * own — is `discovery_watchlist`.
 *
 * No getRouteKeyName() override, unlike DiscoveredWebsite/DiscoverySearch/
 * Audit: this table has no uuid column, so it uses Eloquent's default
 * numeric-id route binding.
 */
final class DiscoveryWatchlistItem extends Model
{
    use HasFactory;

    protected $table = 'discovery_watchlist';

    protected $fillable = [
        // Phase N4 — see this class's own docblock. Nullable (any row
        // created before this column existed has no real owner), set
        // from auth()->id() at creation
        // (App\Http\Controllers\DiscoveryController::watch()).
        'user_id',
        'discovered_website_id',
        'notes',
    ];

    public function discoveredWebsite(): BelongsTo
    {
        return $this->belongsTo(DiscoveredWebsite::class);
    }

    /**
     * Phase N4 — null for any row that predates this column.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}