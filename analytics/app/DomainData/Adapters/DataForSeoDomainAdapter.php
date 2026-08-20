<?php

declare(strict_types=1);

namespace App\DomainData\Adapters;

use App\DomainData\Contracts\DomainProviderAdapterInterface;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase Q1 (Domain Data Service Layer) — the ONE adapter that answers
 * EVERY DomainCapability, unlike Majestic/Moz below which only cover
 * the backlink-related ones. Uses TWO real DataForSEO products under
 * one shared login/password (see App\Enums\ApiProviderType::credentialFields()'s
 * own docblock): Labs API for domain_overview/organic_competitors/
 * ranking_keywords/top_pages, Backlinks API for
 * backlinks_summary/backlinks_list/referring_domains/
 * anchor_text_distribution. Both share this class rather than
 * splitting into two adapters — a single DATAFORSEO_BACKLINKS-typed
 * ApiProvider row is what actually gets THIS class instantiated for
 * the backlink methods (see App\DomainData\DomainDataService::adapterFor()),
 * while a DATAFORSEO_LABS-typed row's own domain capabilities ALSO
 * resolve to this same class for the overview/competitor methods —
 * both are the identical login/password pair working against
 * different DataForSEO endpoints, so one class implementing the whole
 * interface (and simply calling whichever underlying API a given
 * method needs) is simpler than two adapters that would otherwise
 * duplicate the same HTTP-call plumbing.
 */
final class DataForSeoDomainAdapter implements DomainProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    /**
     * Labs' own "Domain Rank Overview" endpoint.
     */
    public function getDomainOverview(string $domain, string $country): array
    {
        $body = $this->post('/v3/dataforseo_labs/google/domain_rank_overview/live', [
            [
                'target' => $domain,
                'location_name' => $country,
            ],
        ]);

        $item = $body['tasks'][0]['result'][0]['items'][0] ?? [];
        $organic = $item['metrics']['organic'] ?? [];
        $paid = $item['metrics']['paid'] ?? [];

        return [
            'organic_traffic' => $organic['etv'] ?? null,
            'organic_keywords' => $organic['count'] ?? null,
            'domain_rank' => $item['metrics']['organic']['pos_1'] ?? null,
            'paid_keywords' => $paid['count'] ?? null,
        ];
    }

    /**
     * Labs' own "Competitors Domain" endpoint.
     */
    public function getOrganicCompetitors(string $domain, string $country, int $limit): array
    {
        $body = $this->post('/v3/dataforseo_labs/google/competitors_domain/live', [
            [
                'target' => $domain,
                'location_name' => $country,
                'limit' => $limit,
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static fn (array $row): array => [
            'domain' => $row['domain'] ?? '',
            'common_keywords' => $row['intersections'] ?? null,
            'competition_level' => $row['avg_position'] ?? null,
        ], $items);
    }

    /**
     * Labs' own "Ranked Keywords" endpoint.
     */
    public function getRankingKeywords(string $domain, string $country, int $limit): array
    {
        $body = $this->post('/v3/dataforseo_labs/google/ranked_keywords/live', [
            [
                'target' => $domain,
                'location_name' => $country,
                'limit' => $limit,
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static function (array $row): array {
            $keywordData = $row['keyword_data'] ?? [];
            $rankedElement = $row['ranked_serp_element']['serp_item'] ?? [];

            return [
                'keyword' => $keywordData['keyword'] ?? '',
                'position' => $rankedElement['rank_absolute'] ?? null,
                'volume' => $keywordData['keyword_info']['search_volume'] ?? null,
                'url' => $rankedElement['url'] ?? '',
            ];
        }, $items);
    }

    /**
     * Labs' own "Relevant Pages" endpoint.
     */
    public function getTopPages(string $domain, string $country, int $limit): array
    {
        $body = $this->post('/v3/dataforseo_labs/google/relevant_pages/live', [
            [
                'target' => $domain,
                'location_name' => $country,
                'limit' => $limit,
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static fn (array $row): array => [
            'url' => $row['page_address'] ?? '',
            'estimated_traffic' => $row['metrics']['organic']['etv'] ?? null,
        ], $items);
    }

    /**
     * Backlinks API's own "Summary" endpoint.
     */
    public function getBacklinksSummary(string $domain): array
    {
        $body = $this->post('/v3/backlinks/summary/live', [
            ['target' => $domain],
        ]);

        $item = $body['tasks'][0]['result'][0] ?? [];
        $total = $item['backlinks'] ?? null;
        $dofollow = $item['backlinks_dofollow'] ?? null;

        return [
            'total_backlinks' => $total,
            'referring_domains' => $item['referring_domains'] ?? null,
            'domain_rank' => $item['rank'] ?? null,
            'dofollow_percent' => ($total !== null && $total > 0 && $dofollow !== null)
                ? round(($dofollow / $total) * 100, 1)
                : null,
        ];
    }

    /**
     * Backlinks API's own "Backlinks" endpoint.
     */
    public function getBacklinksList(string $domain, int $limit): array
    {
        $body = $this->post('/v3/backlinks/backlinks/live', [
            [
                'target' => $domain,
                'limit' => $limit,
                'mode' => 'as_is',
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static fn (array $row): array => [
            'source_url' => $row['url_from'] ?? '',
            'anchor_text' => $row['anchor'] ?? null,
            'link_type' => ($row['dofollow'] ?? true) ? 'dofollow' : 'nofollow',
            'first_seen' => $row['first_seen'] ?? null,
            'source_domain_rank' => $row['rank'] ?? null,
        ], $items);
    }

    /**
     * Backlinks API's own "Referring Domains" endpoint.
     */
    public function getReferringDomains(string $domain, int $limit): array
    {
        $body = $this->post('/v3/backlinks/referring_domains/live', [
            [
                'target' => $domain,
                'limit' => $limit,
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static fn (array $row): array => [
            'domain' => $row['domain'] ?? '',
            'backlinks' => $row['backlinks'] ?? 0,
            'domain_rank' => $row['rank'] ?? null,
        ], $items);
    }

    /**
     * Backlinks API's own "Anchors" endpoint.
     */
    public function getAnchorTextDistribution(string $domain, int $limit): array
    {
        $body = $this->post('/v3/backlinks/anchors/live', [
            [
                'target' => $domain,
                'limit' => $limit,
            ],
        ]);

        $items = $body['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static fn (array $row): array => [
            'anchor_text' => $row['anchor'] ?? '',
            'count' => $row['backlinks'] ?? 0,
        ], $items);
    }

    /**
     * @param  array<int, mixed>  $payload
     * @return array<string, mixed>
     */
    private function post(string $path, array $payload): array
    {
        $response = Http::withBasicAuth($this->provider->credential('login'), $this->provider->credential('password'))
            ->timeout(30)
            ->post("https://api.dataforseo.com{$path}", $payload);

        if (! $response->successful()) {
            throw new \RuntimeException("DataForSEO domain request failed with HTTP {$response->status()}.");
        }

        $body = $response->json();

        if (($body['status_code'] ?? null) !== 20000) {
            throw new \RuntimeException('DataForSEO error: '.($body['status_message'] ?? 'unknown'));
        }

        return $body;
    }
}