<?php

declare(strict_types=1);

namespace App\Discovery\Sources;

use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use App\Discovery\Sources\DTO\DiscoveredWebsiteDTO;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Discovery\Taxonomy\YelpCategoryClassifier;
use Throwable;

/**
 * Discovers candidate websites via Yelp's Fusion API
 * (https://docs.developer.yelp.com/reference/v3_business_search) —
 * active ALONGSIDE App\Discovery\Sources\GooglePlacesSource (both are
 * listed in config('discovery.sources') by default), not instead of
 * it: App\Discovery\Ingestion\DiscoveryIngestionService merges
 * whatever each finds before deduplicating, so a business only one of
 * the two would have surfaced still gets found.
 *
 * KNOWN, IMPORTANT LIMITATION — read before relying on result volume:
 * Yelp's Business Search endpoint does NOT return a business's own
 * external website — only its Yelp profile URL, phone, address,
 * categories, and coordinates. This class calls the Business Details
 * endpoint per result (capped at self::MAX_DETAIL_LOOKUPS per
 * discover() call, to bound both latency and Yelp's own daily call
 * quota) and looks for `attributes.business_url` there, which Yelp
 * only populates for SOME businesses (those that have supplied it via
 * their own Yelp for Business account) — not a documented guarantee.
 * A business without that attribute is skipped entirely: this class
 * would rather report FEWER, but genuinely auditable (real domain),
 * candidates than fabricate a website from a Yelp profile URL, which
 * is not the business's own site. This is exactly why
 * GooglePlacesSource exists as a second, independent source rather
 * than this class alone — see that class's own docblock for why its
 * own fill rate on this front is meaningfully higher.
 *
 * Follows App\Audit\Performance\PageSpeedInsightsClient's own
 * established pattern for a third-party API integration in this
 * codebase exactly: a Guzzle ClientInterface injected (reusing the
 * SAME binding AuditServiceProvider already registers, rather than a
 * new HTTP client stack), an API key that's simply absent until
 * configured, and EVERY failure mode — missing key, network error,
 * timeout, non-200 response, unexpected JSON shape — collapsing to a
 * safe empty result rather than a thrown exception. A third-party API
 * having a bad moment (or simply not being configured yet) must never
 * break discovery for every OTHER configured source in the same run —
 * see App\Discovery\Ingestion\DiscoveryIngestionService::discoverAndIngest(),
 * which already isolates one source's own exception from the rest, but
 * this class doesn't rely on that outer safety net either.
 *
 * Every failure/empty-result branch is logged (Log::warning for actual
 * errors, Log::info for a legitimate "found nothing" outcome) — the
 * same debugging aid GooglePlacesSource's own docblock explains the
 * reasoning for: a discover() call returning fewer candidates than
 * expected should be explainable from storage/logs/laravel.log, not
 * guessed at.
 */
final class YelpBusinessSource implements DiscoverySourceInterface
{
    private const string SEARCH_ENDPOINT = 'https://api.yelp.com/v3/businesses/search';

    private const string DETAILS_ENDPOINT = 'https://api.yelp.com/v3/businesses/';

    /**
     * Yelp's own max per search request is 50; kept well under that —
     * every result also costs one further Details API call (see this
     * class's own docblock), so a smaller search page keeps one
     * discover() call's total Yelp API usage (and latency, since detail
     * lookups happen sequentially) predictable.
     */
    private const int SEARCH_LIMIT = 20;

    /**
     * Lowered from an earlier 20 — see
     * App\Discovery\Sources\GooglePlacesSource::MAX_DETAIL_LOOKUPS's own
     * docblock for the real production 504 Gateway Time-out this
     * addresses, and DiscoveryController::discover()'s own docblock for
     * the fastcgi_finish_request() fix that addresses nginx's side of
     * the same incident.
     */
    private const int MAX_DETAIL_LOOKUPS = 10;

    private const int TIMEOUT_SECONDS = 15;

    private readonly ?string $apiKey;

    public function __construct(
        private readonly ClientInterface $httpClient,
        ?string $apiKey = null,
    ) {
        // NOT promoted directly to a readonly property above: a
        // promoted readonly property is already initialized (to null,
        // the parameter's own default) the moment the constructor body
        // starts, and a readonly property can only ever be written to
        // ONCE — a ??= fallback in the body would be a second write and
        // a fatal "Cannot modify readonly property" error. Assigning it
        // explicitly here, from a plain (non-promoted) parameter, is
        // the first and only write.
        $this->apiKey = $apiKey ?? config('services.yelp.api_key');
    }

    public function discover(DiscoveryFilterCriteria $criteria): Collection
    {
        if (empty($this->apiKey)) {
            // Not configured yet — a safe no-op, not an error. See this
            // class's own docblock: every OTHER configured source
            // (config('discovery.sources')) should keep working
            // regardless of whether this one has a key yet.
            return collect();
        }

        $businesses = $this->searchBusinesses($criteria);

        return $businesses
            ->take(self::MAX_DETAIL_LOOKUPS)
            ->map(fn (array $business): ?DiscoveredWebsiteDTO => $this->toDto($business))
            ->filter()
            ->values();
    }

    public function sourceName(): string
    {
        return 'yelp';
    }

    /**
     * @return Collection<int, array<string, mixed>> raw Yelp business objects
     */
    private function searchBusinesses(DiscoveryFilterCriteria $criteria): Collection
    {
        $params = $this->searchParams($criteria);

        try {
            $response = $this->httpClient->request('GET', self::SEARCH_ENDPOINT, [
                'headers' => ['Authorization' => 'Bearer '.$this->apiKey],
                'query' => $params,
                'timeout' => self::TIMEOUT_SECONDS,
                'connect_timeout' => self::TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('YelpBusinessSource: Business Search returned non-200 status', [
                    'params' => $params,
                    'http_status' => $response->getStatusCode(),
                    'body' => (string) $response->getBody(),
                ]);

                return collect();
            }

            $decoded = json_decode((string) $response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);
            $businesses = $decoded['businesses'] ?? [];

            if (! is_array($businesses) || $businesses === []) {
                Log::info('YelpBusinessSource: Business Search returned zero results', [
                    'params' => $params,
                ]);

                return collect();
            }

            return collect($businesses);
        } catch (Throwable $exception) {
            Log::warning('YelpBusinessSource: Business Search request threw', [
                'params' => $params,
                'exception' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * @return array<string, string|int>
     */
    private function searchParams(DiscoveryFilterCriteria $criteria): array
    {
        $params = ['limit' => self::SEARCH_LIMIT];

        $term = $criteria->subNiche ?? $criteria->industry;

        if ($term !== null) {
            $params['term'] = $term;
        }

        // Yelp requires EITHER `location` OR `latitude`+`longitude` — a
        // criteria with no location filter set at all still needs
        // something here, so it falls back to a broad "United States"
        // search rather than the request failing outright. This mirrors
        // WebsiteSearchService's own "an empty criteria still returns a
        // reasonable, broad result" convention rather than introducing a
        // new "no location means no search" rule just for this source.
        $params['location'] = $this->locationString($criteria) ?? 'United States';

        return $params;
    }

    private function locationString(DiscoveryFilterCriteria $criteria): ?string
    {
        $parts = array_filter([$criteria->city, $criteria->region, $criteria->country]);

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * @param  array<string, mixed>  $business
     */
    private function toDto(array $business): ?DiscoveredWebsiteDTO
    {
        $businessId = $business['id'] ?? null;

        if (! is_string($businessId) || $businessId === '') {
            return null;
        }

        $website = $this->lookupBusinessWebsite($businessId);

        if ($website === null) {
            // See this class's own docblock — no real external website
            // means no genuinely auditable candidate, so this business
            // is skipped rather than reported with a fabricated URL.
            return null;
        }

        $host = parse_url($website, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

                /** @var array<string, mixed> $location */
        $location = is_array($business['location'] ?? null) ? $business['location'] : [];

        $classification = (new YelpCategoryClassifier())->classify($this->categoryTitles($business));

        return new DiscoveredWebsiteDTO(
            url: $website,
            domain: $host,
            discoverySource: $this->sourceName(),
            industry: $classification['industry'],
            subNiche: $classification['subNiche'],
            country: is_string($location['country'] ?? null) ? $location['country'] : null,
            city: is_string($location['city'] ?? null) ? $location['city'] : null,
        );
    }

    /**
     * Yelp's Business Search response never includes a business's own
     * website (see this class's own docblock) — only the Business
     * Details endpoint, requested with `attributes=business_url`, has a
     * chance of returning one, and only for businesses that supplied it
     * via their own Yelp for Business account.
     */
    private function lookupBusinessWebsite(string $businessId): ?string
    {
        try {
            $response = $this->httpClient->request('GET', self::DETAILS_ENDPOINT.$businessId, [
                'headers' => ['Authorization' => 'Bearer '.$this->apiKey],
                'query' => ['attributes' => 'business_url'],
                'timeout' => self::TIMEOUT_SECONDS,
                'connect_timeout' => self::TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('YelpBusinessSource: Business Details returned non-200 status', [
                    'business_id' => $businessId,
                    'http_status' => $response->getStatusCode(),
                ]);

                return null;
            }

            $decoded = json_decode((string) $response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);
            $businessUrl = $decoded['attributes']['business_url'] ?? null;

            if (! is_string($businessUrl) || $businessUrl === '') {
                Log::info('YelpBusinessSource: Business Details has no business_url attribute', [
                    'business_id' => $businessId,
                ]);

                return null;
            }

            return $businessUrl;
        } catch (Throwable $exception) {
            Log::warning('YelpBusinessSource: Business Details request threw', [
                'business_id' => $businessId,
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $business
     */
    private function firstCategoryTitle(array $business): ?string
    {
        $categories = $business['categories'] ?? [];

        if (! is_array($categories) || ! isset($categories[0]) || ! is_array($categories[0])) {
            return null;
        }

        $title = $categories[0]['title'] ?? null;

        return is_string($title) ? $title : null;
    }
        private function secondCategoryTitle(array $business): ?string
    {
        $categories = $business['categories'] ?? [];

        if (! is_array($categories) || ! isset($categories[1]) || ! is_array($categories[1])) {
            return null;
        }

        $title = $categories[1]['title'] ?? null;

        return is_string($title) ? $title : null;
    }
        private function categoryTitles(array $business): array
    {
        $categories = $business['categories'] ?? [];

        if (! is_array($categories)) {
            return [];
        }

        $titles = [];

        foreach ($categories as $category) {
            if (! is_array($category)) {
                continue;
            }

            $title = $category['title'] ?? null;

            if (is_string($title) && $title !== '') {
                $titles[] = $title;
            }
        }

        return $titles;
    }
}