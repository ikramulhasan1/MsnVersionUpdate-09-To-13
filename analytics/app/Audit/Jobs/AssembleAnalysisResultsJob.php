<?php

declare(strict_types=1);

namespace App\Audit\Jobs;

use App\Audit\Accessibility\DTO\AccessibilityAuditResult;
use App\Audit\Accessibility\DTO\AccessibilityResult;
use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Content\DTO\ContentAuditResult;
use App\Audit\Content\DTO\ContentResult;
use App\Audit\Enums\AuditStatus;
use App\Audit\Enums\BulkAuditBatchStatus;
use App\Audit\Jobs\Concerns\HasAuditUniqueness;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Lead\ProspectQualificationScorer;
use App\Audit\Outreach\DTO\OutreachDraftResult;
use App\Audit\Outreach\OutreachDraftGenerator;
use App\Audit\Performance\DTO\PerformanceAuditResult;
use App\Audit\Performance\DTO\PerformanceResult;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Security\DTO\SecurityAuditResult;
use App\Audit\Security\DTO\SecurityResult;
use App\Audit\Technology\TechnologyUpgradeAnalyzer;
use App\Audit\UiUx\DTO\UiUxAuditResult;
use App\Audit\UiUx\DTO\UiUxResult;
use App\Models\Audit;
use App\Models\BulkAuditBatch;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\TechnologyDetector;
use App\Discovery\Normalization\DomainNormalizer;
use App\Models\DiscoveredWebsite;
use Illuminate\Support\Str;
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

        // Same reasoning as $performance above: the 'security' fragment
        // is now a site-wide SecurityAuditResult (see AnalyzeChunkJob and
        // SecurityAnalyzer::analyzeAll()), and AnalysisResults still
        // carries a single-page SecurityResult, so the entry page's
        // result is pulled back out of the wrapper here too.
        $security = $this->extractEntryPageSecurity($fragments['security'] ?? null);

        // Same reasoning again: the 'accessibility' fragment is now a
        // site-wide AccessibilityAuditResult (see AnalyzeChunkJob and
        // AccessibilityAnalyzer::analyzeAll()), and AnalysisResults still
        // carries a single-page AccessibilityResult.
        $accessibility = $this->extractEntryPageAccessibility($fragments['accessibility'] ?? null);

        // Same reasoning again: the 'content' fragment is now a
        // site-wide ContentAuditResult (see AnalyzeChunkJob and
        // ContentAnalyzer::analyzeAll()), and AnalysisResults still
        // carries a single-page ContentResult. The wrapper's
        // crossPageDuplicates (site-wide duplicate content across pages)
        // has no equivalent field on the single-page ContentResult and
        // is not carried into AnalysisResults here — it remains
        // available from the cached fragment itself for anything that
        // wants it directly.
        $content = $this->extractEntryPageContent($fragments['content'] ?? null);

        // Same reasoning again: the 'ui_ux' fragment is now a site-wide
        // UiUxAuditResult (see AnalyzeChunkJob and
        // UiUxAnalyzer::analyzeAll()), and AnalysisResults still carries
        // a single-page UiUxResult.
        $uiUx = $this->extractEntryPageUiUx($fragments['ui_ux'] ?? null);

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
            security: $security,
            accessibility: $accessibility,
            content: $content,
            uiUx: $uiUx,
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

        $this->updateBulkAuditBatchIfAny($audit, $complete);
        

        // Feature request — see syncToDiscoveredWebsite()'s own
        // docblock. Only for a genuinely COMPLETED audit ($complete):
        // a FAILED audit's $results is a much thinner, partial DTO
        // (whichever analyzer chunks actually made it into the cache
        // before something else failed) — syncing that into Discovery
        // would risk overwriting a real, healthy existing row's own
        // scores with nulls/incomplete data, which is worse than not
        // syncing at all.
        if ($complete) {
            $this->syncToDiscoveredWebsite($audit, $results);
        }
    }

    /**
     * Phase K4 (Bulk Audit) — a no-op for a standalone audit
     * ($audit->bulk_audit_batch_id is null for every audit NOT
     * submitted through App\Audit\Services\BulkAuditBatchService — see
     * App\Models\Audit's own docblock on that column). For one that IS
     * part of a batch, increments the matching counter
     * (completed_count/failed_count — see
     * database/migrations/2026_08_19_000000_create_bulk_audit_batches_table.php's
     * own docblock for why these are denormalized counters rather than
     * computed fresh on every read) and marks the whole batch COMPLETED
     * once every one of its audits has reached a final state.
     *
     * Runs here rather than via a Bus::batch()-style callback because
     * this app's audits are NOT dispatched as a Laravel Batch relative
     * to one another — each audit's own pipeline (FetchAndCrawlJob →
     * its own internal analyzer Batch → this job) is independent, and
     * this job is the one place EVERY audit, bulk or not, always ends
     * up exactly once, whether it completed or failed.
     */
    private function updateBulkAuditBatchIfAny(Audit $audit, bool $complete): void
    {
        if ($audit->bulk_audit_batch_id === null) {
            return;
        }

        $batch = BulkAuditBatch::query()->find($audit->bulk_audit_batch_id);

        if ($batch === null) {
            return;
        }

        $batch->increment($complete ? 'completed_count' : 'failed_count');

        if ($batch->completed_count + $batch->failed_count >= $batch->total_count) {
            $batch->update(['status' => BulkAuditBatchStatus::COMPLETED->value]);
        }
    }
    /**
     * Feature request — keeps App\Models\DiscoveredWebsite in sync with
     * whatever a real audit (single or bulk — this job is the one place
     * every audit's own pipeline converges regardless of how it was
     * submitted, so hooking in here covers both without touching
     * AuditService/BulkAuditBatchService separately) just found for a
     * URL, and never creates a duplicate row for one.
     *
     * MATCHING: uses the exact same normalize-then-hash logic
     * App\Models\DiscoveredWebsite::booted() itself already applies to
     * every row's own url_hash (via App\Discovery\Normalization\DomainNormalizer
     * — the same normalizer App\Discovery\Ingestion\DiscoveryIngestionService
     * already relies on for the SAME "never duplicate a URL" guarantee
     * across Discovery's own external sources), so an audit of
     * "https://example.com/" and a Yelp-discovered "http://www.example.com"
     * are correctly recognized as the SAME site rather than creating a
     * second row.
     *
     * UPDATE vs CREATE: if a matching DiscoveredWebsite already exists,
     * only its score/grade/technology/last_updated_at columns are
     * overwritten — industry/country/city/business_size/... (whatever
     * some OTHER source, e.g. Yelp, already populated) are left
     * completely untouched, since an audit has no signal for any of
     * those at all. A brand new row created here gets `industry` set
     * to the literal string 'Uncategorized' rather than left null —
     * a deliberate choice: every other column CAN honestly stay empty
     * (this source simply doesn't know), but Industry specifically
     * always gets a real, non-null value so a row that started life as
     * a direct audit is never invisible to (or silently excluded from)
     * the Industry filter/dynamic dropdown
     * (App\Discovery\Taxonomy\IndustryTaxonomyService) the way a NULL
     * value already correctly is.
     *
     * Wrapped in its own try/catch — a failure syncing to Discovery
     * must never turn an otherwise-successfully-completed audit into a
     * failed one; this is a side effect of a completed audit, not a
     * required step of completing one.
     */
    private function syncToDiscoveredWebsite(Audit $audit, AnalysisResults $results): void
    {
        try {
            $normalizer = new DomainNormalizer();
            $hash = $normalizer->hash($audit->url);
            $host = parse_url($audit->url, PHP_URL_HOST);
            $domain = is_string($host) && $host !== '' ? $host : $audit->url;

            $columns = [
                'seo_score' => $results->seo?->averageScore,
                'seo_grade' => $results->seo !== null ? $this->seoGradeFor($results->seo->averageScore) : null,
                'performance_score' => $results->performance?->score,
                'performance_grade' => $results->performance?->grade,
                'security_score' => $results->security?->score,
                'security_grade' => $results->security?->grade,
                'accessibility_score' => $results->accessibility?->score,
                'accessibility_grade' => $results->accessibility?->grade,
                'last_updated_at' => now(),
            ];

            if ($results->technology !== null) {
                $columns['cms'] = $this->technologyColumnValue($results->technology, ['CMS']);
                $columns['framework'] = $this->technologyColumnValue(
                    $results->technology,
                    ['Backend Framework', 'JavaScript Framework', 'CSS Framework'],
                );
                $columns['ecommerce_platform'] = $this->technologyColumnValue($results->technology, ['Ecommerce']);
                $columns['server'] = $results->technology->serverHeader;
                $columns['cdn'] = $this->technologyColumnValue($results->technology, ['Infrastructure']);
            }

            $existing = DiscoveredWebsite::query()->where('url_hash', $hash)->first();

            if ($existing !== null) {
                $existing->update($columns);

                return;
            }

            DiscoveredWebsite::query()->create(array_merge($columns, [
                'uuid' => (string) Str::uuid(),
                'domain' => $domain,
                'url' => $audit->url,
                // See this method's own docblock for why this specific
                // column always gets a real value rather than staying
                // null like every other one here.
                'industry' => 'Uncategorized',
                'discovery_source' => 'audit',
                'discovered_at' => now(),
            ]));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * SeoAuditResult has no grade of its own (unlike Security/
     * Accessibility/Performance, whose analyzers already compute one)
     * — mirrors the exact A/B/C/D/F thresholds
     * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own gradeFor()
     * already uses for the identical purpose, so an SEO grade written
     * here means the same thing as one written by that job.
     */
    private function seoGradeFor(int $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 40 => 'D',
            default => 'F',
        };
    }

    /**
     * Copied from (not shared with, via a trait, across a Discovery <->
     * Audit namespace boundary) App\Discovery\Jobs\Concerns\BuildsSinglePageCrawlResult's
     * own technologyColumnValue() — identical logic, kept as its own
     * small, self-contained private method here rather than pulling in
     * that trait's other, unrelated methods (crawledPageFrom(),
     * singlePageCrawlResult()) that this job has no use for.
     */
    private function technologyColumnValue(TechnologyResult $technology, array $categories): ?string
    {
        $names = [];

        foreach (TechnologyDetector::CATEGORY_MAP as $slug => $category) {
            if (! in_array($category, $categories, true)) {
                continue;
            }

            $detection = $technology->detections[$slug] ?? null;

            if ($detection !== null && $detection->detected) {
                $names[] = TechnologyDetector::TECHNOLOGY_NAMES[$slug] ?? ucfirst($slug);
            }
        }

        return $names === [] ? null : implode(', ', $names);
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

    /**
     * Same reasoning as extractEntryPagePerformance() above, for the
     * site-wide SecurityAuditResult now cached under the 'security'
     * fragment key.
     */
    private function extractEntryPageSecurity(?object $fragment): ?SecurityResult
    {
        if (! $fragment instanceof SecurityAuditResult) {
            return null;
        }

        if (isset($fragment->pages[$this->url])) {
            return $fragment->pages[$this->url];
        }

        return $fragment->pages === [] ? null : reset($fragment->pages);
    }

    /**
     * Same reasoning again, for the site-wide AccessibilityAuditResult
     * now cached under the 'accessibility' fragment key.
     */
    private function extractEntryPageAccessibility(?object $fragment): ?AccessibilityResult
    {
        if (! $fragment instanceof AccessibilityAuditResult) {
            return null;
        }

        if (isset($fragment->pages[$this->url])) {
            return $fragment->pages[$this->url];
        }

        return $fragment->pages === [] ? null : reset($fragment->pages);
    }

    /**
     * Same reasoning again, for the site-wide ContentAuditResult now
     * cached under the 'content' fragment key.
     */
    private function extractEntryPageContent(?object $fragment): ?ContentResult
    {
        if (! $fragment instanceof ContentAuditResult) {
            return null;
        }

        if (isset($fragment->pages[$this->url])) {
            return $fragment->pages[$this->url];
        }

        return $fragment->pages === [] ? null : reset($fragment->pages);
    }

    /**
     * Same reasoning again, for the site-wide UiUxAuditResult now
     * cached under the 'ui_ux' fragment key.
     */
    private function extractEntryPageUiUx(?object $fragment): ?UiUxResult
    {
        if (! $fragment instanceof UiUxAuditResult) {
            return null;
        }

        if (isset($fragment->pages[$this->url])) {
            return $fragment->pages[$this->url];
        }

        return $fragment->pages === [] ? null : reset($fragment->pages);
    }
}
