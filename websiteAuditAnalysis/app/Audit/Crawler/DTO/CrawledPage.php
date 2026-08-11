<?php

declare(strict_types=1);

namespace App\Audit\Crawler\DTO;

use App\Audit\Fetching\DTO\AnchorLink;
use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\FontAsset;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\MetaData;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\Fetching\DTO\ScriptLink;

final readonly class CrawledPage implements \JsonSerializable
{
    /**
     * @param array<int, string> $redirectChain
     * @param array<int, AnchorLink> $anchors
     * @param array<int, string> $internalLinkUrls unique internal URLs linked from this page
     * @param array<int, string> $externalLinkUrls unique external URLs linked from this page
     * @param array<int, ImageAsset> $images
     * @param array<int, CssLink> $cssAssets
     * @param array<int, ScriptLink> $jsAssets
     * @param array<int, FontAsset> $fontAssets
     * @param array<int, Heading> $headings
     * @param array<int, SchemaBlock> $schema
     * @param array<int, string> $errors
     * @param array<int, string> $mailtoLinks raw addresses from every mailto: link on this page
     * @param array<int, string> $telLinks raw numbers from every tel: link on this page
     */
    public function __construct(
        public string $url,
        public int $depth,
        public bool $success,
        public ?string $finalUrl,
        public ?int $statusCode,
        public array $redirectChain,
        public ?MetaData $meta,
        public ?string $title,
        public ?string $canonical,
        public bool $noIndex,
        public bool $noFollow,
        public array $anchors,
        public array $internalLinkUrls,
        public array $externalLinkUrls,
        public array $images,
        public array $cssAssets,
        public array $jsAssets,
        public array $fontAssets,
        public array $headings,
        public array $schema,
        public int $wordCount,
        public ?int $responseTimeMs,
        public array $errors,
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
            'depth' => $this->depth,
            'success' => $this->success,
            'final_url' => $this->finalUrl,
            'status_code' => $this->statusCode,
            'redirect_chain' => $this->redirectChain,
            'meta' => $this->meta?->toArray(),
            'title' => $this->title,
            'canonical' => $this->canonical,
            'no_index' => $this->noIndex,
            'no_follow' => $this->noFollow,
            'anchor_count' => count($this->anchors),
            'anchors' => array_map(static fn (AnchorLink $a): array => $a->toArray(), $this->anchors),
            'internal_link_urls' => $this->internalLinkUrls,
            'external_link_urls' => $this->externalLinkUrls,
            'images' => array_map(static fn (ImageAsset $i): array => $i->toArray(), $this->images),
            'assets' => [
                'css' => array_map(static fn (CssLink $c): array => $c->toArray(), $this->cssAssets),
                'js' => array_map(static fn (ScriptLink $s): array => $s->toArray(), $this->jsAssets),
                'fonts' => array_map(static fn (FontAsset $f): array => $f->toArray(), $this->fontAssets),
            ],
            'headings' => array_map(static fn (Heading $h): array => $h->toArray(), $this->headings),
            'schema' => array_map(static fn (SchemaBlock $s): array => $s->toArray(), $this->schema),
            'word_count' => $this->wordCount,
            'response_time_ms' => $this->responseTimeMs,
            'errors' => $this->errors,
            'mailto_links' => $this->mailtoLinks,
            'tel_links' => $this->telLinks,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}