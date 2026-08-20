<?php

declare(strict_types=1);

namespace App\DomainData\Adapters;

use App\DomainData\Contracts\DomainProviderAdapterInterface;
use App\DomainData\Exceptions\CapabilityNotSupportedException;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase Q1 (Domain Data Service Layer) — Moz's own Links API
 * (lsapi.seomoz.com), authenticated via HTTP Basic Auth using the
 * account's own Access ID as username and Secret Key as password —
 * Moz's current, documented authentication scheme for their v2 API
 * (their older Mozscape API used a different HMAC-signature scheme;
 * this app targets the newer, simpler one). If Moz's own API rejects
 * this auth style on a real account, this is the first place to check
 * against Moz's own current API docs — third-party API details can
 * shift between when this was written and when it's actually used.
 *
 * Narrowest capability coverage of the three domain providers — only
 * backlinks_summary and referring_domains; Moz's own Link Explorer
 * doesn't expose a raw backlinks LIST or anchor-text breakdown through
 * this same tier of API access the way DataForSEO/Majestic do.
 */
final class MozAdapter implements DomainProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    public function getDomainOverview(string $domain, string $country): array
    {
        throw new CapabilityNotSupportedException('Moz does not provide organic traffic/keyword overview — use a DataForSEO Labs provider instead.');
    }

    public function getOrganicCompetitors(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Moz does not provide organic competitor data — use a DataForSEO Labs provider instead.');
    }

    public function getRankingKeywords(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Moz does not provide ranking keyword data — use a DataForSEO Labs provider instead.');
    }

    public function getTopPages(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Moz does not provide top-pages traffic data — use a DataForSEO Labs provider instead.');
    }

    /**
     * Moz's own "url-metrics" endpoint — Domain Authority (DA) folded
     * into domain_rank here (Moz's own 0-100 proprietary score, the
     * same slot DataForSEO's own rank and Majestic's Trust/Citation
     * Flow average occupy for their own adapters).
     */
    public function getBacklinksSummary(string $domain): array
    {
        $body = $this->post('/v2/url_metrics', ['targets' => [$domain]]);

        $item = $body['results'][0] ?? [];

        return [
            'total_backlinks' => $item['links_to_root_domain'] ?? null,
            'referring_domains' => $item['root_domains_to_root_domain'] ?? null,
            'domain_rank' => $item['domain_authority'] ?? null,
            'dofollow_percent' => null,
        ];
    }

    public function getBacklinksList(string $domain, int $limit): array
    {
        throw new CapabilityNotSupportedException('Moz\'s Link Explorer does not expose a raw backlink list at this access tier — use a DataForSEO Backlinks or Majestic provider instead.');
    }

    /**
     * Moz's own "links" endpoint, target_scope = domain, filtered to
     * distinct linking root domains.
     */
    public function getReferringDomains(string $domain, int $limit): array
    {
        $body = $this->post('/v2/links', [
            'target' => $domain,
            'target_scope' => 'domain',
            'scope' => 'domain_to_domain',
            'limit' => $limit,
        ]);

        $items = $body['results'] ?? [];

        return array_map(static fn (array $row): array => [
            'domain' => $row['source_domain'] ?? '',
            'backlinks' => $row['link_count'] ?? 1,
            'domain_rank' => $row['source_domain_authority'] ?? null,
        ], $items);
    }

    public function getAnchorTextDistribution(string $domain, int $limit): array
    {
        throw new CapabilityNotSupportedException('Moz\'s Link Explorer does not expose anchor text distribution at this access tier — use a DataForSEO Backlinks or Majestic provider instead.');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function post(string $path, array $payload): array
    {
        $response = Http::withBasicAuth($this->provider->credential('access_id'), $this->provider->credential('secret_key'))
            ->timeout(30)
            ->post("https://lsapi.seomoz.com{$path}", $payload);

        if (! $response->successful()) {
            throw new \RuntimeException("Moz API request failed with HTTP {$response->status()}.");
        }

        return $response->json() ?? [];
    }
}