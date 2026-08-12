<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class ImageAsset implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $alt = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?string $pageUrl = null,
        public ?string $domPath = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'alt' => $this->alt,
            'width' => $this->width,
            'height' => $this->height,
            'page_url' => $this->pageUrl,
            'dom_path' => $this->domPath,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}