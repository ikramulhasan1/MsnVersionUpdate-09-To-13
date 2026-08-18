<?php

declare(strict_types=1);

namespace App\Audit\Fetching;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use App\Audit\Fetching\Contracts\HtmlParserInterface;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\DiscoveredResource;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\ParsedHtml;

final class WebsiteFetcherService implements WebsiteFetcherServiceInterface
{
    /**
     * Cap on the timeout used for the small well-known probes (robots.txt,
     * sitemap, manifest, feeds) so one slow probe can't eat the whole
     * request budget meant for the main page.
     */
    private const int WELL_KNOWN_TIMEOUT_CAP = 8;

    /**
     * Well-known resources (robots.txt, sitemap, manifest, feeds) are
     * properties of the *origin*, not of any one page — but fetch() used
     * to re-probe all of them on every single page. On a 25-page crawl
     * that meant up to ~150 extra sequential HTTP round-trips for data
     * that's identical on every page. Keyed by origin, computed once, and
     * reused for the rest of the pages fetched through this instance
     * (the crawler holds one WebsiteFetcherService for an entire crawl()
     * call, so this cache lives exactly as long as it needs to).
     *
     * @var array<string, array{0: DiscoveredResource, 1: DiscoveredResource, 2: DiscoveredResource, 3: array<int, DiscoveredResource>}>
     */
    private array $wellKnownCache = [];

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly HtmlParserInterface $htmlParser,
        private readonly int $timeoutSeconds = 15,
        private readonly string $userAgent = 'WebsiteAuditBot/1.0',
    ) {
    }

    public function fetch(string $url): FetchResult
    {
        $fetchedAt = (new \DateTimeImmutable())->format(DATE_ATOM);
        $start = microtime(true);

        try {
            $response = $this->httpClient->request('GET', $url, $this->pageRequestOptions());
        } catch (GuzzleException $exception) {
            return $this->failureResult($url, $fetchedAt, [
                'Unable to fetch the page: ' . $exception->getMessage(),
            ]);
        }

        $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

        return $this->buildResult($url, $response, $fetchedAt, $responseTimeMs);
    }

    /**
     * Concurrent version of fetch() for a batch of URLs — used by the
     * crawler so a wave of pages is fetched in roughly the time of the
     * single slowest one instead of the sum of all of them. Falls back to
     * a per-URL "unable to fetch" FetchResult (never throws) for any
     * request that fails or rejects, exactly like fetch() does.
     *
     * @param array<int, string> $urls
     * @return array<string, FetchResult> keyed by the original URL, same
     *         order as $urls
     */
    public function fetchMany(array $urls): array
    {
        if ($urls === []) {
            return [];
        }

        $fetchedAt = (new \DateTimeImmutable())->format(DATE_ATOM);
        $options = $this->pageRequestOptions();

        /** @var array<int, PromiseInterface|null> $promises */
        $promises = [];
        $starts = [];

        foreach (array_values($urls) as $i => $url) {
            $starts[$i] = microtime(true);

            try {
                $promises[$i] = $this->httpClient->requestAsync('GET', $url, $options);
            } catch (\Throwable) {
                $promises[$i] = null;
            }
        }

        $settled = Utils::settle(array_filter($promises))->wait();

        $results = [];

        foreach (array_values($urls) as $i => $url) {
            $outcome = $settled[$i] ?? null;

            if ($outcome === null || $outcome['state'] !== PromiseInterface::FULFILLED) {
                $reason = $outcome['reason'] ?? null;
                $message = $reason instanceof \Throwable ? $reason->getMessage() : 'the request could not be completed';

                // PRODUCTION INCIDENT — this branch used to fail
                // silently: fetchMany() itself never logged anything,
                // so when many URLs in the same call all failed at
                // once (a real bulk-audit batch where every one of 60
                // real, distinct external websites came back
                // unfetchable), there was no way to tell FROM THE LOGS
                // whether that was genuinely 60 unrelated network
                // failures or one single, shared underlying cause
                // (e.g. a DNS/outbound-connectivity/TLS issue specific
                // to how this queue worker's own process reaches the
                // network) — the only visible symptom was a much later,
                // unrelated-looking TypeError several layers downstream
                // (see App\Audit\Jobs\AnalyzeChunkJob::fetchPagesForMultiPageAnalysis()'s
                // own docblock for that specific crash and its fix).
                // Logging the REAL Guzzle-level reason here, per URL,
                // is what actually lets that distinction be made from
                // storage/logs/laravel.log after the fact.
                Log::warning('WebsiteFetcherService::fetchMany() request failed', [
                    'url' => $url,
                    'reason' => $message,
                ]);

                $results[$url] = $this->failureResult($url, $fetchedAt, [
                    'Unable to fetch the page: ' . $message,
                ]);
                continue;
            }

            $responseTimeMs = (int) round((microtime(true) - $starts[$i]) * 1000);
            $results[$url] = $this->buildResult($url, $outcome['value'], $fetchedAt, $responseTimeMs);
        }

        return $results;
    }

    /**
     * @return array<string, mixed>
     */
    private function pageRequestOptions(): array
    {
        return [
            'timeout' => $this->timeoutSeconds,
            'connect_timeout' => $this->timeoutSeconds,
            'http_errors' => false,
            'headers' => ['User-Agent' => $this->userAgent],
            'allow_redirects' => ['max' => 5, 'track_redirects' => true],
        ];
    }

    private function buildResult(string $url, ResponseInterface $response, string $fetchedAt, ?int $responseTimeMs): FetchResult
    {
        $html = (string) $response->getBody();

        $redirectChain = array_filter(array_map(
            'trim',
            explode(',', $response->getHeaderLine('X-Guzzle-Redirect-History'))
        ));
        $finalUrl = $redirectChain !== [] ? end($redirectChain) : $url;

        $origin = UrlResolver::originOf($finalUrl) ?? UrlResolver::originOf($url);
        $parsed = $this->htmlParser->parse($html, $finalUrl);

        [$robotsTxt, $sitemap, $manifest, $rssFeeds] = $this->resolveWellKnownResources($origin, $parsed);

        return new FetchResult(
            url: $url,
            success: true,
            finalUrl: $finalUrl,
            statusCode: $response->getStatusCode(),
            headers: $this->flattenHeaders($response),
            html: $html,
            meta: $parsed->meta,
            cssLinks: $parsed->cssLinks,
            jsLinks: $parsed->jsLinks,
            images: $parsed->images,
            fonts: $parsed->fonts,
            anchors: $parsed->anchors,
            headings: $parsed->headings,
            schema: $parsed->schema,
            wordCount: $parsed->wordCount,
            robotsTxt: $robotsTxt,
            sitemap: $sitemap,
            rssFeeds: $rssFeeds,
            manifest: $manifest,
            redirectChain: $redirectChain,
            responseTimeMs: $responseTimeMs,
            errors: [],
            fetchedAt: $fetchedAt,
            mailtoLinks: $parsed->mailtoLinks,
            telLinks: $parsed->telLinks,
        );
    }

    /**
     * Resolves robots.txt / sitemap / manifest / rss feeds for $parsed's
     * origin, from cache when this origin has already been probed once
     * this crawl/fetchMany batch. Same sequential probe order as before
     * (robots, then sitemap, then manifest, then feeds) — only the
     * *repetition* across pages is new, not the request pattern itself.
     *
     * @return array{0: DiscoveredResource, 1: DiscoveredResource, 2: DiscoveredResource, 3: array<int, DiscoveredResource>}
     */
    private function resolveWellKnownResources(?string $origin, ParsedHtml $parsed): array
    {
        if ($origin === null) {
            return [DiscoveredResource::notFound(), DiscoveredResource::notFound(), DiscoveredResource::notFound(), [DiscoveredResource::notFound()]];
        }

        if (isset($this->wellKnownCache[$origin])) {
            return $this->wellKnownCache[$origin];
        }

        [$robotsTxt, $robotsBody] = $this->probe($origin . '/robots.txt', 'well_known');
        $sitemap = $this->detectSitemap($origin, $robotsBody);
        $manifest = $this->detectManifest($origin, $parsed->manifestUrl);
        $rssFeeds = $this->detectFeeds($origin, $parsed->feedUrls);

        return $this->wellKnownCache[$origin] = [$robotsTxt, $sitemap, $manifest, $rssFeeds];
    }

    private function detectSitemap(?string $origin, ?string $robotsBody): DiscoveredResource
    {
        $fromRobots = $robotsBody !== null ? $this->extractSitemapFromRobots($robotsBody) : null;

        if ($fromRobots !== null) {
            [$sitemap] = $this->probe($fromRobots, 'robots_txt');

            return $sitemap;
        }

        if ($origin === null) {
            return DiscoveredResource::notFound();
        }

        [$sitemap] = $this->probe($origin . '/sitemap.xml', 'well_known');

        return $sitemap;
    }

    private function detectManifest(?string $origin, ?string $manifestUrlFromHtml): DiscoveredResource
    {
        if ($manifestUrlFromHtml !== null) {
            [$manifest] = $this->probe($manifestUrlFromHtml, 'html_link');

            return $manifest;
        }

        if ($origin === null) {
            return DiscoveredResource::notFound();
        }

        return $this->firstExisting([
            $origin . '/manifest.json',
            $origin . '/site.webmanifest',
        ], 'well_known');
    }

    /**
     * @param array<int, string> $feedUrlsFromHtml
     * @return array<int, DiscoveredResource>
     */
    private function detectFeeds(?string $origin, array $feedUrlsFromHtml): array
    {
        if ($feedUrlsFromHtml !== []) {
            return array_map(
                fn (string $feedUrl): DiscoveredResource => $this->probe($feedUrl, 'html_link')[0],
                $feedUrlsFromHtml,
            );
        }

        if ($origin === null) {
            return [DiscoveredResource::notFound()];
        }

        return [$this->firstExisting([
            $origin . '/feed',
            $origin . '/feed/',
            $origin . '/rss.xml',
            $origin . '/rss',
        ], 'well_known')];
    }

    private function firstExisting(array $candidates, string $source): DiscoveredResource
    {
        foreach ($candidates as $candidate) {
            [$resource] = $this->probe($candidate, $source);

            if ($resource->exists) {
                return $resource;
            }
        }

        return DiscoveredResource::notFound($source);
    }

    private function extractSitemapFromRobots(string $robotsBody): ?string
    {
        foreach (preg_split('/\R/', $robotsBody) ?: [] as $line) {
            if (preg_match('/^\s*sitemap\s*:\s*(\S+)/i', $line, $matches) === 1) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * GET a well-known resource and report whether it exists. Never throws
     * — network failures here just mean "not detected", they don't fail
     * the overall fetch.
     *
     * @return array{0: DiscoveredResource, 1: ?string} the resource, plus its
     *         body when it exists (needed to scan robots.txt for a Sitemap: line)
     */
    private function probe(string $url, string $source): array
    {
        $timeout = min($this->timeoutSeconds, self::WELL_KNOWN_TIMEOUT_CAP);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => $timeout,
                'connect_timeout' => $timeout,
                'http_errors' => false,
                'headers' => ['User-Agent' => $this->userAgent],
                'allow_redirects' => ['max' => 3],
            ]);
        } catch (\Throwable) {
            // Well-known probes must never fail the overall fetch — a bad
            // derived URL or handler-level error just means "not detected".
            return [new DiscoveredResource(false, $url, null, null, $source), null];
        }

        $status = $response->getStatusCode();
        $exists = $status >= 200 && $status < 400;
        $contentType = $response->getHeaderLine('Content-Type') ?: null;
        $body = $exists ? (string) $response->getBody() : null;

        return [new DiscoveredResource($exists, $url, $status, $contentType, $source), $body];
    }

    /**
     * @return array<string, string>
     */
    private function flattenHeaders(ResponseInterface $response): array
    {
        $flattened = [];

        foreach ($response->getHeaders() as $name => $values) {
            $flattened[$name] = implode(', ', $values);
        }

        return $flattened;
    }

    /**
     * @param array<int, string> $errors
     */
    private function failureResult(string $url, string $fetchedAt, array $errors): FetchResult
    {
        return new FetchResult(
            url: $url,
            success: false,
            finalUrl: null,
            statusCode: null,
            headers: [],
            html: null,
            meta: null,
            cssLinks: [],
            jsLinks: [],
            images: [],
            fonts: [],
            anchors: [],
            headings: [],
            schema: [],
            wordCount: 0,
            robotsTxt: DiscoveredResource::notFound(),
            sitemap: DiscoveredResource::notFound(),
            rssFeeds: [],
            manifest: DiscoveredResource::notFound(),
            redirectChain: [],
            responseTimeMs: null,
            errors: $errors,
            fetchedAt: $fetchedAt,
        );
    }
}
