<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Validation\Contracts\SslInspectorInterface;
use App\Audit\Validation\DTO\SslInfo;

final class FakeSslInspector implements SslInspectorInterface
{
    public function __construct(
        private readonly SslInfo $result,
    ) {
    }

    public function inspect(string $host, int $timeoutSeconds): SslInfo
    {
        return $this->result;
    }
}
