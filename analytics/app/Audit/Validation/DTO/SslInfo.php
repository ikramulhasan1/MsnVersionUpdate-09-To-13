<?php

declare(strict_types=1);

namespace App\Audit\Validation\DTO;

final readonly class SslInfo implements \JsonSerializable
{
    public function __construct(
        public bool $valid,
        public ?string $issuer = null,
        public ?string $validFrom = null,
        public ?string $validTo = null,
        public ?int $daysUntilExpiry = null,
        public ?string $error = null,
    ) {
    }

    public static function unavailable(string $error): self
    {
        return new self(valid: false, error: $error);
    }

    /**
     * @return array{valid: bool, issuer: ?string, valid_from: ?string, valid_to: ?string, days_until_expiry: ?int, error: ?string}
     */
    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'issuer' => $this->issuer,
            'valid_from' => $this->validFrom,
            'valid_to' => $this->validTo,
            'days_until_expiry' => $this->daysUntilExpiry,
            'error' => $this->error,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
