<?php

declare(strict_types=1);

namespace App\Audit\UiUx\DTO;

use App\Audit\Enums\UiUxElementStatus;

final readonly class UiUxElementResult implements \JsonSerializable
{
    /**
     * @param array<int, string> $issues problems found with this element; empty when status is Pass
     * @param array<int, string> $suggestions improvement suggestions; empty when status is Pass
     */
    public function __construct(
        public string $element,
        public UiUxElementStatus $status,
        public array $issues,
        public array $suggestions,
    ) {
    }

    /**
     * @return array{element: string, status: string, issues: array<int, string>, suggestions: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'element' => $this->element,
            'status' => $this->status->value,
            'issues' => $this->issues,
            'suggestions' => $this->suggestions,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
