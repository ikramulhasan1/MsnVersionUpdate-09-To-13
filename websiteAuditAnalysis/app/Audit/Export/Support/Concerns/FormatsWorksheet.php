<?php

declare(strict_types=1);

namespace App\Audit\Export\Support\Concerns;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Shared professional formatting for every export worksheet: a bold,
 * filled header row, thin borders over the used data range, a frozen
 * header row, an auto-filter, wrapped text, and a capped column width
 * so long values (recommendations, narratives) stay readable instead
 * of overflowing indefinitely.
 *
 * Applied via WithEvents rather than WithStyles so it reads the
 * sheet's actual used range (getHighestColumn()/getHighestRow()) after
 * the data is written, instead of hardcoding a column count per
 * sheet — the exact same formatting works unmodified whether a sheet
 * has 4 columns or 7.
 *
 * Every Sheet export class in this module (Parts 1-3) mixes this trait
 * in for a consistent look, on top of — not instead of — its own
 * ShouldAutoSize column sizing. Deliberately touches formatting only:
 * no Sheet class's collection(), array(), headings(), or title()
 * implementation is affected by using this trait.
 */
trait FormatsWorksheet
{
    private const int MAX_COLUMN_WIDTH = 50;

    private const string HEADER_FILL_COLOR = '2F5496';

    private const string HEADER_FONT_COLOR = 'FFFFFF';

    private const string BORDER_COLOR = 'D9D9D9';

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $headerRange = "A1:{$highestColumn}1";

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => self::HEADER_FONT_COLOR]],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::HEADER_FILL_COLOR],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->freezePane('A2');
                $sheet->setAutoFilter($headerRange);

                if ($highestRow > 1) {
                    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => self::BORDER_COLOR],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("A2:{$highestColumn}{$highestRow}")
                        ->getAlignment()
                        ->setWrapText(true);
                }

                for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
                    $dimension = $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex));

                    if ($dimension->getWidth() > self::MAX_COLUMN_WIDTH) {
                        $dimension->setWidth(self::MAX_COLUMN_WIDTH);
                    }
                }
            },
        ];
    }
}
