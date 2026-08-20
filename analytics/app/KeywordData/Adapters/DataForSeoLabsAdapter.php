<?php

declare(strict_types=1);

namespace App\KeywordData\Adapters;

use App\KeywordData\Contracts\ApiProviderAdapterInterface;
use App\KeywordData\Exceptions\CapabilityNotSupportedException;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase O2 (Keyword Data Service Layer) — DataForSEO's own Labs API
 * (dataforseo.com/apis/dataforseo-labs-api), the SEO-intelligence layer
 * built on top of their own indexed SERP/keyword database — see
 * App\Enums\ApiProviderType's own docblock for why this is a SEPARATE
 * provider from DataForSeoKeywordsAdapter despite sharing the same
 * account credential shape: the two are billed and rate-limited as
 * distinct DataForSEO products, even though one account's own
 * login/password pair authenticates both.
 */
final class DataForSeoLabsAdapter implements ApiProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    public function getSearchVolume(array $keywords, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Labs API does not provide search volume directly — use a Keywords Data provider instead.');
    }

    public function getCpc(array $keywords, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Labs API does not provide CPC directly — use a Keywords Data provider instead.');
    }

    /**
     * Labs' own "Bulk Keyword Difficulty" endpoint — up to 1,000
     * keywords per request, a single proprietary 0-100 score per
     * keyword reflecting how hard ranking in the top-10 organic
     * results currently is.
     */
    public function getKeywordDifficulty(array $keywords, string $country): array
    {
        $response = $this->post('/v3/dataforseo_labs/google/bulk_keyword_difficulty/live', [
            [
                'keywords' => array_values($keywords),
                'location_name' => $country,
            ],
        ]);

        $rows = $response['tasks'][0]['result'][0]['items'] ?? [];

        $result = array_fill_keys($keywords, null);

        foreach ($rows as $row) {
            $keyword = $row['keyword'] ?? null;

            if ($keyword !== null && array_key_exists($keyword, $result)) {
                $result[$keyword] = $row['keyword_difficulty'] ?? null;
            }
        }

        return $result;
    }

    /**
     * Labs' own "Keyword Suggestions" endpoint (Google Autocomplete-
     * derived long-tail ideas) — chosen over "Related Keywords" (a
     * narrower "Searches related to" list) as the default for THIS
     * method specifically because it typically returns a far larger,
     * more useful set for Phase O4's own Keyword Magic Tool, which is
     * this method's primary caller.
     */
    public function getRelatedKeywords(string $seedKeyword, string $country, string $language, int $limit): array
    {
        $response = $this->post('/v3/dataforseo_labs/google/keyword_suggestions/live', [
            [
                'keyword' => $seedKeyword,
                'location_name' => $country,
                'language_name' => $language,
                'limit' => $limit,
                'include_seed_keyword' => false,
            ],
        ]);

        $rows = $response['tasks'][0]['result'][0]['items'] ?? [];

        return array_map(static function (array $row): array {
            $info = $row['keyword_info'] ?? [];

            return [
                'keyword' => $row['keyword'] ?? '',
                'volume' => $info['search_volume'] ?? null,
                'cpc' => isset($info['cpc']) ? (float) $info['cpc'] : null,
                'difficulty' => $row['keyword_properties']['keyword_difficulty'] ?? null,
            ];
        }, $rows);
    }

    /**
     * Labs' own "Search Intent" endpoint — classifies each keyword
     * into DataForSEO's own four-category taxonomy, mapped 1:1 onto
     * this app's own vocabulary
     * (App\KeywordData\Contracts\ApiProviderAdapterInterface's own
     * docblock lists the four accepted return values).
     */
    public function getSearchIntent(array $keywords, string $country, string $language): array
    {
        $response = $this->post('/v3/dataforseo_labs/google/search_intent/live', [
            [
                'keywords' => array_values($keywords),
                'language_name' => $language,
            ],
        ]);

        $rows = $response['tasks'][0]['result'][0]['items'] ?? [];

        $result = array_fill_keys($keywords, null);

        foreach ($rows as $row) {
            $keyword = $row['keyword'] ?? null;
            $intent = $row['keyword_intent']['label'] ?? null;

            if ($keyword !== null && array_key_exists($keyword, $result) && $intent !== null) {
                $result[$keyword] = strtolower((string) $intent);
            }
        }

        return $result;
    }

    /**
     * Labs' own "SERP" summary endpoint feeds the feature-list half of
     * this method; DataForSEO's own real-time organic SERP endpoint
     * feeds the top-10-results/People-Also-Ask half — DataForSEO has
     * no single endpoint returning all three at once, so this makes
     * two calls and sorts them into this adapter's own three-part
     * return shape rather than handing back either endpoint's own raw
     * structure.
     */
    public function getSerpData(string $keyword, string $country, string $language): array
    {
        $serpResponse = $this->post('/v3/serp/google/organic/live/advanced', [
            [
                'keyword' => $keyword,
                'location_name' => $country,
                'language_name' => $language,
                'depth' => 10,
            ],
        ]);

        $serpItems = $serpResponse['tasks'][0]['result'][0]['items'] ?? [];
        $features = [];
        $topResults = [];
        $questions = [];

        foreach ($serpItems as $item) {
            $type = $item['type'] ?? null;

            if ($type === 'organic' && count($topResults) < 10) {
                $topResults[] = [
                    'url' => $item['url'] ?? '',
                    'domain' => $item['domain'] ?? '',
                    'title' => $item['title'] ?? null,
                ];

                continue;
            }

            if ($type !== null && $type !== 'organic' && ! in_array($type, $features, true)) {
                $features[] = $type;
            }

            if ($type === 'people_also_ask') {
                foreach ($item['items'] ?? [] as $paaItem) {
                    if (isset($paaItem['title'])) {
                        $questions[] = $paaItem['title'];
                    }
                }
            }
        }

        return [
            'features' => $features,
            'top_results' => $topResults,
            'questions' => $questions,
        ];
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
            throw new \RuntimeException("DataForSEO Labs API request failed with HTTP {$response->status()}.");
        }

        $body = $response->json();

        if (($body['status_code'] ?? null) !== 20000) {
            throw new \RuntimeException('DataForSEO Labs error: '.($body['status_message'] ?? 'unknown'));
        }

        return $body;
    }
}