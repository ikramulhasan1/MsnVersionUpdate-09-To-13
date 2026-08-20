<?php

declare(strict_types=1);

namespace App\KeywordData\Adapters;

use App\KeywordData\Contracts\ApiProviderAdapterInterface;
use App\KeywordData\Exceptions\CapabilityNotSupportedException;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase O2 (Keyword Data Service Layer) — DataForSEO's own Keywords
 * Data API, specifically its "Google Ads" search-volume endpoint
 * (dataforseo.com/apis/keywords-data-api) — the same underlying Google
 * Ads data App\KeywordData\Adapters\GoogleAdsAdapter itself pulls, just
 * resold through DataForSEO's own infrastructure at a much lower
 * per-call cost and without needing a real Google Ads developer-token
 * approval (see App\Enums\ApiProviderType's own docblock on why the
 * two providers' own credential shapes differ so much for what's
 * ultimately similar underlying data).
 *
 * Only ever instantiated by
 * App\KeywordData\KeywordDataService::adapterFor() for an ApiProvider
 * row whose own type is ApiProviderType::DATAFORSEO_KEYWORDS — this
 * class never queries the database itself, $provider is handed to it
 * fully resolved.
 */
final class DataForSeoKeywordsAdapter implements ApiProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    public function getSearchVolume(array $keywords, string $country, string $language): array
    {
        $data = $this->searchVolumeData($keywords, $country, $language);

        return array_map(
            static fn (?array $row): ?int => $row['search_volume'] ?? null,
            $data,
        );
    }

    public function getCpc(array $keywords, string $country, string $language): array
    {
        $data = $this->searchVolumeData($keywords, $country, $language);

        return array_map(
            static fn (?array $row): ?float => isset($row['cpc']) ? (float) $row['cpc'] : null,
            $data,
        );
    }

    public function getKeywordDifficulty(array $keywords, string $country): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Keywords Data API does not provide keyword difficulty — use the Labs API provider instead.');
    }

    public function getRelatedKeywords(string $seedKeyword, string $country, string $language, int $limit): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Keywords Data API does not provide related keywords — use the Labs API provider instead.');
    }

    public function getSearchIntent(array $keywords, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Keywords Data API does not provide search intent — use the Labs API provider instead.');
    }

    public function getSerpData(string $keyword, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('DataForSEO Keywords Data API does not provide SERP data — use the Labs API provider instead.');
    }

    /**
     * Shared by getSearchVolume()/getCpc() — one real API call answers
     * both, so this fetches once and each public method above just
     * projects the one field it needs out of the same response,
     * rather than making two separate HTTP requests for data that
     * comes back together anyway.
     *
     * @param  array<int, string>  $keywords
     * @return array<string, ?array{search_volume: ?int, cpc: ?float}>
     */
    private function searchVolumeData(array $keywords, string $country, string $language): array
    {
        $response = Http::withBasicAuth($this->provider->credential('login'), $this->provider->credential('password'))
            ->timeout(30)
            ->post('https://api.dataforseo.com/v3/keywords_data/google_ads/search_volume/live', [
                [
                    'keywords' => array_values($keywords),
                    'location_name' => $country,
                    'language_name' => $language,
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException("DataForSEO Keywords Data API request failed with HTTP {$response->status()}.");
        }

        $body = $response->json();

        if (($body['status_code'] ?? null) !== 20000) {
            throw new \RuntimeException('DataForSEO error: '.($body['status_message'] ?? 'unknown'));
        }

        $rows = $body['tasks'][0]['result'] ?? [];

        $result = array_fill_keys($keywords, null);

        foreach ($rows as $row) {
            $keyword = $row['keyword'] ?? null;

            if ($keyword !== null && array_key_exists($keyword, $result)) {
                $result[$keyword] = [
                    'search_volume' => $row['search_volume'] ?? null,
                    'cpc' => $row['cpc'] ?? null,
                ];
            }
        }

        return $result;
    }
}