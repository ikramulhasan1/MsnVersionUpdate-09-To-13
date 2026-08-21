<?php

declare(strict_types=1);

namespace App\TechnicalSeo\DTO;

/**
 * Phase R2 (Technical SEO Audit) — the same pattern
 * App\OnPageSeo\DTO\OnPageSeoIssue already established for Phase R1:
 * shaped close to App\Audit\Seo\DTO\SeoIssue for a trivial 1:1
 * conversion (see App\TechnicalSeo\TechnicalSeoAnalyzer::toSeoAuditResult()).
 */
final readonly class TechnicalSeoIssue implements \JsonSerializable
{
    public function __construct(
        public string $check,
        public string $severity,
        public string $message,
        public ?string $recommendation = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'check' => $this->check,
            'severity' => $this->severity,
            'message' => $this->message,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}