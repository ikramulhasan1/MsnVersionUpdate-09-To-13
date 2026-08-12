<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Enums\AuditStatus;
use App\Audit\Jobs\Concerns\HasAuditUniqueness;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Lead\ProspectQualificationScorer;
use App\Audit\Outreach\DTO\OutreachDraftResult;
use App\Audit\Outreach\OutreachDraftGenerator;
use App\Audit\Performance\DTO\PerformanceAuditResult;
use App\Audit\Performance\DTO\PerformanceResult;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Technology\TechnologyUpgradeAnalyzer;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Throwable;

/**
 * Runs once per audit after the analyzer batch finishes (successfully
 * or not — see FetchAndCrawlJob's use of finally() over then()).
 * Reads back whatever analyzer fragments AnalyzeChunkJob managed to
 * write, builds the single AnalysisResults DTO the rest of the app
 * already expects (PDF export, JSON API), and clears the fragments
 * once they're folded in, so a completed audit doesn't leave its
 * per-analyzer intermediate cache entries around using memory.
 */
final class AssembleAnalysisResultsJob extends AuditJob implements ShouldBeUnique
{
    use HasAuditUniqueness;

    public function __construct(string $auditUuid, private readonly string $url)
    {
        parent::__construct();

        $this->auditUuid = $auditUuid;
    }

    /**
     * Only reads cached fragments and writes one DB row — no HTTP
     * work — so this is the tightest timeout in the pipeline.
     */
    protected function defaultTimeoutSeconds(): int
    {
        return (int) config('audit.queue.assemble_timeout_seconds', 30);
    }

    public function handle(
        AuditRepositoryInterface $auditRepository,
        AuditCacheServiceInterface $cache,
        AIRecommendationEngine $recommendationEngine,
        TechnologyUpgradeAnalyzer $technologyUpgradeAnalyzer,
        ProspectQualificationScorer $prospectQualificationScorer,
        OutreachDraftGenerator $outreachDraftGenerator,
    ): void {
        $audit = $auditRepository->findByUuid($this->auditUuid);

        if ($audit === null || $audit->status->isFinished()) {
            return;
        }

        $auditRepository->updateStatus($audit, AuditStatus::GENERATING_REPORT);
        $cache->putProgress($this->auditUuid, 90, 'Generating report…');

        $fragments = $cache->getAnalysisFragments($this->auditUuid);

        $technology = $fragments['technology'] ?? null;

        // The 'performance' fragment is now a site-wide PerformanceAuditResult
        // (see AnalyzeChunkJob and PerformanceAnalyzer::analyzeAll()), keyed
        // by page URL. AnalysisResults still carries a single-page
        // PerformanceResult (like every other per-page analyzer property
        // here, matching FetchResult's single-page scope) so the entry
        // page's result is pulled back out of the wrapper below — the
        // richer multi-page data remains available from the cached
        // fragment itself for anything that wants it directly.
        $performance = $this->extractEntryPagePerformance($fragments['performance'] ?? null);

        $technologyUpgradeOpportunities = [];

        if ($technology !== null) {
            try {
                $technologyUpgradeOpportunities = $technologyUpgradeAnalyzer->analyze($technology);
            } catch (Throwable $e) {
                // Same reasoning as the AI Recommendation Engine guard
                // below: a value-add derived from an already-successful
                // analyzer result, not a required analyzer fragment
                // itself — a failure here shouldn't sink an otherwise
                // successful audit, just report it and move on.
                report($e);
            }
        }

        $results = new AnalysisResults(
            url: $this->url,
            security: $fragments['security'] ?? null,
            accessibility: $fragments['accessibility'] ?? null,
            content: $fragments['content'] ?? null,
            uiUx: $fragments['ui_ux'] ?? null,
            performance: $performance,
            businessOpportunity: $fragments['business_opportunity'] ?? null,
            technology: $technology,
            seo: $fragments['seo'] ?? null,
            businessSignals: $fragments['business_signals'] ?? null,
            contactInfo: $fragments['contact_info'] ?? null,
            reviewPresence: $fragments['review_presence'] ?? null,
            technologyUpgradeOpportunities: $technologyUpgradeOpportunities,
        );

        $prospectQualification = null;

        try {
            $prospectQualification = $prospectQualificationScorer->score($results);
        } catch (Throwable $e) {
            // Same reasoning as the technology-upgrade guard above: a
            // value-add derived from already-successful analyzer
            // results, not a required analyzer fragment itself — a
            // failure here shouldn't sink an otherwise successful
            // audit, just report it and move on.
            report($e);
        }

        if ($prospectQualification instanceof ProspectQualificationResult) {
            // AnalysisResults is immutable — rebuild it once more with
            // the now-known qualification score rather than mutating
            // the instance above, which the rest of this method (and
            // the recommendation engine call below) still relies on
            // being the single source of truth once this point is reached.
            $results = new AnalysisResults(
                url: $results->url,
                security: $results->security,
                accessibility: $results->accessibility,
                content: $results->content,
                uiUx: $results->uiUx,
                performance: $results->performance,
                businessOpportunity: $results->businessOpportunity,
                technology: $results->technology,
                seo: $results->seo,
                businessSignals: $results->businessSignals,
                contactInfo: $results->contactInfo,
                reviewPresence: $results->reviewPresence,
                technologyUpgradeOpportunities: $results->technologyUpgradeOpportunities,
                prospectQualification: $prospectQualification,
            );
        }

        $outreachDraft = null;

        try {
            $outreachDraft = $outreachDraftGenerator->generate($results, $prospectQualification);
        } catch (Throwable $e) {
            // Same reasoning as the technology-upgrade and prospect-
            // qualification guards above: a value-add derived from
            // already-successful analyzer results, not a required
            // analyzer fragment itself — a failure here shouldn't sink
            // an otherwise successful audit, just report it and move on.
            report($e);
        }

        if ($outreachDraft instanceof OutreachDraftResult) {
            // Same immutable-rebuild reasoning as the prospectQualification
            // block above — $results stays the single source of truth for
            // the recommendation engine call and cache write below.
            $results = new AnalysisResults(
                url: $results->url,
                security: $results->security,
                accessibility: $results->accessibility,
                content: $results->content,
                uiUx: $results->uiUx,
                performance: $results->performance,
                businessOpportunity: $results->businessOpportunity,
                technology: $results->technology,
                seo: $results->seo,
                businessSignals: $results->businessSignals,
                contactInfo: $results->contactInfo,
                reviewPresence: $results->reviewPresence,
                technologyUpgradeOpportunities: $results->technologyUpgradeOpportunities,
                prospectQualification: $results->prospectQualification,
                outreachDraft: $outreachDraft,
            );
        }

        $cache->putAnalysisResults($this->auditUuid, $results);

        try {
            $recommendationResult = $recommendationEngine->analyze($results);

            $cache->putRecommendations($this->auditUuid, $recommendationResult);
        } catch (Throwable $e) {
            // The AI Recommendation Engine is a value-add on top of the
            // analyzer results, not a required analyzer fragment itself
            // — a failure here (e.g. an unexpected shape in $results)
            // should not sink an otherwise-successful audit. It's still
            // reported so the failure isn't silently lost.
            report($e);
        }

        $cache->forgetFragments($this->auditUuid);

        $complete = count(array_intersect(AnalyzeChunkJob::ANALYZER_KEYS, array_keys($fragments)))
            === count(AnalyzeChunkJob::ANALYZER_KEYS);

        $auditRepository->updateStatus($audit, $complete ? AuditStatus::COMPLETED : AuditStatus::FAILED);
        $cache->putProgress(
            $this->auditUuid,
            100,
            $complete ? 'Done.' : 'Audit failed.',
        );
    }

    public function failed(Throwable $e): void
    {
        $this->markAuditFailedIfNotFinished($e);
    }

    /**
     * Pulls the entry page's (i.e. $this->url's) PerformanceResult back
     * out of the site-wide PerformanceAuditResult cached under the
     * 'performance' fragment key. Falls back to the first available page
     * result if the entry page itself isn't in the map for some reason
     * (e.g. it redirected and got crawled under its final URL instead),
     * so AnalysisResults still gets a representative result rather than
     * null whenever the audit clearly did produce performance data.
     */
    private function extractEntryPagePerformance(?object $fragment): ?PerformanceResult
    {
        if (! $fragment instanceof PerformanceAuditResult) {
            return null;
        }

        if (isset($fragment->pages[$this->url])) {
            return $fragment->pages[$this->url];
        }

        return $fragment->pages === [] ? null : reset($fragment->pages);
    }
}