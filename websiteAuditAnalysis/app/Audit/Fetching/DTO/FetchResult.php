<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class FetchResult implements \JsonSerializable
{
    /**
     * @param array<string, string> $headers
     * @param array<int, CssLink> $cssLinks
     * @param array<int, ScriptLink> $jsLinks
     * @param array<int, ImageAsset> $images
     * @param array<int, FontAsset> $fonts
     * @param array<int, DiscoveredResource> $rssFeeds
     * @param array<int, AnchorLink> $anchors
     * @param array<int, Heading> $headings
     * @param array<int, SchemaBlock> $schema
     * @param array<int, string> $redirectChain
     * @param array<int, string> $errors
     * @param array<int, string> $mailtoLinks raw addresses from every mailto: link on the page
     * @param array<int, string> $telLinks raw numbers from every tel: link on the page
     */
    public function __construct(
        public string $url,
        public bool $success,
        public ?string $finalUrl,
        public ?int $statusCode,
        public array $headers,
        public ?string $html,
        public ?MetaData $meta,
        public array $cssLinks,
        public array $jsLinks,
        public array $images,
        public array $fonts,
        public array $anchors,
        public array $headings,
        public array $schema,
        public int $wordCount,
        public DiscoveredResource $robotsTxt,
        public DiscoveredResource $sitemap,
        public array $rssFeeds,
        public DiscoveredResource $manifest,
        public array $redirectChain,
        public ?int $responseTimeMs,
        public array $errors,
        public string $fetchedAt,
        public array $mailtoLinks = [],
        public array $telLinks = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'success' => $this->success,
            'final_url' => $this->finalUrl,
            'status_code' => $this->statusCode,
            'headers' => $this->headers,
            'html' => $this->html,
            'meta' => $this->meta?->toArray(),
            'css_links' => array_map(static fn (CssLink $c): array => $c->toArray(), $this->cssLinks),
            'js_links' => array_map(static fn (ScriptLink $s): array => $s->toArray(), $this->jsLinks),
            'images' => array_map(static fn (ImageAsset $i): array => $i->toArray(), $this->images),
            'fonts' => array_map(static fn (FontAsset $f): array => $f->toArray(), $this->fonts),
            'anchors' => array_map(static fn (AnchorLink $a): array => $a->toArray(), $this->anchors),
            'headings' => array_map(static fn (Heading $h): array => $h->toArray(), $this->headings),
            'schema' => array_map(static fn (SchemaBlock $s): array => $s->toArray(), $this->schema),
            'word_count' => $this->wordCount,
            'robots_txt' => $this->robotsTxt->toArray(),
            'sitemap' => $this->sitemap->toArray(),
            'rss_feeds' => array_map(static fn (DiscoveredResource $r): array => $r->toArray(), $this->rssFeeds),
            'manifest' => $this->manifest->toArray(),
            'redirect_chain' => $this->redirectChain,
            'response_time_ms' => $this->responseTimeMs,
            'errors' => $this->errors,
            'fetched_at' => $this->fetchedAt,
            'mailto_links' => $this->mailtoLinks,
            'tel_links' => $this->telLinks,
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