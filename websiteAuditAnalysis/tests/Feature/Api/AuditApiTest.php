<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Audit\Enums\AuditStatus;
use App\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_audit_as_json_with_the_documented_top_level_shape(): void
    {
        $audit = Audit::factory()->create();

        $response = $this->getJson("/api/v1/audits/{$audit->uuid}");

        $response->assertOk();
        $response->assertJsonPath('data.uuid', $audit->uuid);
        $response->assertJsonPath('data.url', $audit->url);
        $response->assertJsonStructure([
            'data' => [
                'uuid',
                'url',
                'status',
                'seo_analysis',
                'performance_analysis',
                'security_analysis',
                'accessibility_analysis',
                'ui_ux_analysis',
                'content_analysis',
                'technology_stack',
                'business_analysis',
                'scores',
                'recommendations',
            ],
            'meta' => [
                'analysis_complete',
                'audit_status',
                'generated_at',
            ],
        ]);
    }

    public function test_an_audit_still_in_progress_returns_200_with_analysis_complete_false(): void
    {
        $audit = Audit::factory()->create(['status' => AuditStatus::CRAWLING->value]);

        $response = $this->getJson("/api/v1/audits/{$audit->uuid}");

        $response->assertOk();
        $response->assertJsonPath('meta.analysis_complete', false);
        $response->assertJsonPath('meta.audit_status', 'crawling');
    }

    public function test_a_completed_audit_reports_analysis_complete_true(): void
    {
        $audit = Audit::factory()->completed()->create();

        $response = $this->getJson("/api/v1/audits/{$audit->uuid}");

        $response->assertJsonPath('meta.analysis_complete', true);
    }

    public function test_an_unknown_audit_returns_a_clean_json_404(): void
    {
        $response = $this->getJson('/api/v1/audits/does-not-exist');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Audit not found.']);
    }
}
