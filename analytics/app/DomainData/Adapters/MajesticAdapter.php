<?php

declare(strict_types=1);

namespace App\DomainData\Adapters;

use App\DomainData\Contracts\DomainProviderAdapterInterface;
use App\DomainData\Exceptions\CapabilityNotSupportedException;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase Q1 (Domain Data Service Layer) — Majestic's own JSON API
 * (api.majestic.com/api/json), authenticated via a single API key
 * passed as the app_api_key query parameter — Majestic's own
 * "Internal/Reseller" auth mode (see
 * App\Enums\ApiProviderType::credentialFields()'s own docblock),
 * chosen over their separate OpenApp flow (a two-token flow meant for
 * building a public, multi-tenant Majestic-powered product for OTHER
 * people, not a good fit for this app's own single-account server-side
 * use). Requires the API option explicitly enabled on the underlying
 * Majestic account (majestic.com/account/api) before this adapter's
 * own calls will succeed.
 *
 * Only backlink-related capabilities — Majestic doesn't offer
 * domain_overview/organic_competitors/ranking_keywords/top_pages at
 * all (it's a pure link-intelligence product, not a full SEO
 * platform), so those four methods always throw.
 */
final class MajesticAdapter implements DomainProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    public function getDomainOverview(string $domain, string $country): array
    {
        throw new CapabilityNotSupportedException('Majestic does not provide domain traffic/keyword overview — use a DataForSEO Labs provider instead.');
    }

    public function getOrganicCompetitors(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Majestic does not provide organic competitor data — use a DataForSEO Labs provider instead.');
    }

    public function getRankingKeywords(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Majestic does not provide ranking keyword data — use a DataForSEO Labs provider instead.');
    }

    public function getTopPages(string $domain, string $country, int $limit): array
    {
        throw new CapabilityNotSupportedException('Majestic does not provide top-pages traffic data — use a DataForSEO Labs provider instead.');
    }

    /**
     * Majestic's own "GetIndexItemInfo" command — the closest
     * equivalent to a backlink summary, including Majestic's own
     * proprietary Trust Flow/Citation Flow metrics folded into
     * domain_rank here (an average of the two, 0-100 scale, since this
     * app's own shared interface only has ONE domain_rank slot — the
     * two individual Majestic-specific scores aren't separately
     * exposed through this shared interface).
     */
    public function getBacklinksSummary(string $domain): array
    {
        $body = $this->call('GetIndexItemInfo', [
            'items' => 1,
            'item0' => $domain,
        ]);

        $item = $body['DataTables']['Results']['Data'][0] ?? [];
        $trustFlow = $item['TrustFlow'] ?? null;
        $citationFlow = $item['CitationFlow'] ?? null;

        return [
            'total_backlinks' => $item['ExtBackLinks'] ?? null,
            'referring_domains' => $item['RefDomains'] ?? null,
            'domain_rank' => ($trustFlow !== null && $citationFlow !== null)
                ? (int) round(($trustFlow + $citationFlow) / 2)
                : null,
            'dofollow_percent' => null,
        ];
    }

    /**
     * Majestic's own "GetBackLinkData" command.
     */
    public function getBacklinksList(string $domain, int $limit): array
    {
        $body = $this->call('GetBackLinkData', [
            'item' => $domain,
            'Count' => min($limit, 1000),
            'datasource' => 'fresh',
        ]);

        $rows = $body['DataTables']['BackLinks']['Data'] ?? [];

        return array_map(static fn (array $row): array => [
            'source_url' => $row['SourceURL'] ?? '',
            'anchor_text' => $row['AnchorText'] ?? null,
            'link_type' => ($row['LinkType'] ?? '') === 'nofollow' ? 'nofollow' : 'dofollow',
            'first_seen' => $row['FirstIndexedDate'] ?? null,
            'source_domain_rank' => $row['SourceTrustFlow'] ?? null,
        ], $rows);
    }

    /**
     * Majestic's own "GetRefDomains" command.
     */
    public function getReferringDomains(string $domain, int $limit): array
    {
        $body = $this->call('GetRefDomains', [
            'item' => $domain,
            'Count' => min($limit, 1000),
        ]);

        $rows = $body['DataTables']['RefDomains']['Data'] ?? [];

        return array_map(static fn (array $row): array => [
            'domain' => $row['Domain'] ?? '',
            'backlinks' => $row['ExtBackLinks'] ?? 0,
            'domain_rank' => $row['TrustFlow'] ?? null,
        ], $rows);
    }

    /**
     * Majestic's own "GetAnchorText" command.
     */
    public function getAnchorTextDistribution(string $domain, int $limit): array
    {
        $body = $this->call('GetAnchorText', [
            'item' => $domain,
            'Count' => min($limit, 1000),
        ]);

        $rows = $body['DataTables']['AnchorText']['Data'] ?? [];

        return array_map(static fn (array $row): array => [
            'anchor_text' => $row['AnchorText'] ?? '',
            'count' => $row['RefDomains'] ?? 0,
        ], $rows);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function call(string $cmd, array $params): array
    {
        $response = Http::timeout(30)->get('https://api.majestic.com/api/json', array_merge([
            'app_api_key' => $this->provider->credential('api_key'),
            'cmd' => $cmd,
        ], $params));

        if (! $response->successful()) {
            throw new \RuntimeException("Majestic API request failed with HTTP {$response->status()}.");
        }

        $body = $response->json();

        if (($body['Code'] ?? null) !== 'OK') {
            throw new \RuntimeException('Majestic error: '.($body['ErrorMessage'] ?? $body['Code'] ?? 'unknown'));
        }

        return $body;
    }
}