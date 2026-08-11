<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

final readonly class Heading implements \JsonSerializable
{
    public function __construct(
        public int $level,
        public string $text,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'text' => $this->text,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
