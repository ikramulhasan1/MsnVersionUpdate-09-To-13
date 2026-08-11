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
            ->latest('id')
            ->first();
    }

    private function hashUrl(string $url): string
    {
        return md5($url);
    }
}
