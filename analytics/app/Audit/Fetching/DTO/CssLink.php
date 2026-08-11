<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class CssLink implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $rel = 'stylesheet',
        public ?string $media = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'rel' => $this->rel,
            'media' => $this->media,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
