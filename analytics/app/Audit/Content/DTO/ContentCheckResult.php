<?php

declare(strict_types=1);

namespace App\Audit\Content\DTO;

use App\Audit\Enums\ContentCheckStatus;

final readonly class ContentCheckResult implements \JsonSerializable
{
    /**
     * @param  ?array<int, array{url: ?string, domPath: ?string, detail: ?string}>  $affectedElements
     *                                                                                                 The specific block(s)/sentence(s) responsible for a non-good
     *                                                                                                 status — e.g. which blocks a repeated passage was found in
     *                                                                                                 for Duplicate Content, which sentence and approximate
     *                                                                                                 location a potential issue was found at for Grammar, or
     *                                                                                                 which blocks the over-used keyword appears in for Keyword
     *                                                                                                 Density. Null when the check doesn't have anything more
     *                                                                                                 specific to point at (or when it passed outright).
     */
    public function __construct(
        public string $metric,
        public ?string $value,
        public ContentCheckStatus $status,
        public ?string $recommendation,
        public ?string $pageUrl = null,
        public ?array $affectedElements = null,
    ) {}

    /**
     * @return array{metric: string, value: ?string, status: string, recommendation: ?string, page_url: ?string, affected_elements: ?array<int, array{url: ?string, domPath: ?string, detail: ?string}>}
     */
    public function toArray(): array
    {
        return [
            'metric' => $this->metric,
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
