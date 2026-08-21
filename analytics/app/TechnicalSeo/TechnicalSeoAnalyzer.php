<?php

declare(strict_types=1);

namespace App\TechnicalSeo;

use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Performance\DTO\PerformanceAuditResult;
use App\Audit\Seo\DTO\PageSeoResult;
use App\Audit\Seo\DTO\SeoAuditResult;
use App\Audit\Seo\DTO\SeoIssue;
use App\TechnicalSeo\DTO\TechnicalSeoIssue;
use App\TechnicalSeo\DTO\TechnicalSeoResult;
use Illuminate\Support\Facades\Http;

/**
 * Phase R2 (Technical SEO Audit) — reuses this app's own EXISTING
 * multi-page crawl engine (App\Audit\Crawler\WebsiteCrawlerService,
 * called by App\TechnicalSeo\Jobs\RunTechnicalSeoScanJob, not this
 * class directly) and EXISTING PageSpeed Insights integration
 * (App\Audit\Performance\PerformanceAnalyzer::analyzeAll(), likewise
 * called by the job before this class ever runs) — this class writes
 * NO new crawling or PageSpeed-calling code, only NEW ANALYSIS on top
 * of a CrawlResult and PerformanceAuditResult that already exist by
 * the time analyze() is called.
 *
 * robots.txt and the XML sitemap are the two things this class DOES
 * fetch itself (via analyzeRobotsTxt()/analyzeSitemap() below) — the
 * existing App\Audit\Fetching\DTO\FetchResult only records whether
 * those well-known URLs EXIST (a boolean + status code), never their
 * actual body content, since nothing before this phase needed to
 * parse robots.txt rules or count sitemap URLs.
 */
final class TechnicalSeoAnalyzer
{
    public function analyze(string $domain, CrawlResult $crawl, PerformanceAuditResult $performance): TechnicalSeoResult
    {
        $issues = [];

        $robotsTxt = $this->analyzeRobotsTxt($domain, $issues);
        $sitemap = $this->analyzeSitemap($domain, $crawl, $issues);
        $brokenLinks = $this->analyzeBrokenLinks($crawl, $issues);
        $redirects = $this->analyzeRedirects($crawl, $issues);
        $indexability = $this->analyzeIndexability($crawl, $issues);
        $coreWebVitals = $this->analyzeCoreWebVitals($performance, $issues);
        $mobileFriendliness = $this->analyzeMobileFriendliness($crawl, $issues);
        $security = $this->analyzeSecurity($domain, $crawl, $issues);
        $crawlDepth = $this->analyzeCrawlDepth($crawl, $issues);
        $hreflang = $this->analyzeHreflang($crawl, $issues);
        $structuredData = $this->analyzeStructuredData($crawl, $issues);

        [$score, $grade] = $this->healthScore($issues);

        return new TechnicalSeoResult(
            domain: $domain,
            pagesCrawled: count($crawl->pages),
            robotsTxt: $robotsTxt,
            sitemap: $sitemap,
            brokenLinks: $brokenLinks,
            redirects: $redirects,
            indexability: $indexability,
            coreWebVitals: $coreWebVitals,
            mobileFriendliness: $mobileFriendliness,
            security: $security,
            crawlDepth: $crawlDepth,
            hreflang: $hreflang,
            structuredData: $structuredData,
            issues: $issues,
            healthScore: $score,
            healthGrade: $grade,
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeRobotsTxt(string $domain, array &$issues): array
    {
        try {
            $response = Http::timeout(10)->get("https://{$domain}/robots.txt");
        } catch (\Throwable) {
            $response = null;
        }

        if ($response === null || ! $response->successful()) {
            $issues[] = new TechnicalSeoIssue('robots_txt', 'notice', 'No robots.txt found.', 'A robots.txt is optional but recommended for controlling crawler access.');

            return ['exists' => false, 'rules' => [], 'blocks_critical_pages' => false];
        }

        $lines = array_values(array_filter(array_map('trim', explode("\n", $response->body()))));
        $disallowRules = [];

        foreach ($lines as $line) {
            if (preg_match('/^disallow:\s*(.+)$/i', $line, $matches)) {
                $disallowRules[] = trim($matches[1]);
            }
        }

        $blocksRoot = in_array('/', $disallowRules, true);

        if ($blocksRoot) {
            $issues[] = new TechnicalSeoIssue('robots_txt', 'critical', 'robots.txt disallows the entire site ("Disallow: /").', 'Remove this rule unless you genuinely want the whole site excluded from search engines.');
        }

        return [
            'exists' => true,
            'rules' => $lines,
            'blocks_critical_pages' => $blocksRoot,
        ];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeSitemap(string $domain, CrawlResult $crawl, array &$issues): array
    {
        try {
            $response = Http::timeout(10)->get("https://{$domain}/sitemap.xml");
        } catch (\Throwable) {
            $response = null;
        }

        if ($response === null || ! $response->successful()) {
            $issues[] = new TechnicalSeoIssue('sitemap', 'warning', 'No sitemap.xml found at the standard location.', 'Add an XML sitemap and reference it in robots.txt to help search engines discover your pages.');

            return ['exists' => false, 'is_valid_xml' => false, 'url_count' => 0, 'crawled_page_count' => count($crawl->pages), 'mismatch' => null];
        }

        $isValidXml = @simplexml_load_string($response->body()) !== false;
        $urlCount = $isValidXml ? substr_count($response->body(), '<loc>') : 0;
        $crawledCount = count($crawl->pages);
        $mismatch = abs($urlCount - $crawledCount);

        if (! $isValidXml) {
            $issues[] = new TechnicalSeoIssue('sitemap', 'critical', 'sitemap.xml exists but is not valid XML.', 'Fix the XML syntax — an invalid sitemap is ignored by search engines entirely.');
        } elseif ($mismatch > max(5, (int) ($crawledCount * 0.2))) {
            $issues[] = new TechnicalSeoIssue('sitemap', 'notice', "Sitemap lists {$urlCount} URLs, but {$crawledCount} pages were actually found while crawling.", 'A large mismatch can mean the sitemap is stale, or the site has pages not reachable via internal links.');
        }

        return [
            'exists' => true,
            'is_valid_xml' => $isValidXml,
            'url_count' => $urlCount,
            'crawled_page_count' => $crawledCount,
            'mismatch' => $mismatch,
        ];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeBrokenLinks(CrawlResult $crawl, array &$issues): array
    {
        $broken = array_map(static fn ($entry): array => [
            'url' => $entry->url,
            'status_code' => $entry->statusCode,
            'found_on_pages' => $entry->foundOnPages,
        ], $crawl->brokenLinks);

        if ($broken !== []) {
            $count = count($broken);
            $issues[] = new TechnicalSeoIssue('broken_links', 'warning', "{$count} broken link(s) found.", 'Fix or remove links to pages that no longer exist — broken links waste crawl budget and hurt user experience.');
        }

        return ['count' => count($broken), 'links' => $broken];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeRedirects(CrawlResult $crawl, array &$issues): array
    {
        $chains = [];
        $loops = [];

        foreach ($crawl->pages as $page) {
            if (count($page->redirectChain) > 1) {
                $isLoop = count(array_unique($page->redirectChain)) < count($page->redirectChain);

                $entry = ['url' => $page->url, 'chain' => $page->redirectChain, 'hops' => count($page->redirectChain) - 1];

                if ($isLoop) {
                    $loops[] = $entry;
                } else {
                    $chains[] = $entry;
                }
            }
        }

        if ($loops !== []) {
            $issues[] = new TechnicalSeoIssue('redirects', 'critical', count($loops).' redirect loop(s) detected.', 'A redirect loop makes a page completely unreachable — fix the redirect rules immediately.');
        }

        if ($chains !== []) {
            $issues[] = new TechnicalSeoIssue('redirects', 'notice', count($chains).' page(s) have multi-hop redirect chains.', 'Point links directly at the final destination URL instead of chaining through multiple redirects.');
        }

        return ['chains' => $chains, 'loops' => $loops];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeIndexability(CrawlResult $crawl, array &$issues): array
    {
        $noindexPages = [];
        $canonicalMismatches = [];

        foreach ($crawl->pages as $page) {
            if ($page->noIndex) {
                $noindexPages[] = $page->url;
            }

            if ($page->canonical !== null && rtrim($page->canonical, '/') !== rtrim($page->url, '/')) {
                $canonicalMismatches[] = ['url' => $page->url, 'canonical' => $page->canonical];
            }
        }

        if ($noindexPages !== []) {
            $count = count($noindexPages);
            $issues[] = new TechnicalSeoIssue('indexability', 'notice', "{$count} page(s) are marked noindex.", 'Confirm this is intentional — a noindex page will never appear in search results.');
        }

        return ['noindex_pages' => $noindexPages, 'canonical_mismatches' => $canonicalMismatches];
    }

    /**
     * Reads directly from the ALREADY-COMPLETED PerformanceAuditResult
     * (see this class's own docblock — the job that calls this class
     * already ran PerformanceAnalyzer::analyzeAll() before analyze()
     * is ever invoked) — makes no PageSpeed API call itself.
     *
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeCoreWebVitals(PerformanceAuditResult $performance, array &$issues): array
    {
        $pages = [];

        foreach ($performance->pages as $url => $result) {
            $pages[] = [
                'url' => $url,
                'score' => $result->score,
                'grade' => $result->grade,
                'lcp' => $result->metrics['lcp'] ?? null,
                'cls' => $result->metrics['cls'] ?? null,
                'fid' => $result->metrics['fid'] ?? null,
            ];
        }

        if ($performance->averageScore !== null && $performance->averageScore < 50) {
            $issues[] = new TechnicalSeoIssue('core_web_vitals', 'warning', "Average performance score is only {$performance->averageScore}/100.", 'Core Web Vitals are a real Google ranking factor — see the per-page breakdown for the biggest offenders.');
        }

        return ['average_score' => $performance->averageScore, 'pages' => $pages];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeMobileFriendliness(CrawlResult $crawl, array &$issues): array
    {
        $missingViewport = [];

        foreach ($crawl->pages as $page) {
            if ($page->meta?->viewport === null) {
                $missingViewport[] = $page->url;
            }
        }

        if ($missingViewport !== []) {
            $count = count($missingViewport);
            $issues[] = new TechnicalSeoIssue('mobile_friendliness', 'warning', "{$count} page(s) have no viewport meta tag.", 'Add <meta name="viewport" content="width=device-width, initial-scale=1"> — Google uses mobile-first indexing.');
        }

        return ['pages_missing_viewport' => $missingViewport, 'pages_checked' => count($crawl->pages)];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeSecurity(string $domain, CrawlResult $crawl, array &$issues): array
    {
        $mixedContentPages = [];

        foreach ($crawl->pages as $page) {
            $hasMixedContent = false;

            foreach (array_merge($page->cssAssets, $page->jsAssets) as $asset) {
                if (isset($asset->url) && str_starts_with($asset->url, 'http://')) {
                    $hasMixedContent = true;

                    break;
                }
            }

            if ($hasMixedContent) {
                $mixedContentPages[] = $page->url;
            }
        }

        $certificateExpiresAt = null;

        try {
            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $client = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);

            if ($client !== false) {
                $params = stream_context_get_params($client);
                $certInfo = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                $certificateExpiresAt = isset($certInfo['validTo_time_t'])
                    ? (new \DateTimeImmutable('@'.$certInfo['validTo_time_t']))->format('Y-m-d')
                    : null;
                fclose($client);
            }
        } catch (\Throwable) {
            // A failed certificate lookup isn't fatal to the rest of
            // this scan — certificate_expires_at simply stays null.
        }

        if ($mixedContentPages !== []) {
            $count = count($mixedContentPages);
            $issues[] = new TechnicalSeoIssue('security', 'warning', "{$count} page(s) load HTTP resources on an HTTPS page (mixed content).", 'Update those resource URLs to HTTPS — browsers may block or warn about mixed content.');
        }

        return [
            'mixed_content_pages' => $mixedContentPages,
            'certificate_expires_at' => $certificateExpiresAt,
        ];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeCrawlDepth(CrawlResult $crawl, array &$issues): array
    {
        $distribution = [];

        foreach ($crawl->pages as $page) {
            $distribution[$page->depth] = ($distribution[$page->depth] ?? 0) + 1;
        }

        ksort($distribution);

        // An "orphan" here means discovered/crawled but never actually
        // linked TO from any other crawled page — approximated as:
        // this page's own URL never appears in any OTHER page's
        // internalLinkUrls list.
        $linkedTo = [];

        foreach ($crawl->pages as $page) {
            foreach ($page->internalLinkUrls as $url) {
                $linkedTo[$url] = true;
            }
        }

        $orphans = [];

        foreach ($crawl->pages as $page) {
            if ($page->depth > 0 && ! isset($linkedTo[$page->url])) {
                $orphans[] = $page->url;
            }
        }

        if ($orphans !== []) {
            $count = count($orphans);
            $issues[] = new TechnicalSeoIssue('crawl_depth', 'notice', "{$count} orphan page(s) found — reachable, but not linked from any other crawled page.", 'Add internal links to orphan pages so search engines (and users) can discover them naturally.');
        }

        return ['depth_distribution' => $distribution, 'orphan_pages' => $orphans];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeHreflang(CrawlResult $crawl, array &$issues): array
    {
        $pagesWithHreflang = 0;

        foreach ($crawl->pages as $page) {
            foreach ($page->meta?->raw ?? [] as $tag) {
                if (($tag['property'] ?? null) === 'hreflang' || str_contains((string) ($tag['name'] ?? ''), 'hreflang')) {
                    $pagesWithHreflang++;

                    break;
                }
            }
        }

        return [
            'implemented' => $pagesWithHreflang > 0,
            'pages_with_hreflang' => $pagesWithHreflang,
        ];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeStructuredData(CrawlResult $crawl, array &$issues): array
    {
        $typeCounts = [];
        $errorPages = [];

        foreach ($crawl->pages as $page) {
            foreach ($page->schema as $block) {
                foreach ($block->types as $type) {
                    $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;
                }

                if (! $block->valid) {
                    $errorPages[] = $page->url;
                }
            }
        }

        if ($errorPages !== []) {
            $count = count(array_unique($errorPages));
            $issues[] = new TechnicalSeoIssue('structured_data', 'warning', "{$count} page(s) have invalid structured data.", 'Validate JSON-LD blocks with Google\'s Rich Results Test.');
        }

        return ['type_counts' => $typeCounts, 'error_pages' => array_values(array_unique($errorPages))];
    }

    /**
     * @param  array<int, TechnicalSeoIssue>  $issues
     * @return array{0: int, 1: string}
     */
    private function healthScore(array $issues): array
    {
        $weights = ['critical' => 15, 'warning' => 6, 'notice' => 2];
        $deduction = array_sum(array_map(static fn (TechnicalSeoIssue $i): int => $weights[$i->severity] ?? 2, $issues));
        $score = max(0, 100 - $deduction);

        $grade = match (true) {
            $score >= 90 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 40 => 'D',
            default => 'F',
        };

        return [$score, $grade];
    }

    /**
     * Same bridge pattern App\OnPageSeo\OnPageSeoAnalyzer::toSeoAuditResult()
     * already established for Phase R1 — converts this class's own
     * issue list into a real App\Audit\Seo\DTO\SeoAuditResult so
     * App\TechnicalSeo\Jobs\RunTechnicalSeoScanJob can feed it to this
     * app's own EXISTING, unmodified AIRecommendationEngine.
     */
    public function toSeoAuditResult(TechnicalSeoResult $result): SeoAuditResult
    {
        $severityMap = [
            'critical' => SeoSeverity::CRITICAL,
            'warning' => SeoSeverity::WARNING,
            'notice' => SeoSeverity::NOTICE,
        ];

        $seoIssues = array_map(
            static fn (TechnicalSeoIssue $issue): SeoIssue => new SeoIssue(
                check: $issue->check,
                code: $issue->check,
                severity: $severityMap[$issue->severity] ?? SeoSeverity::NOTICE,
                message: $issue->message,
                recommendation: $issue->recommendation,
                pageUrl: $result->domain,
            ),
            $result->issues,
        );

        $criticalCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::CRITICAL));
        $warningCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::WARNING));
        $noticeCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::NOTICE));

        $page = new PageSeoResult(
            url: $result->domain,
            score: $result->healthScore,
            issues: $seoIssues,
            criticalCount: $criticalCount,
            warningCount: $warningCount,
            noticeCount: $noticeCount,
        );

        return new SeoAuditResult(
            startUrl: $result->domain,
            pages: [$page],
            failedPageUrls: [],
            pagesAnalyzed: 1,
            pagesFailed: 0,
            averageScore: $result->healthScore,
            recommendations: [],
            analyzedAt: $result->analyzedAt,
        );
    }
}