<?php

declare(strict_types=1);

namespace App\DomainData\Contracts;

use App\DomainData\Exceptions\CapabilityNotSupportedException;

/**
 * Phase Q1 (Domain Data Service Layer) — the domain-data counterpart
 * to App\KeywordData\Contracts\ApiProviderAdapterInterface. Every
 * method takes ONE domain (never a keyword) — see App\Enums\DomainCapability's
 * own docblock for why this stayed a separate module from KeywordData
 * rather than merging the two. App\DomainData\DomainDataService is the
 * only caller of any of these methods anywhere in this app.
 */
interface DomainProviderAdapterInterface
{
    /**
     * @return array{organic_traffic: ?int, organic_keywords: ?int, domain_rank: ?int, paid_keywords: ?int}
     *
     * @throws CapabilityNotSupportedException
     */
    public function getDomainOverview(string $domain, string $country): array;

    /**
     * @return array<int, array{domain: string, common_keywords: ?int, competition_level: ?float}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getOrganicCompetitors(string $domain, string $country, int $limit): array;

    /**
     * @return array<int, array{keyword: string, position: ?int, volume: ?int, url: string}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getRankingKeywords(string $domain, string $country, int $limit): array;

    /**
     * @return array<int, array{url: string, estimated_traffic: ?int}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getTopPages(string $domain, string $country, int $limit): array;

    /**
     * @return array{total_backlinks: ?int, referring_domains: ?int, domain_rank: ?int, dofollow_percent: ?float}
     *
     * @throws CapabilityNotSupportedException
     */
    public function getBacklinksSummary(string $domain): array;

    /**
     * @return array<int, array{source_url: string, anchor_text: ?string, link_type: string, first_seen: ?string, source_domain_rank: ?int}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getBacklinksList(string $domain, int $limit): array;

    /**
     * @return array<int, array{domain: string, backlinks: int, domain_rank: ?int}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getReferringDomains(string $domain, int $limit): array;

    /**
     * @return array<int, array{anchor_text: string, count: int}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getAnchorTextDistribution(string $domain, int $limit): array;
}