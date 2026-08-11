<?php

declare(strict_types=1);

namespace App\Audit\Export\Api\DTO;

/**
 * Everything the JSON Export API's AuditResource needs — one field per
 * requested category (SEO, Performance, Security, Accessibility, UI/UX,
 * Content, Technology Stack, Business Analysis, Scores, Recommendations)
 * plus the identifying/meta fields (uuid, url, status).
 *
 * Each analysis field is already a plain array (each analyzer DTO's own
 * ->toArray(), e.g. SecurityResult::toArray()) rather than the DTO
 * object itself, so AuditResource stays a thin passthrough with no
 * knowledge of any analyzer's internal shape — see
 * AnalysisResultsToApiData, the only class that reads those DTOs.
 *
 * Every analysis field is nullable: an analyzer may not have run yet
 * for this audit, and the API should return null for that category
 * rather than fail or fabricate data — the same "dynamic, never
 * hardcoded" contract the PDF and Excel exports already follow.
 *
 * Group U adds the five Lead Intelligence fields below, following the
 * exact same "own DTO's ->toArray(), nullable, never fabricated"
 * pattern as every field above: $businessSignals, $contactInfo,
 * $reviewPresence, and $prospectQualification are each that DTO's own
 * ->toArray() (or null when that analyzer/scorer hasn't run);
 * $technologyUpgradeOpportunities is a plain array (never null — "no
 * opportunities found" and "not yet computed" mean the same thing here,
 * matching AnalysisResults::$technologyUpgradeOpportunities' own
 * default of [] rather than null); $outreachDraft is
 * OutreachDraftResult::toArray() or null — a DRAFT for human review
 * only, exposed as data here, never auto-sent by this codebase.
 */
final readonly class AuditApiData
{
    /**
     * @param ?array<string, mixed> $seoAnalysis
     * @param ?array<string, mixed> $performanceAnalysis
     * @param ?array<string, mixed> $securityAnalysis
     * @param ?array<string, mixed> $accessibilityAnalysis
     * @param ?array<string, mixed> $uiUxAnalysis
     * @param ?array<string, mixed> $contentAnalysis
     * @param ?array<string, mixed> $technologyStack
     * @param ?array<string, mixed> $businessAnalysis
     * @param array<int, array{category: string, score: ?int, grade: ?string, analyzed_at: string}> $scores
     * @param ?array<int, array{priority: int, category: string, issue: string, severity: string, status: string, recommendation: ?string, page_url: ?string}> $recommendations
     * @param ?array<string, mixed> $businessSignals
     * @param ?array<string, mixed> $contactInfo
     * @param ?array<string, mixed> $reviewPresence
     * @param array<int, array<string, mixed>> $technologyUpgradeOpportunities
     * @param ?array<string, mixed> $prospectQualification
     * @param ?array<string, mixed> $outreachDraft
     */
    public function __construct(
        public string $uuid,
        public string $url,
        public string $status,
        public ?array $seoAnalysis,
        public ?array $performanceAnalysis,
        public ?array $securityAnalysis,
        public ?array $accessibilityAnalysis,
        public ?array $uiUxAnalysis,
        public ?array $contentAnalysis,
        public ?array $technologyStack,
        public ?array $businessAnalysis,
        public array $scores,
        public ?array $recommendations,
        public ?array $businessSignals = null,
        public ?array $contactInfo = null,
        public ?array $reviewPresence = null,
        public array $technologyUpgradeOpportunities = [],
        public ?array $prospectQualification = null,
        public ?array $outreachDraft = null,
    ) {
    }
}