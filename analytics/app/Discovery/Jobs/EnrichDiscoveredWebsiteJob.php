<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Audit\Accessibility\AccessibilityAnalyzer;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\TechnologyDetector;
use App\Models\DiscoveredWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * The bridge between Website Discovery and the existing Audit engine —
 * runs the same five analyzers a real audit uses
 * (SeoAnalyzerServiceInterface, PerformanceAnalyzer, SecurityAnalyzer,
 * AccessibilityAnalyzer, TechnologyDetector), completely unmodified,
 * against ONLY the homepage — never the full multi-page crawl,
 * PDF/Excel export, or any of the other Audit-pipeline jobs
 * (FetchAndCrawlJob/AnalyzeChunkJob/AssembleAnalysisResultsJob) a real
 * audit runs. A discovered site's row needs just enough signal to be
 * searchable/filterable by score, grade, and technology — not a full
 * audit report — so this job is deliberately a lightweight, one-page,
 * one-job "quick scan" instead of reusing the full pipeline.
 *
 * Writes ONLY score + grade (seo/performance/security/accessibility)
 * and technology stack (cms/framework/ecommerce_platform/server/cdn)
 * onto the DiscoveredWebsite row — no other column (website_type,
 * business_size, country/region/city, contact info, domain age, ...)
 * is touched here; those belong to other enrichment steps this job
 * doesn't attempt. mobile_score/opportunity_score also stay untouched:
 * no analyzer this job runs produces either as a distinct value.
 *
 * A future Phase I3 (App\Discovery\Sources\InternalCrawlSource, not
 * built yet) is expected to dispatch this job automatically whenever
 * it discovers a new domain; nothing dispatches it automatically today
 * — call EnrichDiscoveredWebsiteJob::dispatch($website) directly (e.g.
 * from a controller action or artisan command) until that source
 * exists.
 */
final class EnrichDiscoveredWebsiteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly DiscoveredWebsite $website,
    ) {
    }

    public function handle(
        WebsiteFetcherServiceInterface $fetcher,
        SeoAnalyzerServiceInterface $seoAnalyzer,
        PerformanceAnalyzer $performanceAnalyzer,
        SecurityAnalyzer $securityAnalyzer,
        AccessibilityAnalyzer $accessibilityAnalyzer,
        TechnologyDetector $technologyDetector,
    ): void {
        $fetch = $fetcher->fetch($this->website->url);

        if (! $fetch->success) {
            // A failed homepage fetch has nothing to score — leave the
            // row exactly as it was rather than writing zeros/nulls
            // over any signal a previous successful enrichment already
            // found. A future WebsiteConnectivityStatus-backed column
            // (see that enum's own docblock) is the right place to
            // record "we tried and it failed", not this job silently
            // clobbering real data.
            return;
        }

        $page = $this->crawledPageFrom($fetch);

        $seoResult = $seoAnalyzer->analyze($this->singlePageCrawlResult($fetch, $page));
        $performanceResult = $performanceAnalyzer->analyze($page);
        $securityResult = $securityAnalyzer->analyze($fetch);
        $accessibilityResult = $accessibilityAnalyzer->analyze($fetch);
        $technologyResult = $technologyDetector->detect($fetch);

        $this->website->update([
            'seo_score' => $seoResult->averageScore,
            'seo_grade' => $this->gradeFor($seoResult->averageScore),
            'performance_score' => $performanceResult->score,
            'performance_grade' => $performanceResult->grade,
            'security_score' => $securityResult->score,
            'security_grade' => $securityResult->grade,
            'accessibility_score' => $accessibilityResult->score,
            'accessibility_grade' => $accessibilityResult->grade,
            'cms' => $this->technologyColumnValue($technologyResult, ['CMS']),
            'framework' => $this->technologyColumnValue(
                $technologyResult,
                ['Backend Framework', 'JavaScript Framework', 'CSS Framework'],
            ),
            'ecommerce_platform' => $this->technologyColumnValue($technologyResult, ['Ecommerce']),
            'server' => $technologyResult->serverHeader,
            'cdn' => $this->technologyColumnValue($technologyResult, ['Infrastructure']),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }

    /**
     * Converts a single FetchResult into the CrawledPage shape the
     * other four analyzers (and this method's own singlePageCrawlResult())
     * expect — the exact same field-by-field mapping
     * AIRecommendationEngineTest already uses for the same purpose, so
     * a homepage-only FetchResult is represented identically everywhere
     * in this codebase that needs to hand one to a CrawledPage-shaped
     * API. internalLinkUrls/externalLinkUrls are left empty: this job
     * never crawls beyond the homepage, so there is nothing beyond it
     * to enumerate links toward.
     */
    private function crawledPageFrom(FetchResult $fetch): CrawledPage
    {
        return new CrawledPage(
            url: $fetch->url,
            depth: 0,
            success: $fetch->success,
            finalUrl: $fetch->finalUrl,
            statusCode: $fetch->statusCode,
            redirectChain: $fetch->redirectChain,
            meta: $fetch->meta,
            title: $fetch->meta?->title,
            canonical: $fetch->meta?->canonical,
            noIndex: false,
            noFollow: false,
            anchors: $fetch->anchors,
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: $fetch->images,
            cssAssets: $fetch->cssLinks,
            jsAssets: $fetch->jsLinks,
            fontAssets: $fetch->fonts,
            headings: $fetch->headings,
            schema: $fetch->schema,
            wordCount: $fetch->wordCount,
            responseTimeMs: $fetch->responseTimeMs,
            errors: $fetch->errors,
        );
    }

    /**
     * SeoAnalyzerServiceInterface::analyze() takes a full CrawlResult
     * (built for a multi-page crawl), not a single FetchResult — this
     * builds the smallest valid CrawlResult that represents exactly
     * one page (the homepage) and nothing else: $pages holds only
     * $page, and $internalPages/$externalLinks/$brokenLinks are all
     * empty, since this job never crawls beyond the homepage to
     * discover any of those. This keeps every SEO check that only
     * looks at $page->issues (title, description, headings, alt text,
     * schema, ...) working exactly as it would in a real audit, while
     * site-wide checks that need more than one page (e.g. a true
     * broken-link inventory) simply have nothing to report — which is
     * correct for a homepage-only "quick scan", not a bug.
     */
    private function singlePageCrawlResult(FetchResult $fetch, CrawledPage $page): CrawlResult
    {
        return new CrawlResult(
            startUrl: $fetch->url,
            origin: parse_url($fetch->url, PHP_URL_HOST) ?: null,
            pages: [$page],
            internalPages: [],
            externalLinks: [],
            brokenLinks: [],
            maxDepth: 0,
            maxPages: 1,
            truncated: false,
            durationMs: $fetch->responseTimeMs,
            crawledAt: now()->toAtomString(),
        );
    }

    /**
     * SeoAuditResult has no grade of its own (unlike Security/
     * Accessibility/Performance, whose analyzers already compute one) —
     * mirrors the exact A/B/C/D/F thresholds every one of those
     * analyzers' own grade() methods already use by default
     * (gradeAThreshold=90/gradeBThreshold=75/gradeCThreshold=60/
     * gradeDThreshold=40), so an SEO grade means the same thing as a
     * Security/Accessibility/Performance grade at the same score.
     */
    private function gradeFor(int $score): string
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
     * Every detected technology whose TechnologyDetector::CATEGORY_MAP
     * category is in $categories, mapped to its display name via
     * TechnologyDetector::TECHNOLOGY_NAMES and joined with ", " — the
     * exact same public vocabulary
     * App\Discovery\Taxonomy\TechnologyFilterOptions already reuses for
     * the Technology filter's checkbox options (Phase C2), so a
     * discovered site's cms/framework/ecommerce_platform/cdn columns
     * are always expressed in terms a search against those same
     * filters can actually match. Null when nothing in $categories was
     * detected, never an empty string.
     *
     * TechnologyResult::$detections holds one TechnologyDetectionResult
     * per known slug ALWAYS — including every non-match, each with its
     * own ->detected === false — not only the slugs that were actually
     * found; this filters on ->detected explicitly rather than assuming
     * array-key presence alone means "detected" (it doesn't).
     *
     * @param array<int, string> $categories
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
}