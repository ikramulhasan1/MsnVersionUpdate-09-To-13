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
 * collection()/headings()/title() below are unchanged from Prompt 16.1,
 * aside from the two additional "Page URL" / "Element / Location"
 * columns appended at the end (see AnalysisRow::$pageUrl/$elementLocation).
 */
final class AnalysisSheetExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    use FormatsWorksheet;

    /**
     * @param  Collection<int, AnalysisRow>  $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {}

    /**
     * @return Collection<int, array{0: string, 1: string, 2: ?string, 3: string, 4: ?string, 5: ?string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (AnalysisRow $row): array => [
            $row->category,
            $row->check,
            $row->value,
            $row->status,
            $row->pageUrl,
            $row->elementLocation,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Category', 'Check', 'Value', 'Status', 'Page URL', 'Element / Location'];
    }

    public function title(): string
    {
        return 'Analysis';
    }
}

