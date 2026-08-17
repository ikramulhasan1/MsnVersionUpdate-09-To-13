<?php

declare(strict_types=1);

namespace App\Audit\Services\Contracts;

use App\Audit\DTO\CreateAuditData;
use App\Models\Audit;

interface AuditServiceInterface
{
    public function submit(CreateAuditData $data): Audit;

    /**
     * Dispatches $audit's full pipeline (fetch, crawl, analyze,
     * assemble) via FetchAndCrawlJob — a real queued dispatch
     * (QUEUE_CONNECTION=database on this app, not 'sync' — see
     * routes/console.php's own scheduled queue:work comment for the
     * production incident that corrected an earlier, wrong assumption
     * elsewhere in this codebase that it ran synchronously in-process).
     * AuditCacheService::putProgress() calls made along the way (see
     * FetchAndCrawlJob / AssembleAnalysisResultsJob) are what the
     * result page's progress bar polls while the queue worker
     * processes it.
     *
     * $queue (Phase K3, Bulk Audit) — null (the default) means "let
     * this job land on whichever queue FetchAndCrawlJob would normally
     * use", exactly AuditController::store()'s own single-audit flow
     * already relies on. App\Audit\Services\BulkAuditBatchService
     * passes 'audit-bulk' explicitly instead, so audits submitted as
     * part of a bulk batch are processed on their own dedicated queue
     * (see routes/console.php's own scheduled queue:work — it
     * processes 'audit-bulk' alongside 'default') rather than
     * competing with an ordinary, single ad-hoc audit for the same
     * worker slot.
     */
    public function run(Audit $audit, ?string $queue = null): void;

    public function findOrFail(string $uuid): Audit;
}