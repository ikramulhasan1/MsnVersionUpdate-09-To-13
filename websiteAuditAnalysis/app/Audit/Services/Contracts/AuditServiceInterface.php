<?php

declare(strict_types=1);

namespace App\Audit\Services\Contracts;

use App\Audit\DTO\CreateAuditData;
use App\Models\Audit;

interface AuditServiceInterface
{
    public function submit(CreateAuditData $data): Audit;

    /**
     * Runs $audit's full pipeline (fetch, crawl, analyze, assemble)
     * directly in the current PHP process — no queue worker involved.
     * AuditCacheService::putProgress() calls made along the way (see
     * FetchAndCrawlJob / AssembleAnalysisResultsJob) are what the
     * result page's progress bar polls while this runs.
     */
    public function run(Audit $audit): void;

    public function findOrFail(string $uuid): Audit;
}
