<?php

declare(strict_types=1);

namespace App\Discovery\Sources\DTO;

/**
 * A candidate website some App\Discovery\Sources\Contracts\DiscoverySourceInterface
 * implementation found — deliberately NOT a DiscoveredWebsite Eloquent
 * model. A source's whole job is reporting "this URL looks like a
 * match for this search" — it never touches the database directly
 * (see that interface's own docblock for why), so its return type is a
 * plain, source-agnostic value object every implementation (this
 * phase's InternalCrawlSource, and any future GoogleSearchSource/
 * BusinessDirectorySource/...) produces identically.
 *
 * Converting a batch of these into real, persisted DiscoveredWebsite
 * rows — deduplicating via App\Discovery\Normalization\DomainNormalizer
 * (Phase I2), the same normalizer InternalCrawlSource already uses
 * internally to avoid re-discovering a site this module already knows
 * about — is a future ingestion step this phase doesn't build yet;
 * see InternalCrawlSource's own docblock for exactly where that
 * boundary sits.
 *
 * $industry/$country/$city are deliberately nullable and, for
 * InternalCrawlSource specifically, always null: that source has no
 * real signal for either (see its own docblock) and this DTO would
 * rather leave a field empty than have a source guess at data it
 * doesn't actually know, the same "don't fabricate what isn't there"
 * rule this module's other components already follow. A future source
 * with a genuine signal for these (e.g. a business directory API that
 * returns a category/location directly) can populate them for real.
 */
final readonly class DiscoveredWebsiteDTO
{
    public function __construct(
        public string $url,
        public string $domain,
        public string $discoverySource,
        public ?string $industry = null,
        public ?string $country = null,
        public ?string $city = null,
    ) {}
}
