<?php

declare(strict_types=1);

namespace App\Audit\Accessibility\DTO;

use App\Audit\Enums\AccessibilityCheckStatus;

final readonly class AccessibilityCheckResult implements \JsonSerializable
{
    /**
     * @param  ?array<int, array{url: ?string, domPath: ?string, detail: ?string}>  $affectedElements
     *                                                                                                 The specific element(s) responsible for a non-pass status —
     *                                                                                                 e.g. which <img> src is missing alt text in the Alt check,
     *                                                                                                 or which element's inline style falls below the WCAG
     *                                                                                                 contrast ratio in the Contrast check, together with its
     *                                                                                                 location in the DOM. Null when the check doesn't have
     *                                                                                                 anything more specific to point at (or when it passed
     *                                                                                                 outright).
     */
    public function __construct(
        public string $check,
        public ?string $value,
        public AccessibilityCheckStatus $status,
        public ?string $recommendation,
        public ?string $pageUrl = null,
        public ?array $affectedElements = null,
    ) {}

    /**
     * @return array{check: string, value: ?string, status: string, recommendation: ?string, page_url: ?string, affected_elements: ?array<int, array{url: ?string, domPath: ?string, detail: ?string}>}
     */
    public function toArray(): array
    {
        return [
            'check' => $this->check,
            'value' => $this->value,
            'status' => $this->status->value,
            'recommendation' => $this->recommendation,
            'page_url' => $this->pageUrl,
            'affected_elements' => $this->affectedElements,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
