<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class SchemaBlock implements \JsonSerializable
{
    /**
     * @param array<int, string> $types every distinct "@type" found in this
     *        block, including ones nested inside an "@graph" array
     * @param array<mixed>|null $data the decoded JSON-LD payload, or null
     *        when the block failed to decode
     */
    public function __construct(
        public array $types,
        public ?array $data,
        public bool $valid,
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
            'types' => $this->types,
            'valid' => $this->valid,
            'data' => $this->data,
            'page_url' => $this->pageUrl,
            'dom_path' => $this->domPath,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}