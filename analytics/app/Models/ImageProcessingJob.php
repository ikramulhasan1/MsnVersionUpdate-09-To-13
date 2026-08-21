<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImageJobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Image Everything (Phase S1) — see this table's own migration
 * (database/migrations/2026_08_26_000001_create_image_processing_jobs_table.php)
 * for the full "never store image content permanently" principle.
 */
final class ImageProcessingJob extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'total_images',
        'processed_images',
        'expires_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ImageJobStatus::class,
            'total_images' => 'integer',
            'processed_images' => 'integer',
            'expires_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (self $job): void {
            $job->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * Same reasoning as App\Models\Audit/App\Models\TechnicalSeoScan's
     * own getRouteKeyName() — job URLs should never leak internal
     * database ids.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ImageProcessingItem::class, 'job_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}