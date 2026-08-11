<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Audit\Export\Api\DTO\AuditApiData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON shape for GET /api/audits/{audit} — the JSON Export API.
 *
 * Wraps a single AuditApiData (built by AnalysisResultsToApiData, not
 * by this class) rather than the raw Audit model, so this class stays
 * a thin, one-way mapping from "already-assembled data" to "the API's
 * public JSON contract" — it has no analyzer knowledge and performs no
 * data assembly itself (Single Responsibility). Laravel's default
 * JsonResource wrapping applies (the response is wrapped in a top-level
 * "data" key), which is standard Laravel API practice and needs no
 * extra configuration here.
 *
 * Field names here are the requirement's own category names
 * (seo_analysis, performance_analysis, ...), independent of whatever
 * property names AuditApiData or the underlying analyzer DTOs use —
 * this is the one place that contract is defined, so it can evolve
 * without the mapper or the analyzer DTOs needing to change.
 *
 * Group U adds five Lead Intelligence fields
 * (business_signals/contact_info/review_presence/
 * technology_upgrade_opportunities/prospect_qualification/
 * outreach_draft), passed through unmodified from AuditApiData the
 * same way every field above already is. outreach_draft is a DRAFT for
 * human review only — this API exposes it as data, it does not send it
 * anywhere.
 */
final class AuditResource extends JsonResource
{
    public function __construct(
        private readonly AuditApiData $data,
    ) {
        parent::__construct($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->data->uuid,
            'url' => $this->data->url,
            'status' => $this->data->status,
            'seo_analysis' => $this->data->seoAnalysis,
            'performance_analysis' => $this->data->performanceAnalysis,
            'security_analysis' => $this->data->securityAnalysis,
            'accessibility_analysis' => $this->data->accessibilityAnalysis,
            'ui_ux_analysis' => $this->data->uiUxAnalysis,
            'content_analysis' => $this->data->contentAnalysis,
            'technology_stack' => $this->data->technologyStack,
            'business_analysis' => $this->data->businessAnalysis,
            'scores' => $this->data->scores,
            'recommendations' => $this->data->recommendations,
            'business_signals' => $this->data->businessSignals,
            'contact_info' => $this->data->contactInfo,
            'review_presence' => $this->data->reviewPresence,
            'technology_upgrade_opportunities' => $this->data->technologyUpgradeOpportunities,
            'prospect_qualification' => $this->data->prospectQualification,
            'outreach_draft' => $this->data->outreachDraft,
        ];
    }
}