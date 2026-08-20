<?php

declare(strict_types=1);

namespace App\KeywordData;

use App\Models\KeywordDataCache;
use Illuminate\Support\Carbon;

/**
 * Phase O2 (Keyword Data Service Layer) — the ONE place
 * App\KeywordData\KeywordDataService reads/writes
 * App\Models\KeywordDataCache through; kept as its own small class
 * rather than inline in the service so the "how caching works" logic
 * stays in one obviously-named place.
 */
final class KeywordDataCacheRepository
{
    private const int TTL_DAYS = 7;

    /**
     * Null on a genuine miss (never cached, OR cached but expired —
     * an expired row is treated identically to no row at all here,
     * not specially cleaned up on read; a small periodic cleanup of
     * expired rows is a reasonable future addition but not required
     * for correctness, since expired rows are simply never returned).
     */
    public function get(string $keyword, string $country, string $language, string $capability): mixed
    {
        $row = KeywordDataCache::query()
            ->where('keyword', $keyword)
            ->where('country', $country)
            ->where('language', $language)
            ->where('capability', $capability)
            ->where('expires_at', '>', now())
            ->first();

        return $row?->response;
    }

    public function put(string $keyword, string $country, string $language, string $capability, mixed $response): void
    {
        KeywordDataCache::query()->updateOrCreate(
            [
                'keyword' => $keyword,
                'country' => $country,
                'language' => $language,
                'capability' => $capability,
            ],
            [
                'response' => $response,
                'expires_at' => Carbon::now()->addDays(self::TTL_DAYS),
            ],
        );
    }
}