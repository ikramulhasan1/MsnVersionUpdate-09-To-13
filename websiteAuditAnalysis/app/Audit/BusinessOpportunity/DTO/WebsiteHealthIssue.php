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
 */
final readonly class WebsiteHealthIssue implements \JsonSerializable
{
    public function __construct(
        public string $issue,
        public BusinessOpportunityCheckStatus $status,
        public SeoSeverity $severity,
        public ?string $recommendation,
    ) {
    }

    /**
     * @return array{issue: string, status: string, severity: string, recommendation: ?string}
     */
    public function toArray(): array
    {
        return [
            'issue' => $this->issue,
            'status' => $this->status->value,
            'severity' => $this->severity->value,
            'recommendation' => $this->recommendation,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
