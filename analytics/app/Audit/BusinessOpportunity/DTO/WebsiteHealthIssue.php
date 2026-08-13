<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

use App\Audit\Enums\BusinessOpportunityCheckStatus;
use App\Audit\Enums\SeoSeverity;

/**
 * A single Website Health issue (Website Problems / SEO Issues /
 * Performance Issues categories). Deliberately a separate DTO from
 * BusinessOpportunityCheckResult rather than adding fields to it:
 * BusinessOpportunityCheckResult has no severity dimension and its
 * existing (check, value, status, recommendation) shape is left
 * untouched.
 *
 * Reuses two enums already established elsewhere in the codebase
 * instead of introducing new ones: BusinessOpportunityCheckStatus for
 * "Status" (did this specific check pass/warn/fail) and Seo's
 * SeoSeverity for "Severity" (how serious the issue is if it didn't
 * pass) — the two are independent dimensions, e.g. a WARNING-status
 * check can still be a CRITICAL-severity issue.
 *
 * $pageUrl records which page (of potentially several — see
 * BusinessOpportunityAnalyzer::analyzeAll()) this issue was found on;
 * $elementUrl additionally records the specific resource responsible
 * when there is one (e.g. the image missing width/height for an
 * "Image Dimensions" issue) — null when the issue is a page-level
 * finding with no single resource to point at (e.g. a missing title
 * tag).
 */
final readonly class WebsiteHealthIssue implements \JsonSerializable
{
    public function __construct(
        public string $issue,
        public BusinessOpportunityCheckStatus $status,
        public SeoSeverity $severity,
        public ?string $recommendation,
        public ?string $pageUrl = null,
        public ?string $elementUrl = null,
    ) {}

    /**
     * @return array{issue: string, status: string, severity: string, recommendation: ?string, page_url: ?string, element_url: ?string}
     */
    public function toArray(): array
    {
        return [
            'issue' => $this->issue,
            'status' => $this->status->value,
            'severity' => $this->severity->value,
            'recommendation' => $this->recommendation,
            'page_url' => $this->pageUrl,
            'element_url' => $this->elementUrl,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
