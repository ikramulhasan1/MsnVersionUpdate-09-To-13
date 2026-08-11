<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

use App\Audit\Enums\SeoSeverity;

/**
 * A single suggested service offering for one issue category (e.g.
 * "seo" -> "SEO Optimization Package"), with how many issues in that
 * category drove the suggestion and the most severe one found. A
 * separate DTO from {@see \App\Audit\BusinessOpportunity\DTO\SalesOpportunity}:
 * that DTO covers only the Business Opportunity analyzer's own
 * websiteHealth categories, while this one spans every category the
 * AI Recommendation Engine sees (security, accessibility, content,
 * ui_ux, performance, business_opportunity, seo).
 */
final readonly class ServiceRecommendation implements \JsonSerializable
{
    public function __construct(
        public string $category,
        public string $service,
        public int $issueCount,
        public SeoSeverity $topSeverity,
        public string $recommendation,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'service' => $this->service,
            'issue_count' => $this->issueCount,
            'top_severity' => $this->topSeverity->value,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
