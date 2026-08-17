<?php

declare(strict_types=1);

namespace App\Audit\Services;

use App\Audit\Enums\AuditMode;
use App\Audit\Enums\AuditStatus;
use App\Audit\Enums\BulkAuditBatchStatus;
use App\Audit\Jobs\BulkFetchJob;
use App\Models\Audit;
use App\Models\BulkAuditBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Phase K3 (Bulk Audit) — the single place every bulk submission entry
 * point (BulkAuditController's own pasted-URL-list and CSV-upload
 * forms; DiscoveryController::bulkAudit()'s existing "Bulk Audit
 * Selected" checkbox flow) ends up calling. One BulkAuditBatch, one
 * App\Models\Audit per URL.
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
 *
 * Phase K4 (parallel fetching): rather than dispatching each audit's
 * own AuditServiceInterface::run() individually in the loop below
 * (Phase K3's original shape — every Audit row's own
 * FetchAndCrawlJob, fetching its own homepage sequentially once
 * Laravel's queue worker got to it), this class now creates every
 * Audit row first, then dispatches ONE App\Audit\Jobs\BulkFetchJob for
 * the whole batch — see that job's own docblock for how it fetches
 * every audit's own homepage CONCURRENTLY before fanning out each
 * one's ordinary FetchAndCrawlJob. This class no longer needs
 * AuditServiceInterface injected at all — BulkFetchJob is responsible
 * for dispatching FetchAndCrawlJob itself, onto the same 'audit-bulk'
 * queue this class's own QUEUE constant still names (BulkFetchJob
 * reads its own $queue property, which is set to the same value —
 * see that job's own docblock for why they need to match).
 */
final class BulkAuditBatchService
{
    /**
     * BulkFetchJob (and, in turn, every FetchAndCrawlJob it dispatches
     * for this batch's own audits) lands here — see
     * App\Audit\Jobs\BulkFetchJob's own docblock, and
     * App\Audit\Services\Contracts\AuditServiceInterface::run()'s, for
     * why a dedicated queue exists at all.
     */
    private const string QUEUE = 'audit-bulk';

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
            Audit::query()->create([
                'uuid' => (string) Str::uuid(),
                'url' => $url,
                'status' => AuditStatus::QUEUED->value,
                'mode' => $mode->value,
                'bulk_audit_batch_id' => $batch->id,
            ]);
        }

        // A single job for the WHOLE batch — see this class's own
        // docblock (Phase K4) for why that's now BulkFetchJob rather
        // than one AuditServiceInterface::run() call per audit here.
        BulkFetchJob::dispatch($batch->uuid)->onQueue(self::QUEUE);

        // 'processing' the moment its own audits have actually been
        // created and the batch fetch has been dispatched, not left at
        // 'pending' until some later poll happens to notice — a batch
        // with total_count > 0 rows already created is, by definition,
        // already processing.
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