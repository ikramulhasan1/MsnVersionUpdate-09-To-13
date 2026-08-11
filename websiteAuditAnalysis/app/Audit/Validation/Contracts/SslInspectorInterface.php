<?php

declare(strict_types=1);

namespace App\Audit\Validation\Contracts;

use App\Audit\Validation\DTO\SslInfo;

interface SslInspectorInterface
{
    public function inspect(string $host, int $timeoutSeconds): SslInfo;
}
