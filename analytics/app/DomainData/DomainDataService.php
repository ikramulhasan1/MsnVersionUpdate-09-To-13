<?php

declare(strict_types=1);

namespace App\DomainData;

use App\DomainData\Adapters\DataForSeoDomainAdapter;
use App\DomainData\Adapters\MajesticAdapter;
use App\DomainData\Adapters\MozAdapter;
use App\DomainData\Contracts\DomainProviderAdapterInterface;
use App\DomainData\Exceptions\NoAvailableProviderException;
use App\Enums\ApiProviderType;
use App\Enums\DomainCapability;
use App\Models\ApiProvider;
use App\Models\ApiUsageLog;

/**
 * Phase Q1 (Domain Data Service Layer) — the domain-data counterpart
 * to App\KeywordData\KeywordDataService, same design throughout: check
 * cache first, try every ACTIVE provider offering the needed
 * capability in priority order, log real (cache-miss) usage, throw
 * NoAvailableProviderException only once every provider is exhausted.
 * Phase Q2's Competitor Analysis page and Phase Q3's Backlink Analysis
 * page are this service's only callers — neither ever touches an
 * adapter or App\Models\ApiProvider directly.
 *
 * A domain-level capability (backlinks_summary, referring_domains,
 * etc.) can legitimately have MULTIPLE active providers at once —
 * DATAFORSEO_BACKLINKS, MAJESTIC, and MOZ can all be active
 * simultaneously, each offering overlapping capabilities — priority
 * order is what decides which one is actually tried first, with
 * automatic fallback to the next if the first fails, exactly the same
 * mechanism KeywordDataService already established for its own
 * multi-provider capabilities.
 */
final class DomainDataService
{
    public function __construct(
        private readonly DomainDataCacheRepository $cache,
    ) {
    }

    /**
     * @return array{organic_traffic: ?int, organic_keywords: ?int, domain_rank: ?int, paid_keywords: ?int}
     */
    public function getDomainOverview(string $domain, string $country): array
    {
        return $this->cachedFetch(
            DomainCapability::DOMAIN_OVERVIEW,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getDomainOverview($domain, $country),
        );
    }

    /**
     * @return array<int, array{domain: string, common_keywords: ?int, competition_level: ?float}>
     */
    public function getOrganicCompetitors(string $domain, string $country, int $limit = 20): array
    {
        return $this->cachedFetch(
            DomainCapability::ORGANIC_COMPETITORS,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getOrganicCompetitors($domain, $country, $limit),
        );
    }

    /**
     * @return array<int, array{keyword: string, position: ?int, volume: ?int, url: string}>
     */
    public function getRankingKeywords(string $domain, string $country, int $limit = 50): array
    {
        return $this->cachedFetch(
            DomainCapability::RANKING_KEYWORDS,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getRankingKeywords($domain, $country, $limit),
        );
    }

    /**
     * @return array<int, array{url: string, estimated_traffic: ?int}>
     */
    public function getTopPages(string $domain, string $country, int $limit = 20): array
    {
        return $this->cachedFetch(
            DomainCapability::TOP_PAGES,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getTopPages($domain, $country, $limit),
        );
    }

    /**
     * @return array{total_backlinks: ?int, referring_domains: ?int, domain_rank: ?int, dofollow_percent: ?float}
     */
    public function getBacklinksSummary(string $domain, string $country = 'Global'): array
    {
        return $this->cachedFetch(
            DomainCapability::BACKLINKS_SUMMARY,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getBacklinksSummary($domain),
        );
    }

    /**
     * @return array<int, array{source_url: string, anchor_text: ?string, link_type: string, first_seen: ?string, source_domain_rank: ?int}>
     */
    public function getBacklinksList(string $domain, string $country = 'Global', int $limit = 100): array
    {
        return $this->cachedFetch(
            DomainCapability::BACKLINKS_LIST,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getBacklinksList($domain, $limit),
        );
    }

    /**
     * @return array<int, array{domain: string, backlinks: int, domain_rank: ?int}>
     */
    public function getReferringDomains(string $domain, string $country = 'Global', int $limit = 50): array
    {
        return $this->cachedFetch(
            DomainCapability::REFERRING_DOMAINS,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getReferringDomains($domain, $limit),
        );
    }

    /**
     * @return array<int, array{anchor_text: string, count: int}>
     */
    public function getAnchorTextDistribution(string $domain, string $country = 'Global', int $limit = 50): array
    {
        return $this->cachedFetch(
            DomainCapability::ANCHOR_TEXT_DISTRIBUTION,
            $domain,
            $country,
            static fn (DomainProviderAdapterInterface $adapter): array => $adapter->getAnchorTextDistribution($domain, $limit),
        );
    }

    /**
     * Shared by every public method above — check cache, and only on
     * a genuine miss call tryProvidersInOrder() then cache the fresh
     * result.
     *
     * @template T
     *
     * @param  callable(DomainProviderAdapterInterface): T  $attempt
     * @return T
     */
    private function cachedFetch(DomainCapability $capability, string $domain, string $country, callable $attempt): mixed
    {
        $cached = $this->cache->get($domain, $country, $capability->value);

        if ($cached !== null) {
            return $cached;
        }

        $result = $this->tryProvidersInOrder($capability, $attempt);

        $this->cache->put($domain, $country, $capability->value, $result);

        return $result;
    }

    /**
     * @template T
     *
     * @param  callable(DomainProviderAdapterInterface): T  $attempt
     * @return T
     *
     * @throws NoAvailableProviderException
     */
    private function tryProvidersInOrder(DomainCapability $capability, callable $attempt): mixed
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

                $this->logUsage($provider, $capability);

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

    private function adapterFor(ApiProvider $provider): DomainProviderAdapterInterface
    {
        return match ($provider->type) {
            ApiProviderType::DATAFORSEO_LABS, ApiProviderType::DATAFORSEO_BACKLINKS => new DataForSeoDomainAdapter($provider),
            ApiProviderType::MAJESTIC => new MajesticAdapter($provider),
            ApiProviderType::MOZ => new MozAdapter($provider),
            default => throw new \LogicException("Provider type {$provider->type->value} has no domain-data adapter."),
        };
    }

    private function logUsage(ApiProvider $provider, DomainCapability $capability): void
    {
        ApiUsageLog::query()->create([
            'api_provider_id' => $provider->id,
            'capability' => $capability->value,
            'keyword_count' => 1,
            'estimated_cost_usd' => $this->estimatedCostUsd($provider->type),
            'created_at' => now(),
        ]);
    }

    /**
     * PRICING SOURCE — DataForSEO Backlinks API's own published rate
     * ($0.024/request + $0.000036/row, dataforseo.com/pricing/backlinks
     * — this estimate assumes a typical ~50-row response as a rough
     * middle ground, not a precise per-call count). Majestic and Moz
     * are BOTH flat monthly subscriptions (Majestic from $49.99/mo,
     * Moz from $99/mo) with no real per-call price at all — 0.0 here
     * isn't "free", it's "this app has no way to attribute a fraction
     * of a fixed monthly bill to one single API call"; see this app's
     * own Admin API Usage page for the same caveat surfaced to
     * whoever's reading these numbers.
     */
    private function estimatedCostUsd(ApiProviderType $type): float
    {
        return match ($type) {
            ApiProviderType::DATAFORSEO_LABS, ApiProviderType::DATAFORSEO_BACKLINKS => 0.024 + (50 * 0.000036),
            ApiProviderType::MAJESTIC, ApiProviderType::MOZ => 0.0,
            default => 0.0,
        };
    }
}