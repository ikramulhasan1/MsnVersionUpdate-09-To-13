<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Enums\AuditStatus;
use App\Audit\Export\Api\Support\AnalysisResultsToApiData;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Models\Audit;

/**
 * JSON Export API (v1) for a single audit's results.
 *
 * Namespaced `Api\V1` — not just a `/v1` route prefix — so a future v2
 * can add its own controller alongside this one without touching it
 * (Open/Closed): this class is the version-1 contract, frozen once
 * clients depend on it, exactly the point of versioning.
 *
 * Deliberately reuses AuditResource and AnalysisResultsToApiData
 * unchanged from the unversioned endpoint (Prompt 18.1) rather than
 * duplicating or re-namespacing them under `V1` — this requirement
 * explicitly asks to keep the existing API Resource architecture, and
 * a Resource/mapper pair with no version-specific behavior has no
 * reason to be duplicated per version. Only the URL, and this
 * controller's namespace, are versioned.
 */
final class AuditController extends Controller
{
    public function __construct(
        private readonly AnalysisResultsToApiData $apiDataMapper,
    ) {
    }

    public function show(Audit $audit): AuditResource
    {
        // TODO: pass the real AnalysisResults (and, once the AI
        // Recommendation Engine has run, AIRecommendationResult) for this
        // audit once the analyzer/scoring pipeline is wired up — see the
        // matching TODO in AuditController@export (PDF) and
        // resources/views/audit/result.blade.php. Until then, every
        // section below is null (or an empty scores list) rather than
        // fabricated, and will populate automatically once that
        // pipeline supplies real data here.
        $results = new AnalysisResults(url: $audit->url);

        $data = $this->apiDataMapper->map($audit, $results);

        // "Handle missing or unavailable audit data safely": a missing
        // audit is a 404 (see routes/api.php's ->missing() handler,
        // resolved before this action ever runs); an audit that exists
        // but hasn't finished analyzing yet is not an error — it's a
        // normal 200 response whose analysis sections are null (see
        // AuditApiData) and whose meta.analysis_complete says why.
        return (new AuditResource($data))->additional([
            'meta' => [
                'analysis_complete' => $audit->status === AuditStatus::COMPLETED,
                'audit_status' => $audit->status->value,
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
