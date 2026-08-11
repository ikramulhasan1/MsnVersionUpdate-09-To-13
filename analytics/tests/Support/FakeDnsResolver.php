<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Validation\Contracts\DnsResolverInterface;

final class FakeDnsResolver implements DnsResolverInterface
{
    public function __construct(
        private readonly bool $hasRecord = true,
        private readonly ?string $ip = '93.184.216.34',
    ) {
    }

    public function hasAnyRecord(string $host): bool
    {
        return $this->hasRecord;
    }

    public function resolveIp(string $host): ?string
    {
        return $this->ip;
    }
}
