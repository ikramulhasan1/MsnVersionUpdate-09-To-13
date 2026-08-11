<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

use App\Audit\Enums\SeoSeverity;

/**
 * A single issue pulled from any completed analyzer's result and
 * normalized into one shape so the AI Recommendation Engine can rank
 * issues from Security, Accessibility, Content, UI/UX, Performance,
 * Business Opportunity, and SEO against each other on equal terms.
 *
 * Reuses {@see SeoSeverity} (critical/warning/notice) as the severity
 * dimension rather than introducing a new enum, the same way
 * WebsiteHealthIssue reuses it to compare issues across domains that
 * otherwise use their own pass/warning/fail or good/warning/critical
 * vocabularies. $status keeps the analyzer's own status label (e.g.
 * "Fail", "Critical") for display, while $severity is what
 * cross-category prioritization sorts on.
 */
final readonly class PriorityIssue implements \JsonSerializable
{
    public function __construct(
        public string $category,
        public string $issue,
        public SeoSeverity $severity,
        public string $status,
        public ?string $value,
        public ?string $recommendation,
        public ?string $pageUrl = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'issue' => $this->issue,
            'severity' => $this->severity->value,
            'status' => $this->status,
            'value' => $this->value,
            'recommendation' => $this->recommendation,
            'page_url' => $this->pageUrl,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
