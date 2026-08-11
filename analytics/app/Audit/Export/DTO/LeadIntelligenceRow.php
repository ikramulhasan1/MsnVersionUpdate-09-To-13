<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Lead Intelligence" worksheet: one fact from
 * ProspectQualificationResult, BusinessSignalsResult, ContactInfoResult,
 * or TechnologyUpgradeOpportunity. A single flat (section, item, value,
 * detail) shape covers all four sources the same way AnalysisRow's
 * (category, check, value, status) shape already covers every analyzer
 * on the "Analysis" worksheet — see AnalysisResultsToRows::leadIntelligence()
 * for exactly how each source maps to $section/$item/$value/$detail.
 *
 * Deliberately excludes ReviewPresenceResult (out of scope for this
 * worksheet per its originating prompt, which lists prospect score,
 * priority, business signals, contacts found, and upgrade opportunities
 * only) and OutreachDraftResult (a draft email, not a worksheet fact).
 */
final readonly class LeadIntelligenceRow
{
    public function __construct(
        public string $section,
        public string $item,
        public ?string $value,
        public ?string $detail,
    ) {
    }
}