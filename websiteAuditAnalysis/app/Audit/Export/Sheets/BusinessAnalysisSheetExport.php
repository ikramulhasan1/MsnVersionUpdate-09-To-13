<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\BusinessAnalysisRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Business Analysis" worksheet — one row per Website Health issue
 * (Website Problems, SEO Issues, Performance Issues, Website
 * Modernization, Marketing Analysis, Content & Conversion Analysis).
 *
 * Mixes in FormatsWorksheet (Prompt 16.3) for professional formatting;
 * collection()/headings()/title() below are unchanged from Prompt 16.2.
 */
final class BusinessAnalysisSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, BusinessAnalysisRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: string, 2: string, 3: string, 4: ?string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (BusinessAnalysisRow $row): array => [
            $row->category,
            $row->issue,
            $row->status,
            $row->severity,
            $row->recommendation,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Category', 'Issue', 'Status', 'Severity', 'Recommendation'];
    }

    public function title(): string
    {
        return 'Business Analysis';
    }
}
