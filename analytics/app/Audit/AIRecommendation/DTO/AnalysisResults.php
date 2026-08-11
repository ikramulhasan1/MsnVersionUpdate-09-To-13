<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation\DTO;

use App\Audit\Accessibility\DTO\AccessibilityResult;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\BusinessSignals\DTO\BusinessSignalsResult;
use App\Audit\Contacts\DTO\ContactInfoResult;
use App\Audit\Content\DTO\ContentResult;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Outreach\DTO\OutreachDraftResult;
use App\Audit\Performance\DTO\PerformanceResult;
use App\Audit\ReviewPresence\DTO\ReviewPresenceResult;
use App\Audit\Security\DTO\SecurityResult;
use App\Audit\Seo\DTO\SeoAuditResult;
use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use App\Audit\UiUx\DTO\UiUxResult;

/**
 * Bundles every existing analyzer's completed result for a single page
 * (plus the site-wide SEO audit) into one input the AI Recommendation
 * Engine can process, without the engine depending directly on each
 * analyzer class — only on this DTO and the result DTOs it carries.
 *
 * Every result is nullable: a caller may not have run (or may not yet
 * have finished) every analyzer, and the engine should be able to work
 * from whatever subset is available rather than requiring all eight.
 *
 * $seo is a SeoAuditResult (site-wide, covering every crawled page)
 * rather than a single page's SEO result, since that is what
 * SeoAnalyzerService::analyze() returns — every other property here is
 * a single-page result, matching the FetchResult-based analyzers.
 *
 * $technologyUpgradeOpportunities is computed from $technology (by
 * TechnologyUpgradeAnalyzer), not populated by an AnalyzeChunkJob
 * fragment like every other property here — see AssembleAnalysisResultsJob,
 * which fills it in at assembly time once $technology is already known.
 * Defaults to [] (not null) since "no opportunities found" and "not yet
 * computed" both mean the same thing to every caller of this DTO: there
 * is nothing to show.
 *
 * $prospectQualification is likewise computed at assembly time (by
 * ProspectQualificationScorer, after $technologyUpgradeOpportunities is
 * known), from $businessOpportunity, $businessSignals, and
 * $technologyUpgradeOpportunities — see AssembleAnalysisResultsJob. Stays
 * null (never a fabricated score) whenever $businessOpportunity is null.
 *
 * $outreachDraft is likewise computed at assembly time (by
 * OutreachDraftGenerator, after $prospectQualification is known), from
 * $businessOpportunity->websiteHealth, $contactInfo, $url, and
 * $prospectQualification — see AssembleAnalysisResultsJob. A DRAFT FOR
 * HUMAN REVIEW ONLY; never wired to an auto-send path. Stays null
 * whenever there isn't enough real data to personalize a draft — see
 * OutreachDraftGenerator.
 */
final readonly class AnalysisResults implements \JsonSerializable
{
    /**
     * @param  array<int, TechnologyUpgradeOpportunity>  $technologyUpgradeOpportunities
     */
    public function __construct(
        public string $url,
        public ?SecurityResult $security = null,
        public ?AccessibilityResult $accessibility = null,
        public ?ContentResult $content = null,
        public ?UiUxResult $uiUx = null,
        public ?PerformanceResult $performance = null,
        public ?BusinessOpportunityResult $businessOpportunity = null,
        public ?TechnologyResult $technology = null,
        public ?SeoAuditResult $seo = null,
        public ?BusinessSignalsResult $businessSignals = null,
        public ?ContactInfoResult $contactInfo = null,
        public ?ReviewPresenceResult $reviewPresence = null,
        public array $technologyUpgradeOpportunities = [],
        public ?ProspectQualificationResult $prospectQualification = null,
        public ?OutreachDraftResult $outreachDraft = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'security' => $this->security?->toArray(),
            'accessibility' => $this->accessibility?->toArray(),
            'content' => $this->content?->toArray(),
            'ui_ux' => $this->uiUx?->toArray(),
            'performance' => $this->performance?->toArray(),
            'business_opportunity' => $this->businessOpportunity?->toArray(),
            'technology' => $this->technology?->toArray(),
            'seo' => $this->seo?->toArray(),
            'business_signals' => $this->businessSignals?->toArray(),
            'contact_info' => $this->contactInfo?->toArray(),
            'review_presence' => $this->reviewPresence?->toArray(),
            'technology_upgrade_opportunities' => array_map(
                static fn (TechnologyUpgradeOpportunity $opportunity): array => $opportunity->toArray(),
                $this->technologyUpgradeOpportunities,
            ),
            'prospect_qualification' => $this->prospectQualification?->toArray(),
            'outreach_draft' => $this->outreachDraft?->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}