<?php

declare(strict_types=1);

namespace App\Audit\Crawler;

use App\Audit\Crawler\Contracts\LinkCheckerInterface;
use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Crawler\DTO\LinkInventoryEntry;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\UrlResolver;

final class WebsiteCrawlerService implements WebsiteCrawlerServiceInterface
{
    public function __construct(
        private readonly WebsiteFetcherServiceInterface $fetcher,
        private readonly LinkCheckerInterface $linkChecker,
        private readonly int $maxDepth = 2,
        private readonly int $maxPages = 25,
        private readonly bool $checkExternalLinks = true,
        /**
         * How many pages (or link-reachability checks) are fetched at
         * once. Pages within the same BFS wave are independent HTTP
         * requests, so fetching them concurrently instead of one at a
         * time is the single biggest lever on total crawl time — a
         * 25-page crawl that used to be 25 sequential round-trips becomes
         * roughly ceil(25 / concurrency) of them.
         */
        private readonly int $concurrency = 8,
    ) {
    }

    public function crawl(string $startUrl, ?int $maxDepth = null, ?int $maxPages = null, ?callable $onProgress = null): CrawlResult
    {
        $maxDepth = $maxDepth ?? $this->maxDepth;
        $maxPages = $maxPages ?? $this->maxPages;
        $crawledAt = (new \DateTimeImmutable())->format(DATE_ATOM);
        $start = microtime(true);

        $origin = UrlResolver::originOf($startUrl);

        /** @var array<string, array{url: string, isInternal: bool, foundOnPages: array<int, string>, exists: ?bool, statusCode: ?int, error: ?string, checkedVia: string}> $inventory */
        $inventory = [];

        /** @var array<string, true> $visited normalized URLs that have been fetched (or attempted) */
        $visited = [];
        /** @var array<string, true> $enqueued normalized URLs already queued, to avoid duplicate enqueues */
        $enqueued = [self::normalize($startUrl) => true];

        $queue = new \SplQueue();
        $queue->enqueue(['url' => $startUrl, 'depth' => 0]);

        $pages = [];

        while (! $queue->isEmpty()) {
            if (count($pages) >= $maxPages) {
                break;
            }

            // Pull up to $concurrency same-wave items off the queue and
            // fetch them all at once — each is an independent HTTP
            // request, so there is no reason to wait for one before
            // starting the next. This is the only behavioral change from
            // a strict one-at-a-time BFS: pages still come out in queue
            // order, still respect maxDepth/maxPages exactly as before,
            // just several in flight together instead of one at a time.
            $remainingBudget = $maxPages - count($pages);
            $batchSize = max(1, min($this->concurrency, $remainingBudget));

            /** @var array<int, array{url: string, depth: int}> $batch */
            $batch = [];

            while (count($batch) < $batchSize && ! $queue->isEmpty()) {
                /** @var array{url: string, depth: int} $item */
                $item = $queue->dequeue();
                $normalizedUrl = self::normalize($item['url']);

                if (isset($visited[$normalizedUrl])) {
                    continue;
                }

                $visited[$normalizedUrl] = true;
                $batch[] = $item;
            }

            if ($batch === []) {
                continue;
            }

            $results = $this->fetcher->fetchMany(array_map(
                static fn (array $item): string => $item['url'],
                $batch,
            ));

            foreach ($batch as $item) {
                $url = $item['url'];
                $depth = $item['depth'];

                // Defensive fallback only — fetchMany() is documented to
                // return an entry for every URL it's given, successful or
                // not, so this path shouldn't normally be reached.
                $result = $results[$url] ?? $this->fetcher->fetch($url);

                $pages[] = $this->toCrawledPage($result, $depth, $origin);

                $this->recordLink(
                    inventory: $inventory,
                    url: $url,
                    foundOnPage: $url,
                    isInternal: true,
                    exists: $result->success && ($result->statusCode === null || $result->statusCode < 400),
                    statusCode: $result->statusCode,
                    error: $result->success ? null : ($result->errors[0] ?? 'Fetch failed'),
                    checkedVia: 'crawled',
                );

                if (! $result->success) {
                    continue;
                }

                foreach ($result->anchors as $anchor) {
                    if (! $this->isHttpUrl($anchor->url)) {
                        continue;
                    }

                    $isInternal = $this->isInternal($origin, $anchor->url);

                    $this->recordLink(
                        inventory: $inventory,
                        url: $anchor->url,
                        foundOnPage: $url,
                        isInternal: $isInternal,
                    );

                    if (! $isInternal) {
                        continue;
                    }

                    $normalizedAnchor = self::normalize($anchor->url);

                    if ($depth < $maxDepth && ! isset($enqueued[$normalizedAnchor]) && ! isset($visited[$normalizedAnchor])) {
                        $enqueued[$normalizedAnchor] = true;
                        $queue->enqueue(['url' => $anchor->url, 'depth' => $depth + 1]);
                    }
                }
            }

            if ($onProgress !== null) {
                $onProgress(count($pages), $maxPages);
            }
        }

        $truncated = ! $queue->isEmpty();

        $this->checkUncheckedLinks($inventory);

        $internalPages = [];
        $externalLinks = [];
        $brokenLinks = [];

        foreach ($inventory as $entry) {
            $dto = new LinkInventoryEntry(
                url: $entry['url'],
                isInternal: $entry['isInternal'],
                foundOnPages: $entry['foundOnPages'],
                exists: $entry['exists'],
                statusCode: $entry['statusCode'],
                error: $entry['error'],
                checkedVia: $entry['checkedVia'],
            );

            if ($entry['isInternal']) {
                $internalPages[] = $dto;
            } else {
                $externalLinks[] = $dto;
            }

            if ($dto->isBroken()) {
                $brokenLinks[] = $dto;
            }
        }

        return new CrawlResult(
            startUrl: $startUrl,
            origin: $origin,
            pages: $pages,
            internalPages: $internalPages,
            externalLinks: $externalLinks,
            brokenLinks: $brokenLinks,
            maxDepth: $maxDepth,
            maxPages: $maxPages,
            truncated: $truncated,
            durationMs: (int) round((microtime(true) - $start) * 1000),
            crawledAt: $crawledAt,
        );
    }

    private function toCrawledPage(FetchResult $result, int $depth, ?string $origin): CrawledPage
    {
        $internalLinks = [];
        $externalLinks = [];

        foreach ($result->anchors as $anchor) {
            if (! $this->isHttpUrl($anchor->url)) {
                continue;
            }

            if ($this->isInternal($origin, $anchor->url)) {
                if (! in_array($anchor->url, $internalLinks, true)) {
                    $internalLinks[] = $anchor->url;
                }
            } elseif (! in_array($anchor->url, $externalLinks, true)) {
                $externalLinks[] = $anchor->url;
            }
        }

        $robots = strtolower((string) $result->meta?->robots);

        return new CrawledPage(
            url: $result->url,
            depth: $depth,
            success: $result->success,
            finalUrl: $result->finalUrl,
            statusCode: $result->statusCode,
            redirectChain: $result->redirectChain,
            meta: $result->meta,
            title: $result->meta?->title,
            canonical: $result->meta?->canonical,
            noIndex: str_contains($robots, 'noindex'),
            noFollow: str_contains($robots, 'nofollow'),
            anchors: $result->anchors,
            internalLinkUrls: $internalLinks,
            externalLinkUrls: $externalLinks,
            images: $result->images,
            cssAssets: $result->cssLinks,
            jsAssets: $result->jsLinks,
            fontAssets: $result->fonts,
            headings: $result->headings,
            schema: $result->schema,
            wordCount: $result->wordCount,
            responseTimeMs: $result->responseTimeMs,
            errors: $result->errors,
            mailtoLinks: $result->mailtoLinks,
            telLinks: $result->telLinks,
        );
    }

    /**
     * Add or update a URL in the link inventory. The first time a URL is
     * seen it's inserted; every subsequent sighting just appends the
     * referring page to foundOnPages, unless a status is being reported
     * ($checkedVia !== null), which overwrites the stored status. This lets
     * a page that's later fully crawled "upgrade" its own inventory entry
     * from an anchor sighting to a crawled result.
     *
     * @param array<string, array{url: string, isInternal: bool, foundOnPages: array<int, string>, exists: ?bool, statusCode: ?int, error: ?string, checkedVia: string}> &$inventory
     */
    private function recordLink(
        array &$inventory,
        string $url,
        string $foundOnPage,
        bool $isInternal,
        ?bool $exists = null,
        ?int $statusCode = null,
        ?string $error = null,
        ?string $checkedVia = null,
    ): void {
        $normalized = self::normalize($url);

        if (! isset($inventory[$normalized])) {
            $inventory[$normalized] = [
                'url' => $url,
                'isInternal' => $isInternal,
                'foundOnPages' => [],
                'exists' => $exists,
                'statusCode' => $statusCode,
                'error' => $error,
                'checkedVia' => $checkedVia ?? 'not_checked',
            ];
        }

        if (! in_array($foundOnPage, $inventory[$normalized]['foundOnPages'], true)) {
            $inventory[$normalized]['foundOnPages'][] = $foundOnPage;
        }

        if ($checkedVia !== null) {
            $inventory[$normalized]['exists'] = $exists;
            $inventory[$normalized]['statusCode'] = $statusCode;
            $inventory[$normalized]['error'] = $error;
            $inventory[$normalized]['checkedVia'] = $checkedVia;
        }
    }

    /**
     * Reachability-check every inventory entry that wasn't populated by an
     * actual crawl (external links, and internal links beyond the
     * depth/page budget). Skipped entirely when check_external_links is
     * disabled, leaving those entries as "not_checked".
     *
     * @param array<string, array{url: string, isInternal: bool, foundOnPages: array<int, string>, exists: ?bool, statusCode: ?int, error: ?string, checkedVia: string}> &$inventory
     */
    private function checkUncheckedLinks(array &$inventory): void
    {
        if (! $this->checkExternalLinks) {
            return;
        }

        $toCheck = [];

        foreach ($inventory as $normalized => $entry) {
            if ($entry['checkedVia'] === 'not_checked') {
                $toCheck[$normalized] = $entry['url'];
            }
        }

        if ($toCheck === []) {
            return;
        }

        // Checked in concurrency-sized batches rather than all at once —
        // a large site can surface dozens of external links, and firing
        // them all as one giant simultaneous burst would be both
        // impolite to those third-party servers and prone to exhausting
        // local connection limits.
        foreach (array_chunk($toCheck, $this->concurrency, true) as $batch) {
            $results = $this->linkChecker->checkMany(array_values($batch));

            foreach ($batch as $normalized => $url) {
                $check = $results[$url] ?? null;

                if ($check === null) {
                    continue;
                }

                $inventory[$normalized]['exists'] = $check->exists;
                $inventory[$normalized]['statusCode'] = $check->statusCode;
                $inventory[$normalized]['error'] = $check->error;
                $inventory[$normalized]['checkedVia'] = 'head_check';
            }
        }
    }

    private function isHttpUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    private function isInternal(?string $origin, string $url): bool
    {
        if ($origin === null) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $originHost = strtolower((string) parse_url($origin, PHP_URL_HOST));

        if ($host === '' || $originHost === '') {
            return false;
        }

        return self::stripWww($host) === self::stripWww($originHost);
    }

    private static function stripWww(string $host): string
    {
        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }

    /**
     * Normalize a URL for visited/queued/inventory de-duplication: lower
     * -cased scheme + host, trailing slash on the path collapsed, fragment
     * dropped (fragments never change what the server returns).
     */
    private static function normalize(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $scheme = strtolower($parts['scheme'] ?? 'http');
        $host = strtolower($parts['host'] ?? '');
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $path = $path !== '/' ? rtrim($path, '/') : $path;
        $path = $path === '' ? '/' : $path;
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return "{$scheme}://{$host}{$port}{$path}{$query}";
    }
}
