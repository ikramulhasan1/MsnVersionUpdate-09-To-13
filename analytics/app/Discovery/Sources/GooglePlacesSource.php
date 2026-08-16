<?php

declare(strict_types=1);

namespace App\Discovery\Sources;

use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use App\Discovery\Sources\DTO\DiscoveredWebsiteDTO;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Discovers candidate websites via Google's (legacy) Places API — Text
 * Search + Place Details
 * (https://developers.google.com/maps/documentation/places/web-service/search-text,
 * https://developers.google.com/maps/documentation/places/web-service/details)
 * — the Website Discovery module's chosen external data source (every
 * prior phase built the search/filter/display/scoring/export machinery
 * around whatever was already in discovered_websites; nothing before
 * this class ever put real business data INTO that table). See
 * App\Discovery\Ingestion\DiscoveryIngestionService for what turns this
 * class's own DiscoveredWebsiteDTO output into actual, persisted,
 * deduplicated DiscoveredWebsite rows — this class only ever discovers
 * and reports candidates, exactly like DiscoverySourceInterface's own
 * contract requires.
 *
 * WHY THIS API SPECIFICALLY: Google Places' Place Details response has
 * an actual `website` field that Google itself populates directly from
 * what a business (or Google's own crawl of it) has associated with
 * that listing — a meaningfully high fill rate compared to most
 * comparable directory APIs, most of which either don't expose a
 * business's own external site at all or only do so inconsistently.
 * Neither Google's search endpoint nor its own listing summary
 * includes a website field directly, which is why this class follows a
 * two-call shape: a Text Search call, then one Place Details call per
 * result for the one field the search response doesn't include.
 *
 * COST NOTE: Google Places API is NOT free — Text Search and Place
 * Details (particularly Contact Data fields like `website`) are billed
 * per request beyond Google's own monthly free credit. See
 * https://developers.google.com/maps/billing-and-pricing/pricing for
 * current rates before relying on this source at any real volume;
 * self::MAX_DETAIL_LOOKUPS exists specifically to bound that cost per
 * discover() call.
 *
 * Only the FIRST page of Text Search results (up to 20, Google's own
 * fixed page size) is used — Google's `next_page_token` pagination
 * requires a short server-side delay before the next page becomes
 * valid, which this class deliberately does not implement (a
 * synchronous, no-queue-worker request — see
 * DiscoveryController::discover()'s own docblock — is not the place to
 * add an artificial sleep()); one discover() call is one page.
 *
 * Follows the exact same defensive shape
 * App\Audit\Performance\PageSpeedInsightsClient already establishes
 * for a third-party API integration in this codebase: a Guzzle
 * ClientInterface injected (reusing AuditServiceProvider's own
 * binding), an API key that's simply absent until configured, and
 * every failure mode — missing key, network error, timeout, non-OK
 * response, unexpected JSON shape — collapsing to a safe empty result
 * rather than a thrown exception.
 */
final class GooglePlacesSource implements DiscoverySourceInterface
{
    private const string TEXT_SEARCH_ENDPOINT = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

    private const string DETAILS_ENDPOINT = 'https://maps.googleapis.com/maps/api/place/details/json';

    /**
     * Only Basic Data + the one Contact Data field this source actually
     * needs (`website`) is requested — Google Places bills Contact Data
     * fields separately from (more expensive than) Basic Data, so
     * asking for anything beyond what toDto() actually reads would be
     * paying for data this source throws away.
     */
    private const string DETAILS_FIELDS = 'name,website,address_component';

    private const int MAX_DETAIL_LOOKUPS = 20;

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
        $this->apiKey = $apiKey ?? config('services.google_places.api_key');
    }

    public function discover(DiscoveryFilterCriteria $criteria): Collection
    {
        if (empty($this->apiKey)) {
            // Not configured yet — a safe no-op, not an error: this
            // module's other discovery machinery (InternalCrawlSource,
            // and any other source a future phase adds to
            // config('discovery.sources')) should keep working
            // regardless of whether this one has a key yet.
            return collect();
        }

        $places = $this->searchPlaces($criteria);

        return $places
            ->take(self::MAX_DETAIL_LOOKUPS)
            ->map(fn (array $place): ?DiscoveredWebsiteDTO => $this->toDto($place, $criteria))
            ->filter()
            ->values();
    }

    public function sourceName(): string
    {
        return 'google_places';
    }

    /**
     * @return Collection<int, array<string, mixed>> raw Google Places "results" entries
     */
    private function searchPlaces(DiscoveryFilterCriteria $criteria): Collection
    {
        try {
            $response = $this->httpClient->request('GET', self::TEXT_SEARCH_ENDPOINT, [
                'query' => [
                    'query' => $this->searchQuery($criteria),
                    'key' => $this->apiKey,
                ],
                'timeout' => self::TIMEOUT_SECONDS,
                'connect_timeout' => self::TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('GooglePlacesSource: Text Search returned non-200 status', [
                    'http_status' => $response->getStatusCode(),
                    'body' => (string) $response->getBody(),
                ]);

                return collect();
            }

            $decoded = json_decode((string) $response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);

            // Google's own status field ('OK', 'ZERO_RESULTS', 'OVER_QUERY_LIMIT',
            // 'REQUEST_DENIED', ...) — only 'OK' has a `results` array worth
            // reading; every other value (including a legitimate
            // "nothing matched" ZERO_RESULTS) collapses to the same empty
            // Collection.
            if (($decoded['status'] ?? null) !== 'OK') {
                Log::warning('GooglePlacesSource: Text Search status was not OK', [
                    'status' => $decoded['status'] ?? null,
                    'error_message' => $decoded['error_message'] ?? null,
                ]);

                return collect();
            }

            $results = $decoded['results'] ?? [];

            return is_array($results) ? collect($results) : collect();
        } catch (Throwable $exception) {
            Log::warning('GooglePlacesSource: Text Search request threw', [
                'exception' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Google's Text Search takes one free-text `query` string, not
     * separate category/location parameters — "<term> in <location>"
     * is Google's own documented pattern for this endpoint (e.g.
     * "restaurants in Chicago, IL").
     */
    private function searchQuery(DiscoveryFilterCriteria $criteria): string
    {
        $term = $criteria->subNiche ?? $criteria->industry ?? 'business';
        $location = $this->locationString($criteria);

        return $location !== null ? sprintf('%s in %s', $term, $location) : $term;
    }

    private function locationString(DiscoveryFilterCriteria $criteria): ?string
    {
        $parts = array_filter([$criteria->city, $criteria->region, $criteria->country]);

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * @param  array<string, mixed>  $place
     */
    private function toDto(array $place, DiscoveryFilterCriteria $criteria): ?DiscoveredWebsiteDTO
    {
        $placeId = $place['place_id'] ?? null;

        if (! is_string($placeId) || $placeId === '') {
            return null;
        }

        $details = $this->lookupPlaceDetails($placeId);

        if ($details === null || $details['website'] === null) {
            // No real external website for this place — Google
            // doesn't guarantee every listing has one, and this source
            // would rather skip a candidate than report Google's own
            // Maps listing URL as if it were the business's own site.
            return null;
        }

        $host = parse_url($details['website'], PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        return new DiscoveredWebsiteDTO(
            url: $details['website'],
            domain: $host,
            discoverySource: $this->sourceName(),
            industry: $criteria->subNiche ?? $criteria->industry,
            country: $details['country'] ?? null,
            city: $details['city'] ?? null,
        );
    }

    /**
     * @return ?array{website: ?string, city: ?string, country: ?string}
     */
    private function lookupPlaceDetails(string $placeId): ?array
    {
        try {
            $response = $this->httpClient->request('GET', self::DETAILS_ENDPOINT, [
                'query' => [
                    'place_id' => $placeId,
                    'fields' => self::DETAILS_FIELDS,
                    'key' => $this->apiKey,
                ],
                'timeout' => self::TIMEOUT_SECONDS,
                'connect_timeout' => self::TIMEOUT_SECONDS,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('GooglePlacesSource: Place Details returned non-200 status', [
                    'http_status' => $response->getStatusCode(),
                    'place_id' => $placeId,
                ]);

                return null;
            }

            $decoded = json_decode((string) $response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);

            if (($decoded['status'] ?? null) !== 'OK') {
                Log::warning('GooglePlacesSource: Place Details status was not OK', [
                    'place_id' => $placeId,
                    'status' => $decoded['status'] ?? null,
                    'error_message' => $decoded['error_message'] ?? null,
                ]);

                return null;
            }

            $result = $decoded['result'] ?? [];

            if (! is_array($result)) {
                return null;
            }

            $website = $result['website'] ?? null;
            $addressComponents = $result['address_components'] ?? [];

            return [
                'website' => is_string($website) && $website !== '' ? $website : null,
                'city' => $this->addressComponent($addressComponents, 'locality'),
                'country' => $this->addressComponent($addressComponents, 'country'),
            ];
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Google's `address_components` is a flat array of
     * {long_name, short_name, types: [...]}, each entry potentially
     * carrying several `types` (e.g. a country entry has both
     * 'country' and 'political') — the first entry whose `types`
     * contains $type is used; its `long_name` (e.g. "United States",
     * not the short_name "US") is returned to match the free-form,
     * human-readable convention this module's own country/city columns
     * already use elsewhere (see database/migrations/2026_08_14_000000_create_discovered_websites_table.php's
     * own docblock on why `country` is a free-form string, not a forced
     * ISO code).
     *
     * @param  array<int, mixed>  $addressComponents
     */
    private function addressComponent(array $addressComponents, string $type): ?string
    {
        foreach ($addressComponents as $component) {
            if (! is_array($component)) {
                continue;
            }

            $types = $component['types'] ?? [];

            if (is_array($types) && in_array($type, $types, true)) {
                $longName = $component['long_name'] ?? null;

                return is_string($longName) && $longName !== '' ? $longName : null;
            }
        }

        return null;
    }
}
