<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Export\Pdf;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Models\Audit;
use Tests\TestCase;

/**
 * Confirms AuditPdfExportService::download() builds an actual PDF —
 * not just a response that happens to have a 200-shaped return type —
 * for a minimal Audit + AnalysisResults pair, without throwing.
 *
 * Extends the full Laravel TestCase (not plain PHPUnit\TestCase, unlike
 * most tests under tests/Unit) because building the PDF genuinely needs
 * the framework: AuditPdfExportService is resolved from the container
 * per its binding in AuditServiceProvider (pulling in the dompdf
 * wrapper, config('audit.pdf.*'), and config('app.name')), and
 * ->loadView() renders a real Blade view. Nothing here touches the
 * database, so RefreshDatabase is intentionally not used — Audit::factory()
 * ->make() builds the model in memory only, which is all
 * AuditToPdfHeaderData needs (it only reads ->url, ->uuid, ->created_at).
 *
 * $results is a bare AnalysisResults with every analyzer field left
 * null, and $recommendationResult is omitted (defaults to null) — the
 * same minimal-fixture approach AuditReportExportTest uses for the
 * Excel export, since every mapper AuditPdfExportService relies on
 * (AnalysisResultsToRows, SummaryResultsToRows, RecommendationResultToRows)
 * already tolerates nulls/empties safely. The point of this test is
 * that PDF generation itself succeeds end-to-end, not that every
 * section renders realistic content.
 */
final class AuditPdfExportServiceTest extends TestCase
{
    public function test_download_produces_non_empty_pdf_output_without_throwing(): void
    {
        $audit = Audit::factory()->make([
            'url' => 'https://example.com',
        ]);

        $results = new AnalysisResults(url: 'https://example.com');

        $service = $this->app->make(AuditPdfExportServiceInterface::class);

        $response = $service->download($audit, $results);

        $content = (string) $response->getContent();

        $this->assertNotSame('', $content);
        $this->assertStringStartsWith('%PDF-', $content);
        $this->assertStringStartsWith(
            'application/pdf',
            (string) $response->headers->get('Content-Type'),
        );
    }
}
