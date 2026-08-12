<?php

declare(strict_types=1);

namespace App\Audit\Security\DTO;

use App\Audit\Enums\SecurityCheckStatus;

final readonly class SecurityCheckResult implements \JsonSerializable
{
    /**
     * @param  ?array<int, array{url: ?string, domPath: ?string, detail: ?string}>  $affectedElements
     *                                                                                                 The specific resource(s)/cookie(s)/header(s) responsible for a
     *                                                                                                 non-pass status — e.g. which insecure (http://) resource URLs
     *                                                                                                 mixed_content found, or which cookie names are missing the
     *                                                                                                 Secure flag in cookie_security. Null when the check doesn't
     *                                                                                                 have anything more specific to point at than the page itself
     *                                                                                                 (or when it passed outright).
     */
    public function __construct(
        public string $check,
        public ?string $value,
        public SecurityCheckStatus $status,
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
