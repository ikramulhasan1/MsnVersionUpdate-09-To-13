<?php

declare(strict_types=1);

namespace App\Discovery\Export;

use App\Audit\Export\Support\Concerns\FormatsWorksheet;
use App\Discovery\Export\DTO\DiscoveryExportRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Website Discovery's export worksheet (Phase H2) — one row per
 * discovered site, columns: Business Name, Website, Industry, Country,
 * City, Technology, CMS, every score, Opportunity Score, Email, Phone,
 * Social Links. Follows App\Audit\Export\Sheets\AnalysisSheetExport's
 * own pattern exactly (FromCollection/ShouldAutoSize/WithEvents/
 * WithHeadings/WithTitle, FormatsWorksheet mixed in for the same bold-
 * header/borders/frozen-pane/auto-filter formatting every Audit export
 * worksheet already has) — reusing that same shared trait rather than
 * a Discovery-specific copy, since the formatting logic itself has
 * nothing audit-specific about it.
 *
 * Maatwebsite/Excel's writer is chosen from the download() file
 * extension, not from anything in this class — the exact same
 * DiscoveryResultsExport instance produces a .xlsx or a .csv depending
 * only on which extension App\Http\Controllers\DiscoveryController::export()
 * passes to Excel::download(), so Excel and CSV share this one class
 * rather than needing two.
 */
final class DiscoveryResultsExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    use FormatsWorksheet;

    /**
     * @param  Collection<int, DiscoveryExportRow>  $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {}

    /**
     * @return Collection<int, array<int, mixed>>
     */
    public function collection(): Collection
    {
        return $this->rows->map(static fn (DiscoveryExportRow $row): array => [
            $row->businessName,
            $row->website,
            $row->industry,
            $row->country,
            $row->city,
            $row->technology,
            $row->cms,
            $row->seoScore,
            $row->performanceScore,
            $row->securityScore,
            $row->accessibilityScore,
            $row->mobileScore,
            $row->opportunityScore,
            $row->email,
            $row->phone,
            $row->socialLinks,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Business Name',
            'Website',
            'Industry',
            'Country',
            'City',
            'Technology',
            'CMS',
            'SEO Score',
            'Performance Score',
            'Security Score',
            'Accessibility Score',
            'Mobile Score',
            'Opportunity Score',
            'Email',
            'Phone',
            'Social Links',
        ];
    }

    public function title(): string
    {
        return 'Discovered Websites';
    }
}
