<?php

declare(strict_types=1);

namespace App\Audit\Performance;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\ScriptLink;
use App\Audit\Performance\DTO\PerformanceAuditResult;
use App\Audit\Performance\DTO\PerformanceResult;

/**
 * Runs the performance checklist (HTML/CSS/JS/image size, compression,
 * caching, lazy loading, minification, critical CSS, render blocking,
 * DOM size, TTFB, LCP, CLS, FID) against a single crawled page via
 * analyze(), then rolls the results up into an overall score, letter
 * grade, and summary. analyzeAll() runs the same checklist across
 * every successfully crawled page in a CrawlResult and wraps the
 * per-page results in a PerformanceAuditResult, mirroring how
 * SeoAnalyzerService reports site-wide rather than single-page results.
 *
 * Several metrics (compression, caching, lazy loading, minification,
 * critical CSS, LCP, CLS, FID) require data — response headers, raw
 * asset content, or real-browser rendering — that isn't available from
 * a static crawl. Those checks report status 'unknown' and are excluded
 * from scoring rather than penalized.
 */
final class PerformanceAnalyzer
{
    /**
     * CrawledPage does not carry raw HTML/CSS/JS bytes or fetched image
     * sizes (only counts, URLs, and word count) — that data lives one
     * layer down in the fetcher and isn't wired through yet. Until it is,
     * HTML size is approximated from word count and CSS/JS/image size are
     * approximated from resource counts, both clearly unit-labelled below
     * rather than presented as real byte/KB figures.
     */
    public function __construct(
        private readonly int $htmlWordsWarning = 1500,
        private readonly int $htmlWordsCritical = 3000,
        private readonly int $cssFilesWarning = 6,
        private readonly int $cssFilesCritical = 11,
        private readonly int $jsFilesWarning = 9,
        private readonly int $jsFilesCritical = 16,
        private readonly int $imagesWarning = 16,
        private readonly int $imagesCritical = 31,
        private readonly int $renderBlockingWarning = 3,
        private readonly int $renderBlockingCritical = 6,
        private readonly int $domElementsWarning = 150,
        private readonly int $domElementsCritical = 400,
        private readonly int $ttfbWarningMs = 800,
        private readonly int $ttfbCriticalMs = 1800,
        private readonly int $pointsGood = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsCritical = 20,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
        private readonly ?PageSpeedInsightsClient $pageSpeedClient = null,
    ) {
    }

    public function analyze(CrawledPage $page): PerformanceResult
    {
        $metrics = [];

        $pageSpeedMetrics = $this->fetchPageSpeedMetrics($page);

        $metrics['html_size'] = $this->checkHtmlSize($page);
        $metrics['css_size'] = $this->checkCssSize($page);
        $metrics['js_size'] = $this->checkJsSize($page);
        $metrics['image_size'] = $this->checkImageSize($page);
        $metrics['compression'] = $this->checkCompression($page);
        $metrics['caching'] = $this->checkCaching($page);
        $metrics['lazy_load'] = $this->checkLazyLoad($page);
        $metrics['minify'] = $this->checkMinify($page);
        $metrics['critical_css'] = $this->checkCriticalCss($page);
        $metrics['render_blocking'] = $this->checkRenderBlocking($page);
        $metrics['dom_size'] = $this->checkDomSize($page);
        $metrics['ttfb'] = $this->checkTtfb($page);
        $metrics['lcp'] = $this->checkLcp($pageSpeedMetrics);
        $metrics['cls'] = $this->checkCls($pageSpeedMetrics);
        $metrics['fid'] = $this->checkFid($pageSpeedMetrics);

        $score = $this->score($metrics);
        $grade = $this->grade($score);

        return new PerformanceResult(
            url: $page->url,
            score: $score,
            grade: $grade,
            summary: $this->summary($metrics, $score, $grade),
            metrics: $metrics,
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * Runs analyze() over every successfully crawled page in
     * $crawlResult, rather than just the entry page — each page gets
     * its own independent PerformanceResult, since asset weight,
     * render-blocking resources, DOM size, TTFB, and (when PageSpeed
     * Insights is enabled) LCP/CLS/FID can all differ page to page on
     * the same site.
     */
        /**
     * PRODUCTION INCIDENT — read before calling $this->analyze($page)
     * for every page again: this method used to do exactly that, which
     * meant one real PageSpeed Insights HTTP call PER successfully
     * crawled page (up to config('audit.multi_page_analysis.per_page_limit')
     * pages) — each call itself bounded by PageSpeedInsightsClient's
     * own timeout (20s by default), but with NO overall budget across
     * however many pages were being analyzed in this one call. Several
     * pages' worth of PSI calls, run sequentially, could easily exceed
     * App\Audit\Jobs\AnalyzeChunkJob's own overall per-chunk timeout —
     * which doesn't fail gracefully the way a single slow PSI call
     * does (PageSpeedInsightsClient's own docblock: "every failure mode
     * ... collapses to null") but instead kills the ENTIRE chunk job
     * via Illuminate\Queue\TimeoutExceededException, mid-HTTP-request,
     * which on this app's specific host also took the whole
     * `queue:work` worker PROCESS down with it (exit code 137).
     *
     * The fix: only the entry page ($crawlResult->startUrl — the most
     * representative, most valuable Core Web Vitals signal for the
     * site as a whole) gets a real PSI-enriched analysis. Every OTHER
     * page reuses the exact same no-PSI fallback path Quick Scan mode
     * already established (App\Audit\Enums\AuditMode::QUICK — see
     * App\Audit\Jobs\AnalyzeChunkJob's own 'performance' match arm): a
     * fresh PerformanceAnalyzer constructed with pageSpeedClient: null,
     * which makes NO HTTP call at all and returns instantly. This
     * bounds a Full Audit's own worst-case PSI latency to ONE call,
     * regardless of how many pages are being analyzed — matching
     * QUICK mode's own reasoning that PSI is the single slowest step
     * in the whole pipeline, just applied per-audit here instead of
     * per-mode.
     */
    public function analyzeAll(CrawlResult $crawlResult): PerformanceAuditResult
    {
        $successfulPages = array_values(array_filter(
            $crawlResult->pages,
            static fn (CrawledPage $page): bool => $page->success,
        ));

        $noPsiAnalyzer = new self(pageSpeedClient: null);

        $pageResults = [];

        foreach ($successfulPages as $page) {
            $pageResults[$page->url] = $page->url === $crawlResult->startUrl
                ? $this->analyze($page)
                : $noPsiAnalyzer->analyze($page);
        }

        $scoredResults = array_filter(
            $pageResults,
            static fn (PerformanceResult $result): bool => $result->score !== null,
        );

        $averageScore = $scoredResults !== []
            ? (int) round(
                array_sum(array_map(static fn (PerformanceResult $r): int => $r->score, $scoredResults))
                    / count($scoredResults)
            )
            : null;

        return new PerformanceAuditResult(
            startUrl: $crawlResult->startUrl,
            pages: $pageResults,
            pagesAnalyzed: count($pageResults),
            averageScore: $averageScore,
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * @return array{value: int, unit: string, status: string}
     */
    private function checkHtmlSize(CrawledPage $page): array
    {
        $words = $page->wordCount;

        return [
            'value' => $words,
            'unit' => 'words',
            'status' => $this->statusFor($words, $this->htmlWordsWarning, $this->htmlWordsCritical),
        ];
    }

    /**
     * @return array{value: int, unit: string, status: string}
     */
    private function checkCssSize(CrawledPage $page): array
    {
        $count = count($page->cssAssets);

        return [
            'value' => $count,
            'unit' => 'files',
            'status' => $this->statusFor($count, $this->cssFilesWarning, $this->cssFilesCritical),
        ];
    }

    /**
     * @return array{value: int, unit: string, status: string}
     */
    private function checkJsSize(CrawledPage $page): array
    {
        $count = count($page->jsAssets);

        return [
            'value' => $count,
            'unit' => 'files',
            'status' => $this->statusFor($count, $this->jsFilesWarning, $this->jsFilesCritical),
        ];
    }

    /**
     * @return array{value: int, unit: string, status: string}
     */
    private function checkImageSize(CrawledPage $page): array
    {
        $count = count($page->images);

        return [
            'value' => $count,
            'unit' => 'files',
            'status' => $this->statusFor($count, $this->imagesWarning, $this->imagesCritical),
        ];
    }

    /**
     * Not determinable: CrawledPage does not carry response headers
     * (Content-Encoding), only parsed HTML/asset data. Compression can
     * only be read from the raw HTTP response, which isn't wired
     * through to this layer yet.
     *
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function checkCompression(CrawledPage $page): array
    {
        return $this->unknown('Compression cannot be determined: response headers are not available on CrawledPage.');
    }

    /**
     * Not determinable: same reason as compression — Cache-Control/
     * Expires/ETag live in the HTTP response headers, which
     * CrawledPage does not preserve.
     *
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function checkCaching(CrawledPage $page): array
    {
        return $this->unknown('Caching cannot be determined: response headers are not available on CrawledPage.');
    }

    /**
     * Not determinable: ImageAsset does not capture the `loading`
     * attribute (or presence of a lazy-load library/data-src pattern),
     * so it cannot be checked from the current parsed data.
     *
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function checkLazyLoad(CrawledPage $page): array
    {
        return $this->unknown('Lazy loading cannot be determined: the `loading` attribute is not captured on ImageAsset.');
    }

    /**
     * Not determinable: minification requires the raw CSS/JS source
     * text, which is not stored on CrawledPage or its asset DTOs
     * (only URLs and a few attributes are kept).
     *
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function checkMinify(CrawledPage $page): array
    {
        return $this->unknown('Minification cannot be determined: raw CSS/JS content is not available on CrawledPage.');
    }

    /**
     * Not determinable: detecting critical/above-the-fold CSS requires
     * either raw CSS content or an inlined <style> block in the raw
     * HTML, neither of which is preserved on CrawledPage.
     *
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function checkCriticalCss(CrawledPage $page): array
    {
        return $this->unknown('Critical CSS cannot be determined: raw CSS/HTML content is not available on CrawledPage.');
    }

    /**
     * Computable from parsed asset data: a CssLink blocks rendering
     * unless its `media` restricts it away from the default rendering
     * path (e.g. "print"). A ScriptLink blocks rendering unless it is
     * `async`, `defer`, or `type="module"` (deferred by default).
     *
     * @return array{value: int, unit: string, status: string, message: string}
     */
    private function checkRenderBlocking(CrawledPage $page): array
    {
        $blockingCss = array_filter(
            $page->cssAssets,
            static fn (CssLink $css): bool => $css->media === null
                || !in_array(strtolower($css->media), ['print'], true),
        );

        $blockingJs = array_filter(
            $page->jsAssets,
            static fn (ScriptLink $js): bool => !$js->async
                && !$js->defer
                && $js->type !== 'module',
        );

        $count = count($blockingCss) + count($blockingJs);

        return [
            'value' => $count,
            'unit' => 'resources',
            'status' => $this->statusFor($count, $this->renderBlockingWarning, $this->renderBlockingCritical),
            'message' => sprintf(
                '%d render-blocking resource(s): %d CSS, %d JS.',
                $count,
                count($blockingCss),
                count($blockingJs),
            ),
        ];
    }

    /**
     * @return array{value: null, unit: string, status: string, message: string}
     */
    private function unknown(string $message): array
    {
        return [
            'value' => null,
            'unit' => 'n/a',
            'status' => 'unknown',
            'message' => $message,
        ];
    }

    /**
     * Not a true DOM node count: CrawledPage does not preserve the raw
     * HTML/DOM tree, only specific tracked element types (images,
     * anchors, headings, CSS/JS/font links, schema blocks). This sums
     * those as a lightweight structural-complexity proxy — clearly
     * unit-labelled below rather than presented as a real node count.
     *
     * @return array{value: int, unit: string, status: string, message: string}
     */
    private function checkDomSize(CrawledPage $page): array
    {
        $count = count($page->images)
            + count($page->anchors)
            + count($page->headings)
            + count($page->cssAssets)
            + count($page->jsAssets)
            + count($page->fontAssets)
            + count($page->schema);

        return [
            'value' => $count,
            'unit' => 'tracked_elements',
            'status' => $this->statusFor($count, $this->domElementsWarning, $this->domElementsCritical),
            'message' => 'Approximated from tracked element counts (images, links, headings, assets, schema), not a true DOM node count.',
        ];
    }

    /**
     * Approximated from the crawler's total response time, since no
     * separate byte-level TTFB timing (connect vs. first-byte) is
     * captured — only overall responseTimeMs.
     *
     * @return array{value: int|null, unit: string, status: string, message?: string}
     */
    private function checkTtfb(CrawledPage $page): array
    {
        if ($page->responseTimeMs === null) {
            return $this->unknown('TTFB cannot be determined: no response time was recorded for this page.');
        }

        $ms = $page->responseTimeMs;

        return [
            'value' => $ms,
            'unit' => 'ms',
            'status' => $this->statusFor($ms, $this->ttfbWarningMs, $this->ttfbCriticalMs),
            'message' => 'Approximated from total response time, not a byte-level Time to First Byte measurement.',
        ];
    }

    /**
     * Calls PageSpeedInsightsClient at most once per analyze() run
     * (not once per metric) and hands the result to checkLcp/checkCls/
     * checkFid, since all three come from a single PSI API response.
     * Returns null — meaning every LCP/CLS/FID check falls back to its
     * existing unknown() behavior — whenever no client was injected,
     * the feature is disabled via config('audit.pagespeed.enabled'),
     * or the client itself returned null (see PageSpeedInsightsClient's
     * own docblock: it never throws, it returns null on any failure).
     *
     * @return ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float, lcp_resource: ?string, cls_resource: ?string, tbt_resource: ?string}
     */
    private function fetchPageSpeedMetrics(CrawledPage $page): ?array
    {
        if ($this->pageSpeedClient === null || ! config('audit.pagespeed.enabled')) {
            return null;
        }

        return $this->pageSpeedClient->fetch($page->url);
    }

    /**
     * Real value when PageSpeedInsightsClient supplied one (see
     * fetchPageSpeedMetrics); otherwise the previous static-crawl
     * behavior — reporting 'unknown' rather than guessing, since LCP is
     * a real-browser paint-timing metric a static HTTP crawl has no way
     * to derive on its own.
     *
     * @param ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float, lcp_resource: ?string, cls_resource: ?string, tbt_resource: ?string} $pageSpeedMetrics
     * @return array{value: float|null, unit: string, status: string, message?: string, affected_resource?: ?string}
     */
    private function checkLcp(?array $pageSpeedMetrics): array
    {
        $lcpMs = $pageSpeedMetrics['lcp_ms'] ?? null;

        if ($lcpMs === null) {
            return $this->unknown('LCP cannot be determined: it requires real-browser paint timing (e.g. Lighthouse/CrUX), not available from a static crawl.');
        }

        return [
            'value' => $lcpMs,
            'unit' => 'ms',
            // Google's published LCP thresholds: good ≤2500ms, poor >4000ms.
            'status' => $this->statusFor((int) round($lcpMs), 2500, 4000),
            'message' => 'From Google PageSpeed Insights (lab data).',
            'affected_resource' => $pageSpeedMetrics['lcp_resource'] ?? null,
        ];
    }

    /**
     * Real value when PageSpeedInsightsClient supplied one; otherwise
     * the previous static-crawl behavior — CLS is measured by a
     * browser's Layout Instability API during rendering, which cannot
     * be derived from parsed HTML/asset metadata alone.
     *
     * @param ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float, lcp_resource: ?string, cls_resource: ?string, tbt_resource: ?string} $pageSpeedMetrics
     * @return array{value: float|null, unit: string, status: string, message?: string, affected_resource?: ?string}
     */
    private function checkCls(?array $pageSpeedMetrics): array
    {
        $cls = $pageSpeedMetrics['cls'] ?? null;

        if ($cls === null) {
            return $this->unknown('CLS cannot be determined: it requires real-browser layout-shift observation, not available from a static crawl.');
        }

        return [
            'value' => $cls,
            'unit' => 'score',
            // Google's published CLS thresholds: good ≤0.1, poor >0.25.
            // statusFor() takes ints, so this compares CLS*1000 against
            // thresholds scaled the same way, since CLS itself is a
            // small decimal (e.g. 0.08) rather than a millisecond count.
            'status' => $this->statusFor((int) round($cls * 1000), 100, 250),
            'message' => 'From Google PageSpeed Insights (lab data).',
            'affected_resource' => $pageSpeedMetrics['cls_resource'] ?? null,
        ];
    }

    /**
     * PageSpeed Insights v5 no longer reports lab First Input Delay at
     * all, so even with a client configured this can only ever report
     * Total Blocking Time as a proxy — never fabricated as if it were a
     * real FID measurement. The message is explicit about this either
     * way (real-user-only metric that a lab tool can't measure, or a
     * TBT proxy standing in for it) so nothing here is presented as
     * more certain than it is.
     *
     * @param ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float, lcp_resource: ?string, cls_resource: ?string, tbt_resource: ?string} $pageSpeedMetrics
     * @return array{value: float|null, unit: string, status: string, message?: string, affected_resource?: ?string}
     */
    private function checkFid(?array $pageSpeedMetrics): array
    {
        $tbtMs = $pageSpeedMetrics['tbt_ms'] ?? null;

        if ($tbtMs === null) {
            return $this->unknown('FID cannot be determined: it requires real-user interaction timing, not available from a static crawl.');
        }

        return [
            'value' => $tbtMs,
            'unit' => 'ms (Total Blocking Time, used as an FID proxy)',
            // Google's published TBT thresholds: good ≤200ms, poor >600ms
            // (used here as an FID stand-in, not FID's own good/poor
            // cutoffs, since this value is TBT, not FID).
            'status' => $this->statusFor((int) round($tbtMs), 200, 600),
            'message' => 'PageSpeed Insights v5 no longer measures lab FID; this is Total Blocking Time from Google PageSpeed Insights, used as an FID proxy.',
            'affected_resource' => $pageSpeedMetrics['tbt_resource'] ?? null,
        ];
    }

    /**
     * Averages points across every metric whose status was actually
     * determined ('good' | 'warning' | 'critical'). Metrics with an
     * 'unknown' status (no data available from a static crawl) are
     * excluded rather than penalized, since they were never measured.
     *
     * @param array<string, array{status: string}> $metrics
     */
    private function score(array $metrics): ?int
    {
        $completed = array_filter(
            $metrics,
            fn (array $metric): bool => $metric['status'] !== 'unknown',
        );

        if ($completed === []) {
            return null;
        }

        $points = array_map(
            fn (array $metric): int => $this->pointsFor($metric['status']),
            $completed,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(string $status): int
    {
        return match ($status) {
            'good' => $this->pointsGood,
            'warning' => $this->pointsWarning,
            'critical' => $this->pointsCritical,
            default => 0,
        };
    }

    private function grade(?int $score): ?string
    {
        if ($score === null) {
            return null;
        }

        return match (true) {
            $score >= $this->gradeAThreshold => 'A',
            $score >= $this->gradeBThreshold => 'B',
            $score >= $this->gradeCThreshold => 'C',
            $score >= $this->gradeDThreshold => 'D',
            default => 'F',
        };
    }

    /**
     * @param array<string, array{status: string}> $metrics
     */
    private function summary(array $metrics, ?int $score, ?string $grade): string
    {
        $counts = ['good' => 0, 'warning' => 0, 'critical' => 0, 'unknown' => 0];

        foreach ($metrics as $metric) {
            $counts[$metric['status']]++;
        }

        if ($score === null) {
            return sprintf(
                'No performance score could be calculated: all %d checked metric(s) require data that is not available from a static crawl.',
                $counts['unknown'],
            );
        }

        $measured = $counts['good'] + $counts['warning'] + $counts['critical'];

        return sprintf(
            'Performance score %d/100 (grade %s), based on %d measured metric(s): %d good, %d warning, %d critical. '
                . '%d metric(s) could not be determined from a static crawl.',
            $score,
            $grade,
            $measured,
            $counts['good'],
            $counts['warning'],
            $counts['critical'],
            $counts['unknown'],
        );
    }

    private function statusFor(int $value, int $warningThreshold, int $criticalThreshold): string
    {
        if ($value > $criticalThreshold) {
            return 'critical';
        }

        if ($value > $warningThreshold) {
            return 'warning';
        }

        return 'good';
    }
}