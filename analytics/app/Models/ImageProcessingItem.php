<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImageItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Image Everything (Phase S1) — see this table's own migration
 * (database/migrations/2026_08_26_000002_create_image_processing_items_table.php).
 * $temp_path/$processed_path are relative paths on the 'private-images'
 * disk (config/filesystems.php), never a public URL and never raw
 * bytes — see App\ImageProcessing\ImageJobService's own docblock for
 * how they're generated (UUID-based, never the original filename).
 */
final class ImageProcessingItem extends Model
{
    protected $fillable = [
        'job_id',
        'original_filename',
        'temp_path',
        'processed_path',
        'mime_type',
        'file_size_bytes',
        'width',
        'height',
        'format',
        'status',
        'error_message',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'status' => ImageItemStatus::class,
            'file_size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'result' => 'array',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(ImageProcessingJob::class, 'job_id');
    }
}