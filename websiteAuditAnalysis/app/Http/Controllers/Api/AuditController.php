<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Api\Support\AnalysisResultsToApiData;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Models\Audit;

/**
 * JSON Export API for a single audit's results.
 *
 * A separate `Api` namespace from the web AuditController rather than
 * added actions on it: the two controllers serve different clients
 * (browser navigation/redirects vs. a JSON API consumer) and should be
 * free to evolve independently — e.g. API versioning later only
 * touches this namespace, never the web controller.
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

        return new AuditResource($this->apiDataMapper->map($audit, $results));
    }
}
