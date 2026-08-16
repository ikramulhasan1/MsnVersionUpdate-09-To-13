<?php

declare(strict_types=1);

namespace App\Discovery\Export\DTO;

/**
 * One discovered site's worth of export columns — the passive data
 * shape App\Discovery\Export\DiscoveredWebsitesToExportRows maps a
 * DiscoveredWebsite into, and every export format (Excel, CSV, PDF,
 * JSON — Phase H2) reads from. Mirrors App\Audit\Export\DTO\AnalysisRow's
 * own "passive DTO, separate mapper class" split for this module's own
 * export pipeline.
 */
final readonly class DiscoveryExportRow implements \JsonSerializable
{
    public function __construct(
        public string $businessName,
        public string $website,
        public ?string $industry,
        public ?string $country,
        public ?string $city,
        public ?string $technology,
        public ?string $cms,
        public ?int $seoScore,
        public ?int $performanceScore,
        public ?int $securityScore,
        public ?int $accessibilityScore,
        public ?int $mobileScore,
        public ?int $opportunityScore,
        public ?string $email,
        public ?string $phone,
        public ?string $socialLinks,
    ) {}

    /**
     * @return array{
     *     business_name: string, website: string, industry: ?string, country: ?string,
     *     city: ?string, technology: ?string, cms: ?string, seo_score: ?int,
     *     performance_score: ?int, security_score: ?int, accessibility_score: ?int,
     *     mobile_score: ?int, opportunity_score: ?int, email: ?string, phone: ?string,
     *     social_links: ?string,
     * }
     */
    public function toArray(): array
    {
        return [
            'business_name' => $this->businessName,
            'website' => $this->website,
            'industry' => $this->industry,
            'country' => $this->country,
            'city' => $this->city,
            'technology' => $this->technology,
            'cms' => $this->cms,
            'seo_score' => $this->seoScore,
            'performance_score' => $this->performanceScore,
            'security_score' => $this->securityScore,
            'accessibility_score' => $this->accessibilityScore,
            'mobile_score' => $this->mobileScore,
            'opportunity_score' => $this->opportunityScore,
            'email' => $this->email,
            'phone' => $this->phone,
            'social_links' => $this->socialLinks,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
