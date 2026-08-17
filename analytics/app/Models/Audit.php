<?php

declare(strict_types=1);

namespace App\Models;

use App\Audit\Enums\AuditMode;
use App\Audit\Enums\AuditStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'url',
        'url_hash',
        'status',
        'mode',
        // Phase K2 — see App\Models\BulkAuditBatch's own docblock. Null
        // for every audit submitted the ordinary way (single-URL form)
        // — this column is the one place that distinguishes a
        // standalone audit from one submitted as part of a bulk batch.
        'bulk_audit_batch_id',
    ];

    /**
     * url_hash is an internal lookup optimization (see AuditRepository),
     * not part of any public contract — hidden defensively in case this
     * model is ever serialized directly.
     */
    protected $hidden = [
        'url_hash',
    ];

    protected $casts = [
        'status' => AuditStatus::class,
        // Phase K1 — see App\Audit\Enums\AuditMode's own docblock. Cast
        // the same way $status already is: an application-level enum
        // over a plain string column, not a schema-level ENUM type.
        'mode' => AuditMode::class,
    ];

    /**
     * Use the UUID for route model binding instead of the numeric id,
     * so audit URLs never leak internal database ids.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Null for a standalone audit (see $fillable's own comment on
     * bulk_audit_batch_id) — Phase K2/K5's own batch results page is
     * the only place this relation is actually loaded.
     */
    public function bulkAuditBatch(): BelongsTo
    {
        return $this->belongsTo(BulkAuditBatch::class);
    }

    /**
     * Always (re)computes url_hash from the final url attribute at
     * creation time, mirroring AuditRepository::hashUrl(). This covers
     * every creation path — factory, repository, or a direct
     * Audit::create()/save() — rather than relying on each caller to
     * set it correctly, which is what let AuditFactory's definition()
     * go stale when a test overrode 'url' without recomputing the hash.
     */
    protected static function booted(): void
    {
        self::creating(function (self $audit): void {
            $audit->url_hash = md5($audit->url);
        });
    }
}