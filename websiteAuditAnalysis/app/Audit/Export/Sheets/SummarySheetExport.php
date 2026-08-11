<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\SummaryRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Summary" worksheet — a single at-a-glance Metric/Value overview
 * combining the Executive Summary, Business Recommendation, effort/cost
 * totals, and Business Opportunity Score, sourced entirely from data
 * every other worksheet already exports (see SummaryResultsToRows).
 * Placed first among the sheets so it's the first thing a reader sees.
 */
final class SummarySheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, SummaryRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (SummaryRow $row): array => [
            $row->label,
            $row->value,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Summary';
    }
}
