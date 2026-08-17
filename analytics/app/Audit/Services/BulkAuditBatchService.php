<?php

declare(strict_types=1);

namespace App\Audit\Services;

use App\Audit\Enums\AuditMode;
use App\Audit\Enums\AuditStatus;
use App\Audit\Enums\BulkAuditBatchStatus;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Models\Audit;
use App\Models\BulkAuditBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Phase K3 (Bulk Audit) — the single place every bulk submission entry
 * point (BulkAuditController's own pasted-URL-list and CSV-upload
 * forms; DiscoveryController::bulkAudit()'s existing "Bulk Audit
 * Selected" checkbox flow) ends up calling. One BulkAuditBatch, one
 * App\Models\Audit per URL, every one of those audits' own pipelines
 * dispatched onto the dedicated 'audit-bulk' queue (see
 * App\Audit\Services\Contracts\AuditServiceInterface::run()'s own
 * docblock for why that queue exists, and
 * routes/console.php's own scheduled queue:work for how it gets
 * processed without a persistent worker).
 *
 * Deliberately does NOT reuse AuditServiceInterface::submit()'s own
 * duplicate-in-flight-audit reuse logic — that check exists so
 * clicking "Analyze" twice on the same URL from the single-audit form
 * doesn't queue the pipeline twice for it, which is the right behavior
 * there but the wrong one here: a URL appearing more than once ACROSS
 * a bulk submission (or matching some OTHER already-in-flight audit
 * from a different context entirely) should still get its own Audit
 * row and its own place in THIS batch's own total_count/results page
 * (Phase K5), not silently disappear into an unrelated audit this
 * batch has no relationship to. What this class DOES dedupe is
 * touched on in normalizeUrls()'s own docblock — repeats WITHIN the
 * same submission only.
 */
final class BulkAuditBatchService
{
    /**
     * Every App\Audit\Jobs\FetchAndCrawlJob dispatched for a bulk
     * audit's own pipeline lands here — see
     * AuditServiceInterface::run()'s own docblock for exactly what
     * that isolates this queue from, and why.
     */
    private const string QUEUE = 'audit-bulk';

    public function __construct(
        private readonly AuditServiceInterface $auditService,
    ) {
    }

    /**
     * @param array<int, string> $urls
     */
    public function createBatch(array $urls, AuditMode $mode, ?string $name = null): BulkAuditBatch
    {
        $urls = $this->normalizeUrls($urls);

        $batch = BulkAuditBatch::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'total_count' => $urls->count(),
            'completed_count' => 0,
            'failed_count' => 0,
            'status' => BulkAuditBatchStatus::PENDING->value,
            'mode' => $mode->value,
        ]);

        foreach ($urls as $url) {
            $audit = Audit::query()->create([
                'uuid' => (string) Str::uuid(),
                'url' => $url,
                'status' => AuditStatus::QUEUED->value,
                'mode' => $mode->value,
                'bulk_audit_batch_id' => $batch->id,
            ]);

            $this->auditService->run($audit, self::QUEUE);
        }

        // 'processing' the moment its own audits have actually been
        // dispatched, not left at 'pending' until some later poll
        // happens to notice — a batch with total_count > 0 rows already
        // queued is, by definition, already processing.
        $batch->update(['status' => BulkAuditBatchStatus::PROCESSING->value]);

        return $batch;
    }

    /**
     * Trims, drops empty lines, strips a trailing slash (matching
     * App\Audit\DTO\CreateAuditData::fromArray()'s own normalization,
     * so a URL submitted through the single-audit form and one
     * submitted as part of a bulk batch end up hashed/deduplicated the
     * same way downstream), and removes exact duplicates WITHIN this
     * one submission (case-sensitive — "Example.com" and "example.com"
     * are left as two separate entries, since this class has no
     * involvement in deciding what counts as "the same site" the way
     * App\Discovery\Normalization\DomainNormalizer does for a
     * completely different module's own, more thorough deduplication).
     * Does NOT check against any OTHER audit already in the database —
     * see this class's own docblock for why that's deliberate.
     *
     * @param array<int, string> $urls
     * @return Collection<int, string>
     */
    private function normalizeUrls(array $urls): Collection
    {
        return collect($urls)
            ->map(static fn (string $url): string => rtrim(trim($url), '/'))
            ->filter(static fn (string $url): bool => $url !== '')
            ->unique()
            ->values();
    }
}