<?php

declare(strict_types=1);

namespace App\KeywordData;

use App\Enums\ApiProviderType;
use App\KeywordData\Adapters\DataForSeoKeywordsAdapter;
use App\KeywordData\Adapters\DataForSeoLabsAdapter;
use App\KeywordData\Adapters\GoogleAdsAdapter;
use App\KeywordData\Contracts\ApiProviderAdapterInterface;
use App\KeywordData\Exceptions\NoAvailableProviderException;
use App\Enums\KeywordCapability;
use App\Models\ApiProvider;
use App\Models\ApiUsageLog;

/**
 * Phase O2 (Keyword Data Service Layer) — the ONE entry point every
 * feature in this app uses for keyword data. Phase O3's Keyword
 * Research page and Phase O4's Keyword Magic Tool both call this
 * class exclusively — neither ever instantiates an adapter or touches
 * App\Models\ApiProvider directly. Every public method here follows
 * the same shape: check cache first (App\KeywordData\KeywordDataCacheRepository),
 * and only for whatever's actually missing, try each ACTIVE provider
 * offering that capability in priority order
 * (tryProvidersInOrder()) — the first one that succeeds wins, a
 * failure moves on to the next, and running out of providers throws
 * NoAvailableProviderException rather than ever returning fabricated
 * data.
 */
final class KeywordDataService
{
    public function __construct(
        private readonly KeywordDataCacheRepository $cache,
    ) {
    }

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?int>
     */
    public function getSearchVolume(array $keywords, string $country, string $language): array
    {
        return $this->bulkKeywordLookup(
            KeywordCapability::VOLUME,
            $keywords,
            $country,
            $language,
            static fn (ApiProviderAdapterInterface $adapter, array $missing): array => $adapter->getSearchVolume($missing, $country, $language),
        );
    }

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?float>
     */
    public function getCpc(array $keywords, string $country, string $language): array
    {
        return $this->bulkKeywordLookup(
            KeywordCapability::CPC,
            $keywords,
            $country,
            $language,
            static fn (ApiProviderAdapterInterface $adapter, array $missing): array => $adapter->getCpc($missing, $country, $language),
        );
    }

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?int>
     */
    public function getKeywordDifficulty(array $keywords, string $country, string $language): array
    {
        return $this->bulkKeywordLookup(
            KeywordCapability::DIFFICULTY,
            $keywords,
            $country,
            $language,
            static fn (ApiProviderAdapterInterface $adapter, array $missing): array => $adapter->getKeywordDifficulty($missing, $country),
        );
    }

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?string>
     */
    public function getSearchIntent(array $keywords, string $country, string $language): array
    {
        return $this->bulkKeywordLookup(
            KeywordCapability::SEARCH_INTENT,
            $keywords,
            $country,
            $language,
            static fn (ApiProviderAdapterInterface $adapter, array $missing): array => $adapter->getSearchIntent($missing, $country, $language),
        );
    }

    /**
     * @return array<int, array{keyword: string, volume: ?int, cpc: ?float, difficulty: ?int}>
     *
     * @throws NoAvailableProviderException
     */
    public function getRelatedKeywords(string $seedKeyword, string $country, string $language, int $limit = 200): array
    {
        $cached = $this->cache->get($seedKeyword, $country, $language, KeywordCapability::RELATED_KEYWORDS->value);

        if ($cached !== null) {
            return $cached;
        }

        $result = $this->tryProvidersInOrder(
            KeywordCapability::RELATED_KEYWORDS,
            static fn (ApiProviderAdapterInterface $adapter): array => $adapter->getRelatedKeywords($seedKeyword, $country, $language, $limit),
            keywordCount: 1,
        );

        $this->cache->put($seedKeyword, $country, $language, KeywordCapability::RELATED_KEYWORDS->value, $result);

        return $result;
    }

    /**
     * @return array{features: array<int, string>, top_results: array<int, array{url: string, domain: string, title: ?string}>, questions: array<int, string>}
     *
     * @throws NoAvailableProviderException
     */
    public function getSerpData(string $keyword, string $country, string $language): array
    {
        $cached = $this->cache->get($keyword, $country, $language, KeywordCapability::SERP_DATA->value);

        if ($cached !== null) {
            return $cached;
        }

        $result = $this->tryProvidersInOrder(
            KeywordCapability::SERP_DATA,
            static fn (ApiProviderAdapterInterface $adapter): array => $adapter->getSerpData($keyword, $country, $language),
            keywordCount: 1,
        );

        $this->cache->put($keyword, $country, $language, KeywordCapability::SERP_DATA->value, $result);

        return $result;
    }

    /**
     * Phase O3 (Keyword Research page) — added after this service's
     * own initial Phase O2 version. See
     * App\Enums\KeywordCapability::VOLUME_TREND's own docblock.
     *
     * @return array<int, array{month: string, volume: ?int}>
     *
     * @throws NoAvailableProviderException
     */
    public function getSearchVolumeTrend(string $keyword, string $country, string $language): array
    {
        $cached = $this->cache->get($keyword, $country, $language, KeywordCapability::VOLUME_TREND->value);

        if ($cached !== null) {
            return $cached;
        }

        $result = $this->tryProvidersInOrder(
            KeywordCapability::VOLUME_TREND,
            static fn (ApiProviderAdapterInterface $adapter): array => $adapter->getSearchVolumeTrend($keyword, $country, $language),
            keywordCount: 1,
        );

        $this->cache->put($keyword, $country, $language, KeywordCapability::VOLUME_TREND->value, $result);

        return $result;
    }

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?int>
     */
    public function getCompetitiveDensity(array $keywords, string $country, string $language): array
    {
        return $this->bulkKeywordLookup(
            KeywordCapability::COMPETITIVE_DENSITY,
            $keywords,
            $country,
            $language,
            static fn (ApiProviderAdapterInterface $adapter, array $missing): array => $adapter->getCompetitiveDensity($missing, $country, $language),
        );
    }
     * difficulty, intent) above — splits the requested keywords into
     * already-cached vs genuinely missing, only sends the missing ones
     * to a real provider, then merges cached + fresh results back into
     * ONE array covering every originally-requested keyword.
     *
     * @param  array<int, string>  $keywords
     * @param  callable(ApiProviderAdapterInterface, array<int, string>): array<string, mixed>  $fetcher
     * @return array<string, mixed>
     */
    private function bulkKeywordLookup(
        KeywordCapability $capability,
        array $keywords,
        string $country,
        string $language,
        callable $fetcher,
    ): array {
        $result = [];
        $missing = [];

        foreach ($keywords as $keyword) {
            $cached = $this->cache->get($keyword, $country, $language, $capability->value);

            if ($cached !== null) {
                $result[$keyword] = $cached['value'] ?? null;
            } else {
                $missing[] = $keyword;
            }
        }

        if ($missing === []) {
            return $result;
        }

        $fresh = $this->tryProvidersInOrder(
            $capability,
            static fn (ApiProviderAdapterInterface $adapter): array => $fetcher($adapter, $missing),
            keywordCount: count($missing),
        );

        foreach ($fresh as $keyword => $value) {
            $this->cache->put($keyword, $country, $language, $capability->value, ['value' => $value]);
            $result[$keyword] = $value;
        }

        return $result;
    }

    /**
     * The fallback engine every public method above ultimately runs
     * through: every ACTIVE ApiProvider row that lists $capability
     * among its own capabilities, tried in ascending priority order —
     * the first successful call wins and gets logged
     * (App\Models\ApiUsageLog); a thrown exception from one provider
     * is reported and simply moves on to the next, never surfaced to
     * the caller unless EVERY provider is exhausted, at which point
     * NoAvailableProviderException carries the last real failure
     * reason forward so it isn't lost.
     *
     * @template T
     *
     * @param  callable(ApiProviderAdapterInterface): T  $attempt
     * @return T
     *
     * @throws NoAvailableProviderException
     */
    private function tryProvidersInOrder(KeywordCapability $capability, callable $attempt, int $keywordCount): mixed
    {
        $providers = ApiProvider::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get()
            ->filter(fn (ApiProvider $provider): bool => $provider->hasCapability($capability->value));

        if ($providers->isEmpty()) {
            throw new NoAvailableProviderException("No active provider configured for capability '{$capability->value}'. Ask an Admin to add and activate one under API Providers.");
        }

        $lastException = null;

        foreach ($providers as $provider) {
            try {
                $adapter = $this->adapterFor($provider);
                $result = $attempt($adapter);

                $this->logUsage($provider, $capability, $keywordCount);

                return $result;
            } catch (\Throwable $exception) {
                report($exception);
                $lastException = $exception;

                continue;
            }
        }

        throw new NoAvailableProviderException(
            "Every active provider for capability '{$capability->value}' failed.",
            previous: $lastException,
        );
    }

    private function adapterFor(ApiProvider $provider): ApiProviderAdapterInterface
    {
        return match ($provider->type) {
            ApiProviderType::DATAFORSEO_KEYWORDS => new DataForSeoKeywordsAdapter($provider),
            ApiProviderType::DATAFORSEO_LABS => new DataForSeoLabsAdapter($provider),
            ApiProviderType::GOOGLE_ADS => new GoogleAdsAdapter($provider),
        };
    }

    private function logUsage(ApiProvider $provider, KeywordCapability $capability, int $keywordCount): void
    {
        ApiUsageLog::query()->create([
            'api_provider_id' => $provider->id,
            'capability' => $capability->value,
            'keyword_count' => $keywordCount,
            'estimated_cost_usd' => $this->estimatedCostUsd($provider->type, $capability, $keywordCount),
            'created_at' => now(),
        ]);
    }

    /**
     * Rough, hand-maintained per-call cost estimates from each
     * provider's own PUBLISHED pricing at the time this was written
     * (dataforseo.com/pricing — Keywords Data API $0.075/1,000
     * keywords, Labs API roughly $0.025/1,000 calls depending on
     * endpoint; Google Ads API itself has no per-call charge, only the
     * cost of maintaining Google Ads developer-token access, so it's
     * treated as effectively free here). These are ESTIMATES for
     * budgeting purposes only — see api_usage_logs' own migration
     * docblock for why this is never treated as a real invoice.
     * Update these figures if a provider's own published pricing
     * changes.
     */
    private function estimatedCostUsd(ApiProviderType $type, KeywordCapability $capability, int $keywordCount): float
    {
        return match ($type) {
            ApiProviderType::DATAFORSEO_KEYWORDS => ($keywordCount / 1000) * 0.075,
            ApiProviderType::DATAFORSEO_LABS => ($keywordCount / 1000) * 0.025,
            ApiProviderType::GOOGLE_ADS => 0.0,
        };
    }
}