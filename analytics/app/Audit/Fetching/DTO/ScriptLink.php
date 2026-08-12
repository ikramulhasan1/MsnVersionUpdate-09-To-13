<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class ScriptLink implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $type = null,
        public bool $async = false,
        public bool $defer = false,
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
            'type' => $this->type,
            'async' => $this->async,
            'defer' => $this->defer,
            'page_url' => $this->pageUrl,
            'dom_path' => $this->domPath,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}