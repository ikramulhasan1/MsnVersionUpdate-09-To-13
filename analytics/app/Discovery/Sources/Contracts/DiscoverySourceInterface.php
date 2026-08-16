<?php

declare(strict_types=1);

namespace App\Discovery\Sources\Contracts;

use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Sources\DTO\DiscoveredWebsiteDTO;
use Illuminate\Support\Collection;

/**
 * A pluggable source of candidate websites for the Website Discovery
 * module (Phase I3) — the same contract-driven shape
 * App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface already
 * established for that module: a small, behavior-only interface (no
 * implementation detail leaks into the contract), one concrete
 * implementation bound today (App\Discovery\Sources\InternalCrawlSource,
 * bound in App\Providers\DiscoveryServiceProvider), and every future
 * implementation (a GoogleSearchSource calling a search API,
 * a BusinessDirectorySource calling a Google-Places-style API,
 * ...) a drop-in replacement — swap the binding, no caller changes.
 *
 * discover() takes the SAME DiscoveryFilterCriteria the manual search
 * panel/saved searches/scheduled searches already build (Phase D1) —
 * a source describes candidates for "sites like this search", not its
 * own separate query language — and returns
 * App\Discovery\Sources\DTO\DiscoveredWebsiteDTO values, never
 * DiscoveredWebsite Eloquent models directly: a source's job ends at
 * "here are some candidate URLs", not persisting them. Converting a
 * batch of DiscoveredWebsiteDTO into real, deduplicated
 * DiscoveredWebsite rows is a future ingestion step, deliberately not
 * part of this contract — see DiscoveredWebsiteDTO's own docblock.
 *
 * A future phase dispatching several sources at once (e.g. running
 * InternalCrawlSource and a GoogleSearchSource for the same search)
 * would simply loop over an array of DiscoverySourceInterface
 * instances calling discover() on each — nothing about this contract
 * needs to change to support that.
 */
interface DiscoverySourceInterface
{
    /**
     * @return Collection<int, DiscoveredWebsiteDTO>
     */
    public function discover(DiscoveryFilterCriteria $criteria): Collection;

    /**
     * A short, stable identifier for this source (e.g.
     * 'internal_crawl') — written onto every DiscoveredWebsiteDTO this
     * source produces, so a batch of results (or, once ingested, a
     * DiscoveredWebsite row's own discovery_source column) always
     * shows which source found it without the caller needing to
     * inspect which concrete class produced it.
     */
    public function sourceName(): string;
}
