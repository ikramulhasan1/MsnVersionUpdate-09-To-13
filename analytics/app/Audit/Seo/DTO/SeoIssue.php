<?php

declare(strict_types=1);

namespace App\Audit\Seo\DTO;

use App\Audit\Enums\SeoSeverity;

final readonly class SeoIssue implements \JsonSerializable
{
    public function __construct(
        public string $check,
        public string $code,
        public SeoSeverity $severity,
        public string $message,
        public ?string $recommendation,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'check' => $this->check,
            'code' => $this->code,
            'severity' => $this->severity->value,
            'message' => $this->message,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
