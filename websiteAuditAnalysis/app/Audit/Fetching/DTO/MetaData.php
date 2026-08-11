<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class MetaData implements \JsonSerializable
{
    /**
     * @param array<string, string> $openGraph
     * @param array<string, string> $twitter
     * @param array<int, array{name: ?string, property: ?string, content: ?string}> $raw
     */
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?string $keywords,
        public ?string $canonical,
        public ?string $robots,
        public ?string $viewport,
        public ?string $charset,
        public array $openGraph,
        public array $twitter,
        public array $raw,
    ) {
    }

    public static function empty(): self
    {
        return new self(
            title: null,
            description: null,
            keywords: null,
            canonical: null,
            robots: null,
            viewport: null,
            charset: null,
            openGraph: [],
            twitter: [],
            raw: [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'canonical' => $this->canonical,
            'robots' => $this->robots,
            'viewport' => $this->viewport,
            'charset' => $this->charset,
            'open_graph' => $this->openGraph,
            'twitter' => $this->twitter,
            'raw' => $this->raw,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
