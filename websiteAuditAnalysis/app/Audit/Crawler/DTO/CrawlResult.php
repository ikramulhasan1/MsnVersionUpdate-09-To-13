<?php

declare(strict_types=1);

namespace App\Audit\Crawler\DTO;

final readonly class CrawlResult implements \JsonSerializable
{
    /**
     * @param array<int, CrawledPage> $pages internal pages actually fetched, in crawl (BFS) order
     * @param array<int, LinkInventoryEntry> $internalPages every internal URL discovered — crawled or not
     * @param array<int, LinkInventoryEntry> $externalLinks every external URL discovered
     * @param array<int, LinkInventoryEntry> $brokenLinks subset of the above two lists where the link is broken
     */
    public function __construct(
        public string $startUrl,
        public ?string $origin,
        public array $pages,
        public array $internalPages,
        public array $externalLinks,
        public array $brokenLinks,
        public int $maxDepth,
        public int $maxPages,
        public bool $truncated,
        public ?int $durationMs,
        public string $crawledAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'start_url' => $this->startUrl,
            'origin' => $this->origin,
            'summary' => [
                'pages_crawled' => count($this->pages),
                'internal_pages_discovered' => count($this->internalPages),
                'external_links_discovered' => count($this->externalLinks),
                'broken_links' => count($this->brokenLinks),
                'max_depth' => $this->maxDepth,
                'max_pages' => $this->maxPages,
                'truncated' => $this->truncated,
                'duration_ms' => $this->durationMs,
            ],
            'pages' => array_map(static fn (CrawledPage $p): array => $p->toArray(), $this->pages),
            'internal_pages' => array_map(static fn (LinkInventoryEntry $e): array => $e->toArray(), $this->internalPages),
            'external_links' => array_map(static fn (LinkInventoryEntry $e): array => $e->toArray(), $this->externalLinks),
            'broken_links' => array_map(static fn (LinkInventoryEntry $e): array => $e->toArray(), $this->brokenLinks),
            'crawled_at' => $this->crawledAt,
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
