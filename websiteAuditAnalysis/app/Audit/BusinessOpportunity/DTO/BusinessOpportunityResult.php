<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

final readonly class BusinessOpportunityResult implements \JsonSerializable
{
    /**
     * @param array<string, BusinessOpportunityCheckResult> $checks keyed by check name.
     *        Left empty until each business opportunity check is implemented.
     * @param ?int $score overall score out of 100; null until at least one check exists to measure
     * @param ?string $grade letter grade (A-F) derived from score; null when score is null
     * @param string $summary human-readable overview of the business opportunity result
     * @param array<string, array<int, WebsiteHealthIssue>> $websiteHealth Website Health issues
     *        (Website Problems / SEO Issues / Performance Issues checks), keyed by category slug
     *        and added independently of $checks/$score/$grade — see WebsiteHealthIssue.
     * @param ?BusinessOpportunityScore $businessOpportunityScore Lead Score / Priority /
     *        Opportunity Score rolled up from every $websiteHealth issue; null until computed.
     * @param ?SalesOpportunity $salesOpportunity Estimated Deal Potential / Suggested Service
     *        derived from $websiteHealth and $businessOpportunityScore; null until computed.
     * @param ?OutreachMessage $outreachMessage Suggested Outreach Message (subject + body) templated
     *        from $websiteHealth, $businessOpportunityScore, and $salesOpportunity; null until computed.
     */
    public function __construct(
        public string $url,
        public array $checks,
        public ?int $score,
        public ?string $grade,
        public string $summary,
        public string $analyzedAt,
        public array $websiteHealth = [],
        public ?BusinessOpportunityScore $businessOpportunityScore = null,
        public ?SalesOpportunity $salesOpportunity = null,
        public ?OutreachMessage $outreachMessage = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'checks' => array_map(
                static fn (BusinessOpportunityCheckResult $check): array => $check->toArray(),
                $this->checks,
            ),
            'score' => $this->score,
            'grade' => $this->grade,
            'summary' => $this->summary,
            'analyzed_at' => $this->analyzedAt,
            'website_health' => array_map(
                static fn (array $issues): array => array_map(
                    static fn (WebsiteHealthIssue $issue): array => $issue->toArray(),
                    $issues,
                ),
                $this->websiteHealth,
            ),
            'business_opportunity_score' => $this->businessOpportunityScore?->toArray(),
            'sales_opportunity' => $this->salesOpportunity?->toArray(),
            'outreach_message' => $this->outreachMessage?->toArray(),
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
