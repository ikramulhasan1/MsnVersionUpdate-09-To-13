<?php

declare(strict_types=1);

namespace App\Discovery\Sources;

use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Discovery\Normalization\DomainNormalizer;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use App\Discovery\Sources\DTO\DiscoveredWebsiteDTO;
use App\Models\DiscoveredWebsite;
use Illuminate\Support\Collection;

/**
 * The Website Discovery module's first, and today only,
 * DiscoverySourceInterface implementation (Phase I3) — finds NEW
 * candidate sites by crawling OUTWARD from sites this module already
 * knows about, reusing App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface
 * completely unmodified rather than building any new crawling logic:
 * this class is orchestration (which sites to seed from, which links
 * to keep, how to dedupe) on top of an existing capability, not a new
 * one.
 *
 * How it works:
 *   1. Seeds — up to self::MAX_SEEDS existing DiscoveredWebsite rows
 *      already matching $criteria, via WebsiteSearchService::search()
 *      (the exact same query the manual search panel/List View itself
 *      runs). "Sites like the ones this search already found" is the
 *      expansion strategy: a restaurant site's own external links are
 *      disproportionately likely to point at other restaurant-adjacent
 *      sites, a reasonable (if imperfect) heuristic for a source that
 *      has no external search API to call.
 *   2. Crawl — each seed gets one SHALLOW crawl
 *      (self::SEED_CRAWL_MAX_DEPTH/self::SEED_CRAWL_MAX_PAGES, both
 *      deliberately small: this is discovering candidates, not
 *      auditing the seed itself) via WebsiteCrawlerServiceInterface::crawl(),
 *      harvesting $externalLinks — every external URL the crawler
 *      found on the seed's own pages.
 *   3. Filter — a link known to be broken (LinkInventoryEntry::$exists
 *      === false) is dropped; nothing else about link quality is
 *      judged at this layer.
 *   4. Dedupe — every surviving candidate is hashed via
 *      App\Discovery\Normalization\DomainNormalizer (Phase I2), the
 *      exact same normalization DiscoveredWebsite::booted() applies
 *      before storing url_hash, so a candidate that's really just
 *      "http://" vs "https://www." of a site this module already has
 *      is correctly recognized as already-known rather than
 *      "discovered" a second time. Checked via ONE bulk whereIn()
 *      query against every candidate hash at once, not one exists()
 *      query per candidate — a handful of seeds crawled shallowly can
 *      still realistically surface dozens of external links between
 *      them.
 *
 * $industry/$country/$city are left null on every DiscoveredWebsiteDTO
 * this source produces — see that DTO's own docblock for why a source
 * with no real signal for a field should leave it empty rather than
 * inherit the seed's own value as an unverified guess.
 */
final class InternalCrawlSource implements DiscoverySourceInterface
{
    private const int MAX_SEEDS = 10;

    private const int SEED_CRAWL_MAX_DEPTH = 1;

    private const int SEED_CRAWL_MAX_PAGES = 5;

    public function __construct(
        private readonly WebsiteCrawlerServiceInterface $crawler,
        private readonly WebsiteSearchService $websiteSearchService,
        private readonly DomainNormalizer $domainNormalizer = new DomainNormalizer,
    ) {}

    public function discover(DiscoveryFilterCriteria $criteria): Collection
    {
        $seeds = $this->websiteSearchService->search($criteria, self::MAX_SEEDS);

        if ($seeds->isEmpty()) {
            return collect();
        }

        /** @var Collection<string, array{url: string, domain: string}> $candidates keyed by normalized url_hash */
        $candidates = collect();

        foreach ($seeds as $seed) {
            $crawlResult = $this->crawler->crawl(
                $seed->url,
                self::SEED_CRAWL_MAX_DEPTH,
                self::SEED_CRAWL_MAX_PAGES,
            );

            foreach ($crawlResult->externalLinks as $link) {
                if ($link->exists === false) {
                    continue;
                }

                $host = parse_url($link->url, PHP_URL_HOST);

                if (! is_string($host) || $host === '') {
                    continue;
                }

                // Collection::put() keyed by hash naturally dedupes across
                // every seed's own external links within this one run — a
                // later duplicate simply overwrites the earlier,
                // equivalent entry.
                $candidates->put($this->domainNormalizer->hash($link->url), [
                    'url' => $link->url,
                    'domain' => $host,
                ]);
            }
        }

        if ($candidates->isEmpty()) {
            return collect();
        }

        $alreadyKnownHashes = DiscoveredWebsite::query()
            ->whereIn('url_hash', $candidates->keys())
            ->pluck('url_hash')
            ->flip();

        return $candidates
            ->reject(fn (array $candidate, string $hash): bool => $alreadyKnownHashes->has($hash))
            ->map(fn (array $candidate): DiscoveredWebsiteDTO => new DiscoveredWebsiteDTO(
                url: $candidate['url'],
                domain: $candidate['domain'],
                discoverySource: $this->sourceName(),
            ))
            ->values();
    }

    public function sourceName(): string
    {
        return 'internal_crawl';
    }
}
