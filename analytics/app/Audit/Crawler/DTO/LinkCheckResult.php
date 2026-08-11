<?php

declare(strict_types=1);

namespace App\Audit\Crawler\DTO;

final readonly class LinkCheckResult
{
    public function __construct(
        public bool $exists,
        public ?int $statusCode,
        public ?string $error,
    ) {
    }
}
