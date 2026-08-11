<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\TechnologyRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Technology Stack" worksheet — one row per detected technology
 * (CMS/e-commerce platform, JS framework, etc.), with its category,
 * version (where known), and detection confidence.
 *
 * Mixes in FormatsWorksheet (Prompt 16.3) for professional formatting;
 * collection()/headings()/title() below are unchanged from Prompt 16.2.
 */
final class TechnologySheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, TechnologyRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: string, 2: ?string, 3: ?int}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (TechnologyRow $row): array => [
            $row->technology,
            $row->category,
            $row->version,
            $row->confidenceScore,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Technology', 'Category', 'Version', 'Confidence Score'];
    }

    public function title(): string
    {
        return 'Technology Stack';
    }
}
