<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\AnalysisRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Analysis" worksheet — one row per individual check/metric, across
 * every analyzed category.
 *
 * Mixes in FormatsWorksheet (Prompt 16.3) for professional formatting;
 * collection()/headings()/title() below are unchanged from Prompt 16.1.
 */
final class AnalysisSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, AnalysisRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: string, 2: ?string, 3: string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (AnalysisRow $row): array => [
            $row->category,
            $row->check,
            $row->value,
            $row->status,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Category', 'Check', 'Value', 'Status'];
    }

    public function title(): string
    {
        return 'Analysis';
    }
}
