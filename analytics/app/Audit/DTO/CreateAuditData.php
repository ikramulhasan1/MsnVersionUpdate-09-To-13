<?php

declare(strict_types=1);

namespace App\Audit\DTO;

use App\Audit\Enums\AuditMode;

final readonly class CreateAuditData
{
    public function __construct(
        public string $url,
        public AuditMode $mode = AuditMode::FULL,
    ) {
    }

    /**
     * @param array{url: string, mode?: string|AuditMode|null} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        $mode = $attributes['mode'] ?? AuditMode::FULL;

        return new self(
            url: rtrim($attributes['url'], '/'),
            mode: $mode instanceof AuditMode ? $mode : (AuditMode::tryFrom((string) $mode) ?? AuditMode::FULL),
        );
    }
}