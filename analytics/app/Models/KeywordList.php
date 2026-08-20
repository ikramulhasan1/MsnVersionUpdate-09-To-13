<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Phase O5 (Keyword List/Project Management) — see this table's own
 * migration (database/migrations/2026_08_22_000001_create_keyword_lists_table.php)
 * for the full reasoning.
 */
final class KeywordList extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KeywordListItem::class);
    }
}