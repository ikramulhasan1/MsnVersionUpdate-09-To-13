<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImageItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Image Everything (Phase S4) — see this table's own migration
 * (database/migrations/2026_08_29_000001_create_image_processing_operations_table.php)
 * for why this is its own table rather than a column on
 * ImageProcessingItem, and for $result's shape per 'type'.
 */
final class ImageProcessingOperation extends Model
{
    protected $fillable = [
        'item_id',
        'type',
        'status',
        'params',
        'result',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ImageItemStatus::class,
            'params' => 'array',
            'result' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ImageProcessingItem::class, 'item_id');
    }
}