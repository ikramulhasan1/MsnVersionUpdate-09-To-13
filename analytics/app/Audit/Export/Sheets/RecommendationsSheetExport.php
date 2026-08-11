<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\RecommendationRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Recommendations" worksheet — every prioritized issue/recommendation
 * from the AI Recommendation Engine's `issue_priority` category, most
 * severe first.
 *
 * Mixes in FormatsWorksheet (Prompt 16.3) for professional formatting;
 * collection()/headings()/title() below are unchanged from Prompt 16.2.
 */
final class RecommendationsSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, RecommendationRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: int, 1: string, 2: string, 3: string, 4: string, 5: ?string, 6: ?string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (RecommendationRow $row): array => [
            $row->priority,
            $row->category,
            $row->issue,
            $row->severity,
            $row->status,
            $row->recommendation,
            $row->pageUrl,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Priority', 'Category', 'Issue', 'Severity', 'Status', 'Recommendation', 'Page URL'];
    }

    public function title(): string
    {
        return 'Recommendations';
    }
}
