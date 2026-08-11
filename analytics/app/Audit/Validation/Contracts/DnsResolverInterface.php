<?php

declare(strict_types=1);

namespace App\Audit\Validation\Contracts;

interface DnsResolverInterface
{
    /**
     * Whether the host has any DNS record at all (A, AAAA, MX, etc.).
     */
    public function hasAnyRecord(string $host): bool;

    /**
     * Resolve the host to an IPv4 address, or null if it doesn't resolve.
     */
    public function resolveIp(string $host): ?string;
}
