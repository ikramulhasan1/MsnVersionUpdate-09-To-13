<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class AnchorLink implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $text,
        public ?string $rel,
        public bool $nofollow,
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
            'text' => $this->text,
            'rel' => $this->rel,
            'nofollow' => $this->nofollow,
            'page_url' => $this->pageUrl,
            'dom_path' => $this->domPath,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}