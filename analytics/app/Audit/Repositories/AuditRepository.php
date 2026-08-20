<?php

declare(strict_types=1);

namespace App\Audit\Repositories;

use App\Audit\Enums\AuditStatus;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Models\Audit;

final class AuditRepository implements AuditRepositoryInterface
{
    public function __construct(
        private readonly Audit $model,
    ) {
    }

    public function create(array $attributes): Audit
    {
        // url_hash is a derived lookup column (see findLatestPendingByUrl()),
        // not something callers should ever need to supply themselves —
        // computed here so every insert stays consistent even if a future
        // caller forgets it.
        if (isset($attributes['url']) && ! isset($attributes['url_hash'])) {
            $attributes['url_hash'] = $this->hashUrl($attributes['url']);
        }

        return $this->model->newQuery()->create($attributes);
    }

    public function findByUuid(string $uuid): ?Audit
    {
        return $this->model->newQuery()
            ->where('uuid', $uuid)
            ->first();
    }

    public function updateStatus(Audit $audit, AuditStatus $status): Audit
    {
        // No refresh() here: Eloquent's update() already syncs $audit's
        // in-memory attributes (including updated_at) from the values it
        // just wrote, so a follow-up SELECT would only re-fetch data this
        // instance already has. updateStatus() is called multiple times
        // per job across the pipeline (FETCHING -> CRAWLING -> ANALYZING,
        // etc.) — cutting one SELECT per transition adds up.
        $audit->update(['status' => $status]);

        return $audit;
    }

    /**
     * PRODUCTION INCIDENT — read before removing the time-window check
     * below: this app's own real production data had 186 Audit rows
     * (out of 265 total — 70%) permanently stuck in a non-terminal
     * status (queued/crawling/analyzing), every one of them from a
     * SINGLE day, with NO corresponding row left in the `jobs` table
     * at all — their background jobs were gone (likely a queue reset/
     * crash that happened without the audits' own status ever being
     * correspondingly marked failed), but this method's own
     * whereNotIn(COMPLETED, FAILED) check had no way to tell "still
     * genuinely in progress" apart from "abandoned days ago". The
     * practical effect: for 87 distinct URLs, EVERY subsequent audit
     * request — including ones submitted by real, logged-in users days
     * later, well after Phase N2 added user_id — silently returned
     * that SAME ancient, stuck, owner-less row instead of ever creating
     * a fresh one. Not a single new Audit row was created for ANY of
     * those 87 URLs for days, which is also why their own user_id
     * stayed permanently null regardless of who actually asked to
     * audit them.
     *
     * The fix: a "pending" audit only counts as genuinely in-flight if
     * it was created within the last hour — comfortably longer than
     * this app's own real pipeline ever legitimately takes (see
     * routes/console.php's own --max-time=50 scheduled worker comment
     * for the actual per-run time budget), so a truly-still-processing
     * audit is never mistaken for an abandoned one. An audit older than
     * that is treated as abandoned, and a fresh request for the same
     * URL creates a brand new row rather than reusing it — the SAME
     * outcome as if this method returned null. The abandoned row itself
     * is left alone here (see
     * app/Console/Commands/MarkAbandonedAuditsFailedCommand.php for the
     * separate scheduled cleanup that actually marks it failed, so it
     * stops cluttering dashboards/lists as a phantom "still running"
     * entry too).
     */
    public function findLatestPendingByUrl(string $url): ?Audit
    {
        // Filters on the indexed, fixed-length url_hash rather than the
        // raw url column (up to 2048 chars, unindexed) — this query runs
        // on every audit submission, so avoiding a full table scan here
        // matters as the audits table grows. See the
        // add_url_hash_to_audits_table migration for why url itself
        // isn't indexed directly.
        return $this->model->newQuery()
            ->where('url_hash', $this->hashUrl($url))
            ->whereNotIn('status', [AuditStatus::COMPLETED->value, AuditStatus::FAILED->value])
            ->where('created_at', '>=', now()->subHour())
            ->latest('id')
            ->first();
    }

    private function hashUrl(string $url): string
    {
        return md5($url);
    }
}