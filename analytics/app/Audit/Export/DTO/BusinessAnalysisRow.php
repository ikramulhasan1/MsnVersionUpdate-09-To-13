<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Business Analysis" worksheet: one Website
 * Health issue (Website Problems, SEO Issues, Performance Issues,
 * Website Modernization, Marketing Analysis, or Content & Conversion
 * Analysis) from BusinessOpportunityResult::$websiteHealth.
 * Deliberately excludes the Lead/Priority/Opportunity scores and Sales
 * Opportunity / Outreach Message data, which are summary-level rather
 * than per-issue and are out of scope for this row shape.
 */
final readonly class BusinessAnalysisRow
{
    public function __construct(
        public string $category,
        public string $issue,
        public string $status,
        public string $severity,
        public ?string $recommendation,
    ) {
    }
}
