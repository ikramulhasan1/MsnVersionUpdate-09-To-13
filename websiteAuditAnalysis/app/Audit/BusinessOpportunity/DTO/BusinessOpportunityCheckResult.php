<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

use App\Audit\Enums\BusinessOpportunityCheckStatus;

final readonly class BusinessOpportunityCheckResult implements \JsonSerializable
{
    public function __construct(
        public string $check,
        public ?string $value,
        public BusinessOpportunityCheckStatus $status,
        public ?string $recommendation,
    ) {
    }

    /**
     * @return array{check: string, value: ?string, status: string, recommendation: ?string}
     */
    public function toArray(): array
    {
        return [
            'check' => $this->check,
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
