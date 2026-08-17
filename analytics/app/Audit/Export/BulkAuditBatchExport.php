<?php

declare(strict_types=1);

namespace App\Audit\Export;

use App\Audit\Export\DTO\BulkAuditExportRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Phase K5 (Bulk Audit) — one row per audit in a BulkAuditBatch,
 * columns: Website, Status, SEO/Performance/Security/Accessibility
 * score+grade. Follows App\Discovery\Export\DiscoveryResultsExport's
 * own pattern exactly (FromCollection/ShouldAutoSize/WithEvents/
 * WithHeadings/WithTitle, FormatsWorksheet mixed in for the same
 * bold-header/borders/frozen-pane/auto-filter formatting every other
 * export in this app already has) — same shared trait Discovery's own
 * export already reuses, since the formatting logic has nothing
 * audit-specific (or bulk-specific) about it either.
 *
 * Maatwebsite/Excel's writer is chosen from the download() file
 * extension, not from anything in this class — the exact same
 * BulkAuditBatchExport instance produces a .xlsx or a .csv depending
 * only on which extension BulkAuditController::export() passes to
 * Excel::download(), so Excel and CSV share this one class rather than
 * needing two.
 */
final class BulkAuditBatchExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, BulkAuditExportRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array<int, mixed>>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (BulkAuditExportRow $row): array => [
            $row->url,
            $row->status,
            $row->seoScore,
            $row->performanceScore,
            $row->performanceGrade,
            $row->securityScore,
            $row->securityGrade,
            $row->accessibilityScore,
            $row->accessibilityGrade,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Website',
            'Status',
            'SEO Score',
            'Performance Score',
            'Performance Grade',
            'Security Score',
            'Security Grade',
            'Accessibility Score',
            'Accessibility Grade',
        ];
    }

    public function title(): string
    {
        return 'Bulk Audit Results';
    }
}