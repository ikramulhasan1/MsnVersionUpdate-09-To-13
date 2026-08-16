<?php

declare(strict_types=1);

namespace App\Discovery\Scoring\DTO;

use App\Discovery\Enums\OpportunityFilter;

/**
 * One detected "you could sell this site a service" recommendation —
 * App\Discovery\Scoring\ServiceOpportunityDetector's own output shape
 * (Phase G4).
 */
final readonly class ServiceOpportunity
{
    public function __construct(
        public OpportunityFilter $type,
        public string $serviceName,
        public string $reason,
    ) {}
}
