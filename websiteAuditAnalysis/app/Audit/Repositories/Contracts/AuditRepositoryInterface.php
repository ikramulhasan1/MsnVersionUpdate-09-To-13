<?php

declare(strict_types=1);

namespace App\Audit\Repositories\Contracts;

use App\Audit\Enums\AuditStatus;
use App\Models\Audit;

interface AuditRepositoryInterface
{
    /**
     * @param array{uuid: string, url: string, status: string} $attributes
     */
    public function create(array $attributes): Audit;

    public function findByUuid(string $uuid): ?Audit;

    public function updateStatus(Audit $audit, AuditStatus $status): Audit;

    public function findLatestPendingByUrl(string $url): ?Audit;
}
