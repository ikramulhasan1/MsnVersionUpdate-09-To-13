<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_pdf_export_route_returns_a_downloadable_pdf_for_a_completed_audit(): void
    {
        $audit = Audit::factory()->completed()->create();

        $response = $this->get(route('audits.export', $audit->uuid));

        $response->assertOk();
        $this->assertStringStartsWith(
            'application/pdf',
            (string) $response->headers->get('Content-Type'),
        );
    }

    public function test_the_excel_export_route_returns_a_downloadable_spreadsheet_for_a_completed_audit(): void
    {
        $audit = Audit::factory()->completed()->create();

        $response = $this->get(route('audits.export.excel', $audit->uuid));

        $response->assertOk();
        $this->assertStringStartsWith(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('Content-Type'),
        );
    }
}
