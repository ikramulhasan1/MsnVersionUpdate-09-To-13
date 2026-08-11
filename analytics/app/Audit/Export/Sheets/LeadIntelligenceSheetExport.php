<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\LeadIntelligenceRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Lead Intelligence" worksheet (Group U) — one row per fact from
 * prospect qualification, business signals, contacts found, and
 * technology upgrade opportunities, grouped by $section.
 *
 * Mixes in FormatsWorksheet for the same professional formatting every
 * other worksheet in this module already gets; collection()/headings()/
 * title() follow the exact shape every other Sheet export class here
 * uses (see TechnologySheetExport / BusinessAnalysisSheetExport).
 */
final class LeadIntelligenceSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, LeadIntelligenceRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: string, 2: ?string, 3: ?string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (LeadIntelligenceRow $row): array => [
            $row->section,
            $row->item,
            $row->value,
            $row->detail,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Section', 'Item', 'Value', 'Detail'];
    }

    public function title(): string
    {
        return 'Lead Intelligence';
    }
}