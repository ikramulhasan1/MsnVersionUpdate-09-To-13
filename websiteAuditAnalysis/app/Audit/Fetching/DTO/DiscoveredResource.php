<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class DiscoveredResource implements \JsonSerializable
{
    public function __construct(
        public bool $exists,
        public ?string $url,
        public ?int $statusCode,
        public ?string $contentType,
        public string $source,
    ) {
    }

    public static function notFound(string $source = 'well_known'): self
    {
        return new self(
            exists: false,
            url: null,
            statusCode: null,
            contentType: null,
            source: $source,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'exists' => $this->exists,
            'url' => $this->url,
            'status_code' => $this->statusCode,
            'content_type' => $this->contentType,
            'source' => $this->source,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
