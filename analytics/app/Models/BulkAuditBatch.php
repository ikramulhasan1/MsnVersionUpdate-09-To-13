<?php

declare(strict_types=1);

namespace App\Models;

use App\Audit\Enums\AuditMode;
use App\Audit\Enums\BulkAuditBatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Phase K2 (Bulk Audit Batch) — one "audit several websites together"
 * submission (Phase K3 builds the actual submission entry points: the
 * existing Discovery "Bulk Audit" checkbox flow, a pasted-URL-list
 * form, and CSV upload — all three end up creating one of these, plus
 * one App\Models\Audit per URL). See
 * database/migrations/2026_08_19_000000_create_bulk_audit_batches_table.php's
 * own docblock for why total_count/completed_count/failed_count are
 * plain denormalized counters rather than computed from audits() on
 * every read.
 *
 * Follows this app's own uuid route-key-binding convention
 * (App\Models\Audit, App\Models\DiscoverySearch, ...) — uuid is set
 * explicitly by whichever service creates a batch (Phase K3's
 * BulkAuditBatchService), the same way Audit's own uuid is set by
 * AuditService::submit() rather than a booted() hook on this model;
 * this model deliberately has no booted() hook of its own for the
 * same reason DiscoverySearch's own docblock already explains for
 * itself.
 */
final class BulkAuditBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'total_count',
        'completed_count',
        'failed_count',
        'status',
        'mode',
    ];

    protected $casts = [
        'total_count' => 'integer',
        'completed_count' => 'integer',
        'failed_count' => 'integer',
        'status' => BulkAuditBatchStatus::class,
        'mode' => AuditMode::class,
    ];

    /**
     * Use the UUID for route model binding instead of the numeric id,
     * mirroring Audit::getRouteKeyName() — so a bulk batch's own
     * results page URL (Phase K5) never leaks an internal database id.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    /**
     * (completed + failed) / total, as a whole percentage — the exact
     * same "finished, one way or another, out of the total" framing
     * App\Audit\Enums\AuditStatus::isFinished() already uses for a
     * single audit, rolled up across every audit in this batch.
     * Returns 0 rather than dividing by zero for a batch whose
     * total_count somehow hasn't been set yet (defensive — every real
     * creation path sets total_count up front, at batch-creation time,
     * before any child Audit exists to complete or fail).
     */
    public function progressPercent(): int
    {
        if ($this->total_count === 0) {
            return 0;
        }

        return (int) round((($this->completed_count + $this->failed_count) / $this->total_count) * 100);
    }
}