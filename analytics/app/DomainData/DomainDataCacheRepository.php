<?php

declare(strict_types=1);

namespace App\DomainData;

use App\Models\DomainDataCache;
use Illuminate\Support\Carbon;

/**
 * Phase Q1 (Domain Data Service Layer) — the domain-data counterpart
 * to App\KeywordData\KeywordDataCacheRepository. Same TTL, same
 * "expired treated as miss, not specially cleaned up" behavior — see
 * that class's own docblock for the full reasoning, identical here.
 */
final class DomainDataCacheRepository
{
    private const int TTL_DAYS = 7;

    public function get(string $domain, string $country, string $capability): mixed
    {
        $row = DomainDataCache::query()
            ->where('domain', $domain)
            ->where('country', $country)
            ->where('capability', $capability)
            ->where('expires_at', '>', now())
            ->first();

        return $row?->response;
    }

    public function put(string $domain, string $country, string $capability, mixed $response): void
    {
        DomainDataCache::query()->updateOrCreate(
            [
                'domain' => $domain,
                'country' => $country,
                'capability' => $capability,
            ],
            [
                'response' => $response,
                'expires_at' => Carbon::now()->addDays(self::TTL_DAYS),
            ],
        );
    }
}