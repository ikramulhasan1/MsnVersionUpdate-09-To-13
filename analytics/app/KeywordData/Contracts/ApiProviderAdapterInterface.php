<?php

declare(strict_types=1);

namespace App\KeywordData\Contracts;

use App\KeywordData\Exceptions\CapabilityNotSupportedException;

/**
 * Phase O2 (Keyword Data Service Layer) — the ONE shape every real
 * provider adapter (App\KeywordData\Adapters\DataForSeoKeywordsAdapter/
 * DataForSeoLabsAdapter/GoogleAdsAdapter) implements. App\KeywordData\KeywordDataService
 * is the only caller of any of these methods anywhere in this app —
 * no controller/feature ever instantiates an adapter or calls a
 * provider's own API directly.
 *
 * A method a given adapter's own provider TYPE genuinely can't answer
 * (e.g. GoogleAdsAdapter::getKeywordDifficulty() — Google Ads has no
 * such metric at all, see App\Enums\ApiProviderType::possibleCapabilities()'s
 * own docblock) throws CapabilityNotSupportedException rather than
 * returning an empty/fabricated result — KeywordDataService's own
 * fallback logic only ever calls a method on an adapter whose
 * underlying ApiProvider row has that capability checked in the first
 * place (Phase O1's own $capabilities column), so in practice this
 * exception should never actually fire from a correctly-configured
 * provider — it exists as a hard safety net, not a normal code path.
 */
interface ApiProviderAdapterInterface
{
    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?int> keyword => monthly search volume, or
     *         null for a keyword this provider had no data for
     *         (never omitted from the array entirely — every requested
     *         keyword gets a key, even if its own value is null)
     *
     * @throws CapabilityNotSupportedException
     */
    public function getSearchVolume(array $keywords, string $country, string $language): array;

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?float> keyword => CPC in USD
     *
     * @throws CapabilityNotSupportedException
     */
    public function getCpc(array $keywords, string $country, string $language): array;

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?int> keyword => difficulty score, 0-100
     *
     * @throws CapabilityNotSupportedException
     */
    public function getKeywordDifficulty(array $keywords, string $country): array;

    /**
     * @return array<int, array{keyword: string, volume: ?int, cpc: ?float, difficulty: ?int}>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getRelatedKeywords(string $seedKeyword, string $country, string $language, int $limit): array;

    /**
     * @param  array<int, string>  $keywords
     * @return array<string, ?string> keyword => one of
     *         'informational'|'navigational'|'commercial'|'transactional'
     *
     * @throws CapabilityNotSupportedException
     */
    public function getSearchIntent(array $keywords, string $country, string $language): array;

    /**
     * @return array{features: array<int, string>, top_results: array<int, array{url: string, domain: string, title: ?string}>, questions: array<int, string>}
     *
     * @throws CapabilityNotSupportedException
     */
    public function getSerpData(string $keyword, string $country, string $language): array;

    /**
     * Phase O3 (Keyword Research page) — added after this interface's
     * own initial Phase O2 version, once that page's own trend graph
     * turned out to need this. See
     * App\Enums\KeywordCapability::VOLUME_TREND's own docblock.
     *
     * @return array<int, array{month: string, volume: ?int}> exactly
     *         12 entries, oldest first, month formatted 'YYYY-MM'
     *
     * @throws CapabilityNotSupportedException
     */
    public function getSearchVolumeTrend(string $keyword, string $country, string $language): array;

    /**
     * Phase O3 — 0-100, DataForSEO's own normalized measure of paid-ad
     * competition for this keyword (NOT the same thing as organic
     * Keyword Difficulty — a keyword can have low ad competition but
     * high organic difficulty, or vice versa).
     *
     * @param  array<int, string>  $keywords
     * @return array<string, ?int>
     *
     * @throws CapabilityNotSupportedException
     */
    public function getCompetitiveDensity(array $keywords, string $country, string $language): array;
}