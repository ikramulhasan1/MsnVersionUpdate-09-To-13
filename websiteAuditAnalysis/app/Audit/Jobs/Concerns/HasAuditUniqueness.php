<?php

declare(strict_types=1);

namespace App\Audit\Jobs\Concerns;

/**
 * Implements the {@see \Illuminate\Contracts\Queue\ShouldBeUnique}
 * contract for audit-pipeline jobs: while a job with the same
 * uniqueId() is already on the queue or executing, Laravel silently
 * drops any further dispatch of it — this is what actually prevents
 * duplicate *dispatch* (as opposed to WithoutOverlapping, which
 * prevents duplicate concurrent *execution*; see {@see \App\Audit\Jobs\AuditJob}).
 *
 * uniqueId() is scoped to $auditUuid by default so at most one
 * instance of a given job class is ever in flight per audit. Jobs
 * that dispatch several parallel instances of themselves for the
 * same audit (e.g. one AnalyzeChunkJob per chunk) must override
 * uniqueIdSuffix() so each chunk gets its own identity instead of
 * colliding with (and silently dropping) its siblings.
 */
trait HasAuditUniqueness
{
    public function uniqueId(): string
    {
        return static::class . ':' . $this->auditUuid . $this->uniqueIdSuffix();
    }

    /**
     * How long the uniqueness lock is held for, in seconds. Set well
     * above any realistic pipeline duration for a single audit so a
     * stuck/slow job can't be "un-uniqued" and duplicated mid-run.
     */
    public function uniqueFor(): int
    {
        return (int) config('audit.queue.unique_for_seconds', 3600);
    }

    protected function uniqueIdSuffix(): string
    {
        return '';
    }
}
