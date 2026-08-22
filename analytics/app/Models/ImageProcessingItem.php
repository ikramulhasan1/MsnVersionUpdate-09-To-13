<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImageItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'metadata',
        'quality_analysis',
        'quality_score',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ImageItemStatus::class,
            'file_size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'result' => 'array',
            'metadata' => 'array',
            'quality_analysis' => 'array',
            'quality_score' => 'integer',
            'analyzed_at' => 'datetime',
        ];
    }

    /**
     * Phase S2 — convenience for callers (controllers/views) that just
     * need "should I show a privacy warning" without reaching into the
     * $metadata JSON shape themselves. See
     * App\ImageProcessing\ImageMetadataExtractor's own docblock for why
     * GPS EXIF fields are extracted (not silently dropped) alongside
     * this flag rather than instead of it.
     */
    public function hasGpsData(): bool
    {
        return (bool) data_get($this->metadata, 'exif.gps.present', false);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(ImageProcessingJob::class, 'job_id');
    }

    /**
     * Phase S4 — every resize/crop/compress/convert/responsive
     * operation ever run against this item, oldest first. See
     * App\Models\ImageProcessingOperation's own docblock for why this
     * is a separate table rather than another column here.
     */
    public function operations(): HasMany
    {
        return $this->hasMany(ImageProcessingOperation::class, 'item_id')->oldest();
    }
}