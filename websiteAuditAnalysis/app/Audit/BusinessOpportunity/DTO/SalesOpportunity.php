<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

/**
 * Sales framing derived from every WebsiteHealthIssue and the
 * BusinessOpportunityScore already computed for this page: a rough
 * deal-size range and which service offering best fits the site's
 * biggest problem area.
 */
final readonly class SalesOpportunity implements \JsonSerializable
{
    public function __construct(
        public string $estimatedDealPotential,
        public string $suggestedService,
    ) {
    }

    /**
     * @return array{estimated_deal_potential: string, suggested_service: string}
     */
    public function toArray(): array
    {
        return [
            'estimated_deal_potential' => $this->estimatedDealPotential,
            'suggested_service' => $this->suggestedService,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
