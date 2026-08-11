<?php

declare(strict_types=1);

namespace App\Audit\DTO;

final readonly class CreateAuditData
{
    public function __construct(
        public string $url,
    ) {
    }

    /**
     * @param array{url: string} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            url: rtrim($attributes['url'], '/'),
        );
    }
}
