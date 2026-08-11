<?php

declare(strict_types=1);

namespace App\Audit\Content\DTO;

use App\Audit\Enums\ContentCheckStatus;

final readonly class ContentCheckResult implements \JsonSerializable
{
    public function __construct(
        public string $metric,
        public ?string $value,
        public ContentCheckStatus $status,
        public ?string $recommendation,
    ) {
    }

    /**
     * @return array{metric: string, value: ?string, status: string, recommendation: ?string}
     */
    public function toArray(): array
    {
        return [
            'metric' => $this->metric,
            'value' => $this->value,
            'status' => $this->status->value,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
