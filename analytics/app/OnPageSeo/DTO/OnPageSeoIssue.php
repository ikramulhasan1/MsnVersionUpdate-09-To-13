<?php

declare(strict_types=1);

namespace App\OnPageSeo\DTO;

/**
 * Phase R1 (On-Page SEO Checker) — deliberately shaped very close to
 * this app's own existing App\Audit\Seo\DTO\SeoIssue (same
 * check/code/severity/message/recommendation fields) specifically so
 * App\OnPageSeo\OnPageSeoAnalyzer::toSeoAuditResult() can convert a list
 * of these into real SeoIssue objects with a trivial 1:1 mapping — see
 * that method's own docblock for why this reuse matters (it's what lets
 * this whole feature reuse the existing AIRecommendationEngine without
 * forking or reimplementing any of its own logic).
 *
 * $severity is a plain string ('critical'|'warning'|'notice'), not
 * App\Audit\Enums\SeoSeverity itself — this DTO intentionally has NO
 * dependency on the Audit module's own namespace; only the conversion
 * method that bridges the two knows about SeoSeverity at all.
 */
final readonly class OnPageSeoIssue implements \JsonSerializable
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