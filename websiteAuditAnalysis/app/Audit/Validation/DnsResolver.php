<?php

declare(strict_types=1);

namespace App\Audit\Validation;

use App\Audit\Validation\Contracts\DnsResolverInterface;

final class DnsResolver implements DnsResolverInterface
{
    public function hasAnyRecord(string $host): bool
    {
        return checkdnsrr($host, 'ANY')
            || checkdnsrr($host, 'A')
            || checkdnsrr($host, 'AAAA')
            || checkdnsrr($host, 'MX');
    }

    public function resolveIp(string $host): ?string
    {
        $ip = gethostbyname($host);

        // gethostbyname() returns the original host unchanged on failure.
        return $ip !== $host ? $ip : null;
    }
}
