<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class FontAsset implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $format = null,
        public string $source = 'link',
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'format' => $this->format,
            'source' => $this->source,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
