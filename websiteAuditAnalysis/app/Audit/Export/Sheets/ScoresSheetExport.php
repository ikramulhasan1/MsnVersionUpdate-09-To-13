<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\ScoreRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * "Scores" worksheet — one row per analyzed category with its overall
 * score, grade, and when it was analyzed.
 *
 * Mixes in FormatsWorksheet (Prompt 16.3) for professional formatting;
 * collection()/headings()/title() below are unchanged from Prompt 16.1.
 */
final class ScoresSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    /**
     * @param Collection<int, ScoreRow> $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {
    }

    /**
     * @return Collection<int, array{0: string, 1: ?int, 2: ?string, 3: string}>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (ScoreRow $row): array => [
            $row->category,
            $row->score,
            $row->grade,
            $row->analyzedAt,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Category', 'Score', 'Grade', 'Analyzed At'];
    }

    public function title(): string
    {
        return 'Scores';
    }
}
