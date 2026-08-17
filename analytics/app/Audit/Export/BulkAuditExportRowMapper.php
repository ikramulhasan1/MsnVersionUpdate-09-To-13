<?php

declare(strict_types=1);

namespace App\Audit\Export;

use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Enums\AuditStatus;
use App\Audit\Export\DTO\BulkAuditExportRow;
use App\Models\Audit;
use App\Models\BulkAuditBatch;
use Illuminate\Support\Collection;

/**
 * Phase K5 (Bulk Audit) — maps one BulkAuditBatch's own audits into
 * BulkAuditExportRow, mirroring
 * App\Discovery\Export\DiscoveredWebsitesToExportRows's own "one
 * mapper, every export format reads from it" shape for a completely
 * different module. Used by both BulkAuditController::show() (the
 * results table) and ::export() (Excel/CSV/JSON) — the SAME row shape
 * backs the on-screen table and every downloadable format, so they can
 * never drift out of sync with each other.
 *
 * Only a COMPLETED audit's own scores are ever real — see map()'s own
 * comment for why an audit that's still processing, or that FAILED,
 * gets every score column left null rather than a fabricated 0.
 */
final class BulkAuditExportRowMapper
{
    public function __construct(
        private readonly AuditCacheServiceInterface $cache,
    ) {
    }

    /**
     * @return Collection<int, BulkAuditExportRow>
     */
    public function map(BulkAuditBatch $batch): Collection
    {
        return $batch->audits
            ->map(fn (Audit $audit): BulkAuditExportRow => $this->toRow($audit))
            ->values();
    }

    private function toRow(Audit $audit): BulkAuditExportRow
    {
        // A still-processing or FAILED audit has no
        // AnalysisResults cached under its own uuid at all (see
        // App\Audit\Jobs\AssembleAnalysisResultsJob — that cache write
        // only happens once assembly runs, regardless of whether the
        // outcome was COMPLETED or FAILED) — every score column below
        // stays null for it rather than guessing at a value that was
        // never actually computed.
        $results = $audit->status === AuditStatus::COMPLETED
            ? $this->cache->getAnalysisResults($audit->uuid)
            : null;

        return new BulkAuditExportRow(
            url: $audit->url,
            status: $audit->status->label(),
            seoScore: $results?->seo?->averageScore,
            performanceScore: $results?->performance?->score,
            performanceGrade: $results?->performance?->grade,
            securityScore: $results?->security?->score,
            securityGrade: $results?->security?->grade,
            accessibilityScore: $results?->accessibility?->score,
            accessibilityGrade: $results?->accessibility?->grade,
        );
    }
}