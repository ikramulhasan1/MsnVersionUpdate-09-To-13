<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

/**
 * Aggregated Business Opportunity Score, rolled up from every
 * WebsiteHealthIssue already produced across $websiteHealth (Website
 * Problems, SEO Issues, Performance Issues, Website Modernization,
 * Marketing Analysis, Content & Conversion Analysis).
 *
 * Deliberately has no recommendation field — this phase generates
 * scores only, not new recommendation text.
 */
final readonly class BusinessOpportunityScore implements \JsonSerializable
{
    public function __construct(
        public int $leadScore,
        public string $priority,
        public int $opportunityScore,
    ) {
    }

    /**
     * @return array{lead_score: int, priority: string, opportunity_score: int}
     */
    public function toArray(): array
    {
        return [
            'lead_score' => $this->leadScore,
            'priority' => $this->priority,
            'opportunity_score' => $this->opportunityScore,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
