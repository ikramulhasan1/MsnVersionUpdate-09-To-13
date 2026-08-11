<?php

declare(strict_types=1);

namespace App\Audit\Export\Sheets;

use App\Audit\Export\DTO\ScoreRow;
use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

/**
 * "Charts" worksheet — a bar chart of each category's overall score
 * (columns A/B, built from the same ScoreRow data the "Scores"
 * worksheet uses), plus, when the recommendation engine has run, a
 * pie chart of the Critical/Warning/Notice issue breakdown (columns
 * D/E, sourced from Executive Summary's counts) placed beneath it.
 *
 * Writes the small backing data tables itself (via array()) purely so
 * the charts below have cells to reference — this sheet generates no
 * new figures of its own; both tables are values already computed
 * elsewhere (AnalysisResultsToRows::scores() and the executive_summary
 * category on AIRecommendationResult).
 */
final class ChartsSheetExport implements FromArray, WithTitle, WithCharts, ShouldAutoSize, WithEvents
{
    use FormatsWorksheet;

    private const array SEVERITY_LABELS = ['Critical', 'Warning', 'Notice'];

    private const array SEVERITY_KEYS = ['critical', 'warning', 'notice'];

    /**
     * @param Collection<int, ScoreRow> $scoreRows
     * @param ?array{critical: int, warning: int, notice: int} $severityCounts
     */
    public function __construct(
        private readonly Collection $scoreRows,
        private readonly ?array $severityCounts = null,
    ) {
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $rows = [['Category', 'Score', null, 'Severity', 'Count']];

        $rowCount = max($this->scoreRows->count(), $this->severityCounts !== null ? count(self::SEVERITY_LABELS) : 0);

        for ($index = 0; $index < $rowCount; $index++) {
            /** @var ?ScoreRow $scoreRow */
            $scoreRow = $this->scoreRows->get($index);

            $rows[] = [
                $scoreRow?->category,
                $scoreRow?->score,
                null,
                $this->severityCounts !== null ? (self::SEVERITY_LABELS[$index] ?? null) : null,
                $this->severityCounts !== null && isset(self::SEVERITY_KEYS[$index])
                    ? $this->severityCounts[self::SEVERITY_KEYS[$index]] ?? 0
                    : null,
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Charts';
    }

    /**
     * @return array<int, Chart>
     */
    public function charts(): array
    {
        $charts = [$this->scoreChart()];

        if ($this->severityCounts !== null) {
            $charts[] = $this->severityChart();
        }

        return $charts;
    }

    private function scoreChart(): Chart
    {
        $lastRow = $this->scoreRows->count() + 1;

        $categories = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Charts'!\$A\$2:\$A\${$lastRow}",
            null,
            $this->scoreRows->count(),
        )];
        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Charts'!\$B\$2:\$B\${$lastRow}",
            null,
            $this->scoreRows->count(),
        )];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            [],
            $categories,
            $values,
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $chart = new Chart(
            'category_scores',
            new Title('Category Scores'),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$series]),
        );
        $chart->setTopLeftPosition('G2');
        $chart->setBottomRightPosition('P20');

        return $chart;
    }

    private function severityChart(): Chart
    {
        $lastRow = count(self::SEVERITY_LABELS) + 1;

        $categories = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Charts'!\$D\$2:\$D\${$lastRow}",
            null,
            count(self::SEVERITY_LABELS),
        )];
        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Charts'!\$E\$2:\$E\${$lastRow}",
            null,
            count(self::SEVERITY_LABELS),
        )];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values) - 1),
            [],
            $categories,
            $values,
        );

        $chart = new Chart(
            'issue_severity',
            new Title('Issue Severity Breakdown'),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$series]),
        );
        $chart->setTopLeftPosition('G22');
        $chart->setBottomRightPosition('P40');

        return $chart;
    }
}
