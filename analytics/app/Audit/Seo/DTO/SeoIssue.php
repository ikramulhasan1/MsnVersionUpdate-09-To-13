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
        public ?string $pageUrl = null,
        public ?string $elementUrl = null,
        public ?string $domPath = null,
        public ?string $context = null,
    ) {}

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
            'page_url' => $this->pageUrl,
            'element_url' => $this->elementUrl,
            'dom_path' => $this->domPath,
            'context' => $this->context,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
