<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase O5 (Keyword List/Project Management) — see this table's own
 * migration (database/migrations/2026_08_22_000002_create_keyword_list_items_table.php)
 * for the full reasoning, especially the "snapshot, never auto-
 * refreshed" caveat on volume/difficulty/cpc.
 */
final class KeywordListItem extends Model
{
    protected $fillable = [
        'keyword_list_id',
        'keyword',
        'volume',
        'difficulty',
        'cpc',
    ];

    protected function casts(): array
    {
        return [
            'volume' => 'integer',
            'difficulty' => 'integer',
            'cpc' => 'decimal:2',
        ];
    }

    public function keywordList(): BelongsTo
    {
        return $this->belongsTo(KeywordList::class);
    }
}