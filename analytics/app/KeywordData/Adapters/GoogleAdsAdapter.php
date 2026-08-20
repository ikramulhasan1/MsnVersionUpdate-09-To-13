<?php

declare(strict_types=1);

namespace App\KeywordData\Adapters;

use App\KeywordData\Contracts\ApiProviderAdapterInterface;
use App\KeywordData\Exceptions\CapabilityNotSupportedException;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase O2 (Keyword Data Service Layer) — Google Ads' own real REST
 * API (googleads.googleapis.com), authenticated via a stored OAuth2
 * refresh token exchanged for a short-lived access token on EVERY
 * call (see exchangeAccessToken() below — Google's own access tokens
 * expire in about an hour, so this can't cache the token itself
 * across requests the way DataForSEO's own Basic Auth needs no
 * per-call exchange at all).
 *
 * Only search volume and CPC — see
 * App\Enums\ApiProviderType::possibleCapabilities()'s own docblock for
 * why Google Ads has nothing to offer for difficulty/related-keywords/
 * intent/SERP data at all; those four methods below always throw.
 */
final class GoogleAdsAdapter implements ApiProviderAdapterInterface
{
    public function __construct(
        private readonly ApiProvider $provider,
    ) {
    }

    public function getSearchVolume(array $keywords, string $country, string $language): array
    {
        $data = $this->keywordIdeasData($keywords, $country, $language);

        return array_map(
            static fn (?array $row): ?int => $row['avg_monthly_searches'] ?? null,
            $data,
        );
    }

    public function getCpc(array $keywords, string $country, string $language): array
    {
        $data = $this->keywordIdeasData($keywords, $country, $language);

        return array_map(
            static fn (?array $row): ?float => $row['avg_cpc_micros'] !== null
                ? $row['avg_cpc_micros'] / 1_000_000
                : null,
            $data,
        );
    }

    public function getKeywordDifficulty(array $keywords, string $country): array
    {
        throw new CapabilityNotSupportedException('Google Ads API does not provide keyword difficulty — use a DataForSEO Labs provider instead.');
    }

    public function getRelatedKeywords(string $seedKeyword, string $country, string $language, int $limit): array
    {
        throw new CapabilityNotSupportedException('Google Ads API does not provide related-keyword discovery — use a DataForSEO Labs provider instead.');
    }

    public function getSearchIntent(array $keywords, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('Google Ads API does not provide search intent — use a DataForSEO Labs provider instead.');
    }

    public function getSerpData(string $keyword, string $country, string $language): array
    {
        throw new CapabilityNotSupportedException('Google Ads API does not provide SERP data — use a DataForSEO Labs provider instead.');
    }

    /**
     * Google Ads' own Keyword Plan Idea Service — the closest
     * equivalent to DataForSEO's own search-volume endpoint. Returns
     * BUCKETED volume ranges rather than exact numbers unless the
     * underlying Ads account has real, recent ad spend (a real,
     * documented Google Ads API limitation — see this app's own
     * deploy notes on why DataForSEO was recommended as the primary
     * volume source, with Google Ads as a secondary/fallback provider
     * rather than the primary one).
     *
     * @param  array<int, string>  $keywords
     * @return array<string, ?array{avg_monthly_searches: ?int, avg_cpc_micros: ?int}>
     */
    private function keywordIdeasData(array $keywords, string $country, string $language): array
    {
        $accessToken = $this->exchangeAccessToken();
        $customerId = preg_replace('/\D/', '', (string) $this->provider->credential('customer_id'));

        $response = Http::withToken($accessToken)
            ->withHeaders(['developer-token' => $this->provider->credential('developer_token')])
            ->timeout(30)
            ->post("https://googleads.googleapis.com/v18/customers/{$customerId}:generateKeywordIdeas", [
                'keywordSeed' => ['keywords' => array_values($keywords)],
                'geoTargetConstants' => [$this->geoTargetConstant($country)],
                'language' => $this->languageConstant($language),
                'keywordPlanNetwork' => 'GOOGLE_SEARCH',
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?? "HTTP {$response->status()}";

            throw new \RuntimeException("Google Ads API error: {$message}");
        }

        $rows = $response->json('results') ?? [];

        $result = array_fill_keys($keywords, null);

        foreach ($rows as $row) {
            $keyword = $row['text'] ?? null;
            $metrics = $row['keywordIdeaMetrics'] ?? null;

            if ($keyword !== null && array_key_exists($keyword, $result) && $metrics !== null) {
                $result[$keyword] = [
                    'avg_monthly_searches' => $metrics['avgMonthlySearches'] ?? null,
                    'avg_cpc_micros' => $metrics['averageCpcMicros'] ?? null,
                ];
            }
        }

        return $result;
    }

    /**
     * Exchanges the stored, long-lived refresh_token for a fresh,
     * short-lived access token — every call, since Google's own access
     * tokens expire in roughly an hour and this class has no request-
     * scoped or persistent place to cache one safely across separate
     * HTTP requests to this app.
     */
    private function exchangeAccessToken(): string
    {
        $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->provider->credential('client_id'),
            'client_secret' => $this->provider->credential('client_secret'),
            'refresh_token' => $this->provider->credential('refresh_token'),
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Google Ads OAuth token exchange failed — check the stored client ID/secret/refresh token.');
        }

        $accessToken = $response->json('access_token');

        if (! is_string($accessToken)) {
            throw new \RuntimeException('Google Ads OAuth exchange returned no access token.');
        }

        return $accessToken;
    }

    /**
     * A small, deliberately-incomplete lookup — Google Ads identifies
     * countries/languages by their own numeric constant IDs, not ISO
     * codes, and the full official list has hundreds of entries. This
     * covers the handful most likely to matter for this app's own
     * early users; extend as real usage reveals which country/language
     * combinations are actually requested.
     */
    private function geoTargetConstant(string $country): string
    {
        $map = [
            'United States' => 'geoTargetConstants/2840',
            'Bangladesh' => 'geoTargetConstants/2050',
            'United Kingdom' => 'geoTargetConstants/2826',
            'India' => 'geoTargetConstants/2356',
            'Canada' => 'geoTargetConstants/2124',
            'Australia' => 'geoTargetConstants/2036',
        ];

        return $map[$country] ?? $map['United States'];
    }

    private function languageConstant(string $language): string
    {
        $map = [
            'English' => 'languageConstants/1000',
            'Bengali' => 'languageConstants/1056',
        ];

        return $map[$language] ?? $map['English'];
    }
}