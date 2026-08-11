<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Audit\Enums\AuditStatus;
use App\Audit\Jobs\FetchAndCrawlJob;
use App\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class AuditSubmissionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_loads_successfully(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_submitting_a_valid_url_creates_a_queued_audit_and_dispatches_the_pipeline(): void
    {
        Queue::fake();

        $response = $this->post(route('audits.store'), ['url' => 'https://example.com']);

        $audit = Audit::query()->sole();

        $response->assertRedirect(route('audits.show', $audit->uuid));
        $response->assertSessionHas('status');

        $this->assertSame('https://example.com', $audit->url);
        $this->assertSame(AuditStatus::QUEUED, $audit->status);
        $this->assertNotEmpty($audit->uuid);

        Queue::assertPushed(FetchAndCrawlJob::class, function (FetchAndCrawlJob $job) use ($audit): bool {
            $reflection = new \ReflectionClass($job);

            $auditUuid = $reflection->getProperty('auditUuid');
            $auditUuid->setAccessible(true);

            return $auditUuid->getValue($job) === $audit->uuid;
        });
    }

    public function test_submitting_without_a_url_fails_validation_and_redirects_back(): void
    {
        $response = $this->post(route('audits.store'), ['url' => '']);

        $response->assertRedirect();
        $response->assertSessionHasErrors('url');
        $this->assertSame(0, Audit::query()->count());
    }

    public function test_submitting_a_url_without_a_scheme_fails_validation(): void
    {
        $response = $this->post(route('audits.store'), ['url' => 'example.com']);

        $response->assertSessionHasErrors('url');
    }

    public function test_submitting_a_duplicate_pending_url_reuses_the_existing_audit_instead_of_creating_a_new_one(): void
    {
        Queue::fake();

        $existing = Audit::factory()->create([
            'url' => 'https://example.com',
            'status' => AuditStatus::CRAWLING->value,
        ]);

        $response = $this->post(route('audits.store'), ['url' => 'https://example.com']);

        $response->assertRedirect(route('audits.show', $existing->uuid));
        $this->assertSame(1, Audit::query()->count());
    }

    public function test_a_completed_audits_url_does_not_block_a_new_submission_for_the_same_url(): void
    {
        Queue::fake();

        Audit::factory()->completed()->create(['url' => 'https://example.com']);

        $this->post(route('audits.store'), ['url' => 'https://example.com']);

        $this->assertSame(2, Audit::query()->count());
    }

    public function test_the_show_page_displays_an_existing_audit(): void
    {
        $audit = Audit::factory()->create();

        $this->get(route('audits.show', $audit->uuid))
            ->assertOk()
            ->assertSee($audit->url, false);
    }

    public function test_the_show_page_returns_a_404_for_an_unknown_audit(): void
    {
        $this->get('/audits/does-not-exist')->assertNotFound();
    }
}
