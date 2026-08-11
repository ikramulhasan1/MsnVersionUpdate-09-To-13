<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity;

use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityCheckResult;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityScore;
use App\Audit\BusinessOpportunity\DTO\OutreachMessage;
use App\Audit\BusinessOpportunity\DTO\SalesOpportunity;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\Enums\BusinessOpportunityCheckStatus;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Fetching\DTO\FetchResult;

/**
 * Foundation for the business opportunity checklist (e.g. contact
 * information, lead-generation elements, business credibility signals,
 * conversion paths) against a single fetched page.
 *
 * This phase only establishes the analyzer's structure — the checks
 * array, the score/grade/summary plumbing, and the public analyze()
 * contract — so future phases can add one check method per business
 * opportunity signal without touching this scaffold. No checks are
 * implemented yet: $checks is intentionally empty, and score/grade
 * report null until at least one check exists to measure. Mirrors
 * AccessibilityAnalyzer's and UiUxAnalyzer's structure, and reuses
 * PerformanceAnalyzer's established pattern for a nullable score/grade
 * when nothing has been measured yet.
 *
 * Takes a FetchResult, consistent with AccessibilityAnalyzer and
 * UiUxAnalyzer, since business opportunity signals (contact links,
 * calls to action, trust markers, etc.) are expected to need the raw
 * DOM rather than only the parsed Fetching DTOs.
 *
 * A later phase adds a separate Website Health issue list — Website
 * Problems, SEO Issues, and Performance Issues — reported via
 * $websiteHealth on BusinessOpportunityResult rather than through
 * $checks: those checks have a severity dimension that
 * BusinessOpportunityCheckResult doesn't carry, so they use the
 * dedicated WebsiteHealthIssue DTO and are entirely independent of the
 * $checks/score/grade/summary scaffold above, which is untouched.
 *
 * A further phase adds a Website Modernization group (Old Technology,
 * Missing SSL, Missing Schema, Missing Sitemap) to the same
 * $websiteHealth structure, as an additional category alongside — not
 * replacing — website_problems/seo_issues/performance_issues.
 *
 * A further phase adds a Marketing Analysis group (No Tracking, No
 * Analytics, No Chat) to the same $websiteHealth structure, again as
 * an additional category alongside the existing ones.
 *
 * A further phase adds a Content & Conversion Analysis group (Missing
 * CTA, No Blog, Old Blog, Poor Mobile UX) to the same $websiteHealth
 * structure, again as an additional category alongside the existing
 * ones.
 *
 * A further phase adds $businessOpportunityScore — Lead Score,
 * Priority, and Opportunity Score — computed purely from the
 * WebsiteHealthIssue results already present across $websiteHealth.
 * It generates no new recommendation text of its own and does not
 * alter any $websiteHealth category's checks.
 *
 * A further phase adds $salesOpportunity — Estimated Deal Potential
 * and Suggested Service — derived from $websiteHealth and
 * $businessOpportunityScore, again without altering either.
 *
 * The final phase adds $outreachMessage — a Suggested Outreach Message
 * (subject + body) templated from $websiteHealth,
 * $businessOpportunityScore, and $salesOpportunity. It generates no new
 * findings, scores, or recommendations of its own; it only assembles
 * text from data those earlier phases already computed, and does not
 * alter any of them.
 */
final class BusinessOpportunityAnalyzer
{
    /**
     * Points-averaging and letter-grade thresholds for the overall
     * score, established now so future checks only need to report a
     * status — not redesign scoring. Mirrors AccessibilityAnalyzer's
     * constructor defaults exactly.
     */
    public function __construct(
        private readonly int $pointsPass = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsFail = 0,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function analyze(FetchResult $result): BusinessOpportunityResult
    {
        /**
         * Future phases add one entry per business opportunity check
         * here, e.g. 'contact_info' => $this->checkContactInfo($xpath),
         * matching AccessibilityAnalyzer::analyze()'s pattern. Left
         * empty in this phase — no checks are implemented yet.
         *
         * @var array<string, BusinessOpportunityCheckResult> $checks
         */
        $checks = [];

        $score = $this->score($checks);
        $grade = $score !== null ? $this->grade($score) : null;

        /**
         * Website Health issue list — Website Problems, SEO Issues,
         * Performance Issues only, kept separate from $checks above.
         *
         * @var array<string, array<int, WebsiteHealthIssue>> $websiteHealth
         */
        $websiteHealth = [
            'website_problems' => $this->checkWebsiteProblems($result),
            'seo_issues' => $this->checkSeoIssues($result),
            'performance_issues' => $this->checkPerformanceIssues($result),
            'website_modernization' => $this->checkWebsiteModernization($result),
            'marketing_analysis' => $this->checkMarketingIssues($result),
            'content_conversion_analysis' => $this->checkContentConversionIssues($result),
        ];

        $businessOpportunityScore = $this->businessOpportunityScore($websiteHealth);
        $salesOpportunity = $this->salesOpportunity($websiteHealth, $businessOpportunityScore);
        $outreachMessage = $this->outreachMessage(
            $result->url,
            $websiteHealth,
            $businessOpportunityScore,
            $salesOpportunity,
        );

        return new BusinessOpportunityResult(
            url: $result->url,
            checks: $checks,
            score: $score,
            grade: $grade,
            summary: $this->summary($checks, $score, $grade),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
            websiteHealth: $websiteHealth,
            businessOpportunityScore: $businessOpportunityScore,
            salesOpportunity: $salesOpportunity,
            outreachMessage: $outreachMessage,
        );
    }

    /**
     * Averages points across every check. Returns null when there are
     * no checks to average yet — this analyzer has none implemented in
     * this phase.
     *
     * @param array<string, BusinessOpportunityCheckResult> $checks
     */
    private function score(array $checks): ?int
    {
        if ($checks === []) {
            return null;
        }

        $points = array_map(
            fn (BusinessOpportunityCheckResult $check): int => $this->pointsFor($check->status),
            $checks,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(BusinessOpportunityCheckStatus $status): int
    {
        return match ($status) {
            BusinessOpportunityCheckStatus::PASS => $this->pointsPass,
            BusinessOpportunityCheckStatus::WARNING => $this->pointsWarning,
            BusinessOpportunityCheckStatus::FAIL => $this->pointsFail,
        };
    }

    private function grade(int $score): string
    {
        return match (true) {
            $score >= $this->gradeAThreshold => 'A',
            $score >= $this->gradeBThreshold => 'B',
            $score >= $this->gradeCThreshold => 'C',
            $score >= $this->gradeDThreshold => 'D',
            default => 'F',
        };
    }

    /**
     * @param array<string, BusinessOpportunityCheckResult> $checks
     */
    private function summary(array $checks, ?int $score, ?string $grade): string
    {
        if ($checks === [] || $score === null || $grade === null) {
            return 'No business opportunity checks have been implemented yet.';
        }

        $counts = ['pass' => 0, 'warning' => 0, 'fail' => 0];

        foreach ($checks as $check) {
            $counts[$check->status->value]++;
        }

        return sprintf(
            'Business opportunity score %d/100 (grade %s), based on %d check(s): %d passed, %d warning(s), '
                . '%d failed.',
            $score,
            $grade,
            count($checks),
            $counts['pass'],
            $counts['warning'],
            $counts['fail'],
        );
    }

    /**
     * General page/crawl-health checks: whether the page fetched
     * without transport errors, whether it responded with a healthy
     * (non-redirect, non-error) status code, and whether the three
     * "well-known" discoverability files (robots.txt, XML sitemap, web
     * manifest) exist — all read directly off FetchResult, with no
     * additional fetching of their own.
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkWebsiteProblems(FetchResult $result): array
    {
        $issues = [];

        $issues[] = $result->errors === []
            ? $this->buildIssue(
                'Page Fetch Errors',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            )
            : $this->buildIssue(
                'Page Fetch Errors',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Resolve the fetch/transport errors preventing this page from loading reliably: '
                    . implode('; ', $result->errors),
            );

        $issues[] = match (true) {
            $result->statusCode === 200 => $this->buildIssue(
                'HTTP Status Code',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
            $result->statusCode !== null && $result->statusCode >= 300 && $result->statusCode < 400 => $this->buildIssue(
                'HTTP Status Code',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Page returned a redirect status ({$result->statusCode}); point links directly at the final URL.",
            ),
            default => $this->buildIssue(
                'HTTP Status Code',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Page did not return a healthy 200 status code ('
                    . ($result->statusCode !== null ? (string) $result->statusCode : 'none')
                    . '); fix the error preventing a normal response.',
            ),
        };

        $issues[] = $result->robotsTxt->exists
            ? $this->buildIssue('Robots.txt Presence', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Robots.txt Presence',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'Add a robots.txt file so search engine crawlers get explicit crawling guidance.',
            );

        $issues[] = $result->sitemap->exists
            ? $this->buildIssue('XML Sitemap Presence', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'XML Sitemap Presence',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'Add an XML sitemap and reference it from robots.txt to help search engines discover pages.',
            );

        return $issues;
    }

    /**
     * On-page SEO fundamentals read from FetchResult's parsed <meta>
     * data and heading list: title tag, meta description, canonical
     * tag, and single-H1 heading structure.
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkSeoIssues(FetchResult $result): array
    {
        $issues = [];
        $title = $result->meta?->title;
        $titleLength = $title !== null ? mb_strlen(trim($title)) : 0;

        $issues[] = match (true) {
            $title === null || trim($title) === '' => $this->buildIssue(
                'Title Tag',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Add a unique, descriptive <title> tag to the page.',
            ),
            $titleLength < 10 || $titleLength > 60 => $this->buildIssue(
                'Title Tag',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'Adjust the title tag length to roughly 10-60 characters so it displays well in search results.',
            ),
            default => $this->buildIssue('Title Tag', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null),
        };

        $description = $result->meta?->description;
        $descriptionLength = $description !== null ? mb_strlen(trim($description)) : 0;

        $issues[] = match (true) {
            $description === null || trim($description) === '' => $this->buildIssue(
                'Meta Description',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'Add a meta description summarizing the page to improve search-result click-through.',
            ),
            $descriptionLength < 50 || $descriptionLength > 160 => $this->buildIssue(
                'Meta Description',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::NOTICE,
                'Adjust the meta description length to roughly 50-160 characters so it isn\'t cut off in search results.',
            ),
            default => $this->buildIssue(
                'Meta Description',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
        };

        $issues[] = $result->meta?->canonical !== null && trim($result->meta->canonical) !== ''
            ? $this->buildIssue('Canonical Tag', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Canonical Tag',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'Add a rel="canonical" link tag to prevent duplicate-content ambiguity.',
            );

        $h1Count = count(array_filter($result->headings, static fn ($heading): bool => $heading->level === 1));

        $issues[] = match (true) {
            $h1Count === 1 => $this->buildIssue(
                'Heading Structure (H1)',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
            $h1Count === 0 => $this->buildIssue(
                'Heading Structure (H1)',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Add exactly one <h1> heading that describes the page\'s main topic.',
            ),
            default => $this->buildIssue(
                'Heading Structure (H1)',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Page has {$h1Count} <h1> headings; use exactly one <h1> per page.",
            ),
        };

        return $issues;
    }

    /**
     * Lightweight performance signals available directly from
     * FetchResult without a full browser render: response time,
     * render-blocking-style CSS/JS asset counts, and images missing
     * explicit width/height (a layout-shift risk).
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkPerformanceIssues(FetchResult $result): array
    {
        $issues = [];

        $issues[] = match (true) {
            $result->responseTimeMs === null => $this->buildIssue(
                'Server Response Time',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::NOTICE,
                'Response time could not be measured for this fetch.',
            ),
            $result->responseTimeMs <= 800 => $this->buildIssue(
                'Server Response Time',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
            $result->responseTimeMs <= 1800 => $this->buildIssue(
                'Server Response Time',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Server took {$result->responseTimeMs}ms to respond; investigate server-side or hosting bottlenecks.",
            ),
            default => $this->buildIssue(
                'Server Response Time',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                "Server took {$result->responseTimeMs}ms to respond, which is too slow; investigate server-side or hosting bottlenecks.",
            ),
        };

        $cssCount = count($result->cssLinks);

        $issues[] = $cssCount <= 6
            ? $this->buildIssue('CSS File Count', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'CSS File Count',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Page loads {$cssCount} separate stylesheets; combine or defer non-critical CSS to reduce render blocking.",
            );

        $jsCount = count($result->jsLinks);

        $issues[] = $jsCount <= 9
            ? $this->buildIssue('JS File Count', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'JS File Count',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Page loads {$jsCount} separate scripts; bundle or defer non-critical JS to speed up page rendering.",
            );

        $imagesMissingDimensions = count(array_filter(
            $result->images,
            static fn ($image): bool => $image->width === null || $image->height === null,
        ));

        $issues[] = $imagesMissingDimensions === 0
            ? $this->buildIssue(
                'Image Dimensions',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            )
            : $this->buildIssue(
                'Image Dimensions',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "{$imagesMissingDimensions} image(s) are missing explicit width/height attributes; add them to "
                    . 'prevent layout shift while images load.',
            );

        return $issues;
    }

    /**
     * Website Modernization checks: legacy technology markers, missing
     * HTTPS, missing structured data (schema.org), and missing XML
     * sitemap. All read directly from FetchResult with no additional
     * fetching — SSL is inferred from the fetched URL's scheme (and,
     * if https, whether an HSTS header was sent), schema from the
     * already-parsed schema.org block list, and sitemap from the same
     * DiscoveredResource used by checkWebsiteProblems(), just reported
     * here under the modernization framing the prompt asked for
     * ("Missing Sitemap" as a fail/pass, rather than a
     * pass/warning presence check).
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkWebsiteModernization(FetchResult $result): array
    {
        $issues = [];
        $html = (string) $result->html;

        $hasFlashOrApplet = (bool) preg_match('/<embed\b[^>]*shockwave-flash|<object\b[^>]*shockwave-flash|<applet\b/i', $html);
        $hasLegacyDoctype = (bool) preg_match('/<!DOCTYPE\s+html\s+PUBLIC/i', $html);
        $hasViewportMeta = $result->meta?->viewport !== null && trim((string) $result->meta?->viewport) !== '';

        $issues[] = match (true) {
            $hasFlashOrApplet => $this->buildIssue(
                'Old Technology',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Remove Flash/Java applet embeds (<embed>/<object>/<applet>) — they are unsupported by modern '
                    . 'browsers and should be replaced with HTML5/CSS/JS equivalents.',
            ),
            $hasLegacyDoctype => $this->buildIssue(
                'Old Technology',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'Page uses a legacy HTML4/XHTML DOCTYPE; migrate to the HTML5 <!DOCTYPE html> declaration.',
            ),
            ! $hasViewportMeta => $this->buildIssue(
                'Old Technology',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'Add a <meta name="viewport"> tag so the page renders responsively on modern devices.',
            ),
            default => $this->buildIssue(
                'Old Technology',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
        };

        $effectiveUrl = $result->finalUrl ?? $result->url;
        $isHttps = str_starts_with(strtolower($effectiveUrl), 'https://');
        $hasHsts = false;

        foreach ($result->headers as $headerName => $headerValue) {
            if (strtolower($headerName) === 'strict-transport-security' && trim($headerValue) !== '') {
                $hasHsts = true;

                break;
            }
        }

        $issues[] = match (true) {
            ! $isHttps => $this->buildIssue(
                'Missing SSL',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'Serve the site over HTTPS with a valid SSL/TLS certificate; browsers flag HTTP pages as not secure.',
            ),
            ! $hasHsts => $this->buildIssue(
                'Missing SSL',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                'HTTPS is present but no Strict-Transport-Security header was found; add HSTS to prevent '
                    . 'downgrade/protocol-stripping attacks.',
            ),
            default => $this->buildIssue('Missing SSL', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null),
        };

        $issues[] = $result->schema !== []
            ? $this->buildIssue('Missing Schema', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Missing Schema',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'Add structured data (schema.org JSON-LD) so search engines can generate rich results for this page.',
            );

        $issues[] = $result->sitemap->exists
            ? $this->buildIssue('Missing Sitemap', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Missing Sitemap',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'Add an XML sitemap and reference it from robots.txt to help search engines discover and index pages.',
            );

        return $issues;
    }

    /**
     * Marketing tooling checks: whether any ad/marketing tracking
     * pixel, any web analytics platform, and any live-chat widget can
     * be found anywhere in the page's HTML or script asset URLs.
     * Detection is presence-only (no version/config parsing) — these
     * are simple "is a known third-party marketing script present at
     * all" checks, each covering several common providers so a site
     * using any one of them isn't flagged as missing the category.
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkMarketingIssues(FetchResult $result): array
    {
        $issues = [];

        $hasTrackingPixel = $this->pageContainsAny($result, [
            'fbq(', 'connect.facebook.net', 'googleadservices.com', 'googlesyndication.com',
            '_linkedin_partner_id', 'snap.licdn.com', 'ttq.load', 'analytics.tiktok.com',
            'px.ads.linkedin.com', 'bat.bing.com',
        ]);

        $issues[] = $hasTrackingPixel
            ? $this->buildIssue('No Tracking', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'No Tracking',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'No ad/marketing tracking pixel (e.g. Meta Pixel, Google Ads, LinkedIn Insight Tag) was detected; '
                    . 'add one to measure campaign performance and enable conversion-based ad optimization.',
            );

        $hasAnalytics = $this->pageContainsAny($result, [
            'googletagmanager.com', 'google-analytics.com', 'gtag(', "ga('create'", 'plausible.io',
            'matomo.js', 'piwik.js',
        ]);

        $issues[] = $hasAnalytics
            ? $this->buildIssue('No Analytics', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'No Analytics',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'No web analytics platform (e.g. Google Analytics/GA4, GTM) was detected; add one to understand '
                    . 'visitor behavior and measure site performance against business goals.',
            );

        $hasChatWidget = $this->pageContainsAny($result, [
            'widget.intercom.io', 'Intercom(', 'js.driftt.com', 'drift.load', 'embed.tawk.to',
            'zdassets.com', 'zEmbed', 'client.crisp.chat', 'wa.me/', 'api.whatsapp.com/send',
            'customerchat',
        ]);

        $issues[] = $hasChatWidget
            ? $this->buildIssue('No Chat', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'No Chat',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'No live-chat widget (e.g. Intercom, Drift, Tawk.to, WhatsApp chat) was detected; adding one gives '
                    . 'visitors a low-friction way to ask questions and can improve lead conversion.',
            );

        return $issues;
    }

    /**
     * Content & Conversion checks: a call-to-action link/button, a
     * discoverable blog section, that blog's apparent freshness, and
     * basic mobile-viewport correctness. All read directly from
     * FetchResult — anchors/text for CTA and blog discovery, decoded
     * schema.org JSON-LD for blog post dates when available, and the
     * viewport meta tag for mobile UX. A single page fetch cannot
     * reliably assess tap-target sizing, font legibility, or
     * horizontal-scroll issues without a real browser render, so Poor
     * Mobile UX is scoped to the one signal that is reliably available
     * here: whether a correct responsive viewport meta tag is present.
     *
     * @return array<int, WebsiteHealthIssue>
     */
    private function checkContentConversionIssues(FetchResult $result): array
    {
        $issues = [];
        $html = (string) $result->html;

        $ctaPhrases = [
            'buy now', 'sign up', 'signup', 'get started', 'book now', 'book a', 'subscribe',
            'add to cart', 'request a quote', 'free trial', 'contact us', 'get a quote',
            'schedule a', 'start now', 'try for free', 'shop now', 'order now', 'join now',
        ];

        $hasCtaAnchorText = false;

        foreach ($result->anchors as $anchor) {
            $text = strtolower(trim((string) $anchor->text));

            foreach ($ctaPhrases as $phrase) {
                if ($text !== '' && str_contains($text, $phrase)) {
                    $hasCtaAnchorText = true;

                    break 2;
                }
            }
        }

        $hasCtaMarkup = (bool) preg_match('/class=["\'][^"\']*\b(?:btn-primary|cta|call-to-action)\b/i', $html);

        $issues[] = $hasCtaAnchorText || $hasCtaMarkup
            ? $this->buildIssue('Missing CTA', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Missing CTA',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'No clear call-to-action (e.g. "Get Started", "Contact Us", "Buy Now") was found; add one so '
                    . 'visitors know what action to take next.',
            );

        $blogPattern = '/\bblog\b/i';
        $hasBlogLink = false;

        foreach ($result->anchors as $anchor) {
            if (
                ($anchor->text !== null && preg_match($blogPattern, $anchor->text) === 1)
                || preg_match('#/blog(?:/|$)#i', $anchor->url) === 1
            ) {
                $hasBlogLink = true;

                break;
            }
        }

        $issues[] = $hasBlogLink
            ? $this->buildIssue('No Blog', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'No Blog',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'No blog/articles section was found; regularly publishing content helps SEO and gives visitors a '
                    . 'reason to return.',
            );

        $issues[] = $this->checkOldBlog($result, $hasBlogLink);

        $viewport = $result->meta?->viewport;

        $issues[] = match (true) {
            $viewport === null || trim($viewport) === '' => $this->buildIssue(
                'Poor Mobile UX',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::CRITICAL,
                'No viewport meta tag was found; add <meta name="viewport" content="width=device-width, '
                    . 'initial-scale=1"> so the page scales correctly on mobile devices.',
            ),
            ! str_contains(strtolower($viewport), 'width=device-width') => $this->buildIssue(
                'Poor Mobile UX',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::WARNING,
                "Viewport meta tag (\"{$viewport}\") doesn't set width=device-width; update it so the layout "
                    . 'adapts to the visitor\'s screen width instead of a fixed desktop width.',
            ),
            default => $this->buildIssue(
                'Poor Mobile UX',
                BusinessOpportunityCheckStatus::PASS,
                SeoSeverity::NOTICE,
                null,
            ),
        };

        return $issues;
    }

    /**
     * Looks for a datePublished/dateModified value inside any decoded
     * schema.org JSON-LD block whose types suggest a blog post
     * (Blog/BlogPosting/Article/NewsArticle), and flags the blog as
     * stale when the most recent date found is over 12 months old.
     * When no blog was found at all, or no such date is present in the
     * page's structured data, freshness genuinely cannot be verified
     * from a single page fetch, so that is reported plainly rather
     * than guessed at.
     */
    private function checkOldBlog(FetchResult $result, bool $hasBlogLink): WebsiteHealthIssue
    {
        if (! $hasBlogLink) {
            return $this->buildIssue(
                'Old Blog',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::NOTICE,
                'No blog was found on this page (see "No Blog"), so blog freshness could not be assessed.',
            );
        }

        $blogTypes = ['blog', 'blogposting', 'article', 'newsarticle'];
        $latestDate = null;

        foreach ($result->schema as $block) {
            if ($block->data === null) {
                continue;
            }

            $isBlogLike = false;

            foreach ($block->types as $type) {
                if (in_array(strtolower($type), $blogTypes, true)) {
                    $isBlogLike = true;

                    break;
                }
            }

            if (! $isBlogLike) {
                continue;
            }

            foreach (['dateModified', 'datePublished'] as $dateField) {
                $rawDate = $block->data[$dateField] ?? null;

                if (! is_string($rawDate) || trim($rawDate) === '') {
                    continue;
                }

                try {
                    $parsed = new \DateTimeImmutable($rawDate);
                } catch (\Exception) {
                    continue;
                }

                if ($latestDate === null || $parsed > $latestDate) {
                    $latestDate = $parsed;
                }
            }
        }

        if ($latestDate === null) {
            return $this->buildIssue(
                'Old Blog',
                BusinessOpportunityCheckStatus::WARNING,
                SeoSeverity::NOTICE,
                'A blog was found, but no schema.org publish/update date was available to verify how recently it '
                    . 'was updated; add BlogPosting structured data with datePublished/dateModified.',
            );
        }

        $twelveMonthsAgo = (new \DateTimeImmutable())->modify('-12 months');

        return $latestDate >= $twelveMonthsAgo
            ? $this->buildIssue('Old Blog', BusinessOpportunityCheckStatus::PASS, SeoSeverity::NOTICE, null)
            : $this->buildIssue(
                'Old Blog',
                BusinessOpportunityCheckStatus::FAIL,
                SeoSeverity::WARNING,
                'Most recent blog content is dated ' . $latestDate->format('Y-m-d')
                    . ', which is over 12 months old; publish new content regularly to stay relevant to search '
                    . 'engines and visitors.',
            );
    }

    /**
     * Case-insensitive substring search across the page HTML and every
     * JS asset URL — used for simple third-party script presence
     * checks that don't need a dedicated weighted-signal method.
     *
     * @param array<int, string> $needles
     */
    private function pageContainsAny(FetchResult $result, array $needles): bool
    {
        $haystack = strtolower((string) $result->html);

        foreach ($result->jsLinks as $link) {
            $haystack .= ' ' . strtolower($link->url);
        }

        foreach ($needles as $needle) {
            if (str_contains($haystack, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rolls every WebsiteHealthIssue already produced in $websiteHealth
     * up into a Lead Score, Priority, and Opportunity Score. No new
     * recommendation text is generated here — only these three scores
     * — and no existing $websiteHealth entry is modified; this reads
     * the already-built category arrays only.
     *
     * Opportunity Score reflects overall site-health gap across every
     * category: each non-passing issue contributes its severity's
     * existing SeoSeverity::scoreWeight() (reused as-is, not
     * redefined), normalized against every issue's worst-case
     * (CRITICAL) weight so the score stays 0-100 regardless of how
     * many checks exist in total.
     *
     * Lead Score uses the same normalized-weight calculation, but
     * scoped to just the Marketing Analysis and Content & Conversion
     * Analysis categories: those are the business-facing gaps (no
     * tracking/analytics/chat, missing CTA, no/stale blog, poor mobile
     * UX) a non-technical decision-maker actually feels and that make
     * for a compelling outreach conversation, as opposed to backend
     * technical issues that matter for Opportunity Score but less for
     * lead quality.
     *
     * Priority is High whenever any CRITICAL-severity issue is
     * currently failing (regardless of the overall score, since a
     * single critical issue like missing SSL is urgent on its own),
     * or when Opportunity Score is otherwise high; Medium/Low
     * otherwise follow Opportunity Score.
     *
     * @param array<string, array<int, WebsiteHealthIssue>> $websiteHealth
     */
    private function businessOpportunityScore(array $websiteHealth): BusinessOpportunityScore
    {
        $allIssues = array_merge(...array_values($websiteHealth));

        $businessCategories = ['marketing_analysis', 'content_conversion_analysis'];
        $businessIssues = array_merge(
            ...array_values(array_intersect_key($websiteHealth, array_flip($businessCategories))),
        );

        [$overallWeight, $overallMax] = $this->weighIssues($allIssues);
        [$businessWeight, $businessMax] = $this->weighIssues($businessIssues);

        $opportunityScore = $overallMax > 0 ? (int) round($overallWeight / $overallMax * 100) : 0;
        $leadScore = $businessMax > 0 ? (int) round($businessWeight / $businessMax * 100) : 0;

        $hasCriticalFailure = false;

        foreach ($allIssues as $issue) {
            if ($issue->status === BusinessOpportunityCheckStatus::FAIL && $issue->severity === SeoSeverity::CRITICAL) {
                $hasCriticalFailure = true;

                break;
            }
        }

        $priority = match (true) {
            $hasCriticalFailure || $opportunityScore >= 60 => 'High',
            $opportunityScore >= 30 => 'Medium',
            default => 'Low',
        };

        return new BusinessOpportunityScore(
            leadScore: $leadScore,
            priority: $priority,
            opportunityScore: $opportunityScore,
        );
    }

    /**
     * Sums each non-passing issue's severity weight (via the existing
     * SeoSeverity::scoreWeight()), alongside the maximum possible sum
     * if every issue were CRITICAL — used to normalize both
     * Opportunity Score and Lead Score to a 0-100 range.
     *
     * @param array<int, WebsiteHealthIssue> $issues
     * @return array{0: int, 1: int} [weightSum, maxWeightSum]
     */
    private function weighIssues(array $issues): array
    {
        $weightSum = 0;
        $maxWeightSum = 0;

        foreach ($issues as $issue) {
            $maxWeightSum += SeoSeverity::CRITICAL->scoreWeight();

            if ($issue->status !== BusinessOpportunityCheckStatus::PASS) {
                $weightSum += $issue->severity->scoreWeight();
            }
        }

        return [$weightSum, $maxWeightSum];
    }

    /**
     * Derives an Estimated Deal Potential range from the already-
     * computed Opportunity Score, and a Suggested Service from
     * whichever $websiteHealth category carries the most weighted,
     * non-passing severity (via the same weighIssues() used for
     * BusinessOpportunityScore) — i.e. the site's single biggest
     * problem area, framed as the most relevant service to pitch.
     *
     * @param array<string, array<int, WebsiteHealthIssue>> $websiteHealth
     */
    private function salesOpportunity(array $websiteHealth, BusinessOpportunityScore $score): SalesOpportunity
    {
        $dealPotential = match (true) {
            $score->opportunityScore >= 75 => '$7,500 - $15,000+',
            $score->opportunityScore >= 50 => '$3,500 - $7,500',
            $score->opportunityScore >= 25 => '$1,500 - $3,500',
            default => '$500 - $1,500',
        };

        $serviceByCategory = [
            'website_problems' => 'Website Health & Reliability Audit',
            'seo_issues' => 'SEO Optimization Package',
            'performance_issues' => 'Performance Optimization Package',
            'website_modernization' => 'Website Modernization & Security Upgrade',
            'marketing_analysis' => 'Marketing & Analytics Integration Package',
            'content_conversion_analysis' => 'Content Strategy & Conversion Rate Optimization',
        ];

        $topCategory = null;
        $topWeight = 0;

        foreach ($websiteHealth as $category => $issues) {
            [$weightSum] = $this->weighIssues($issues);

            if ($weightSum > $topWeight) {
                $topWeight = $weightSum;
                $topCategory = $category;
            }
        }

        $suggestedService = $topCategory !== null && isset($serviceByCategory[$topCategory])
            ? $serviceByCategory[$topCategory]
            : 'General Website Consultation';

        return new SalesOpportunity(
            estimatedDealPotential: $dealPotential,
            suggestedService: $suggestedService,
        );
    }

    private function buildIssue(
        string $issue,
        BusinessOpportunityCheckStatus $status,
        SeoSeverity $severity,
        ?string $recommendation,
    ): WebsiteHealthIssue {
        return new WebsiteHealthIssue(
            issue: $issue,
            status: $status,
            severity: $severity,
            recommendation: $recommendation,
        );
    }

    /**
     * Templates a draft outreach message from data already computed by
     * businessOpportunityScore() and salesOpportunity() — no new
     * findings, scores, or recommendation text are generated here.
     *
     * The subject references the single most severe open issue (or, for
     * a clean site, leads with the positive result instead); the body
     * lists up to three of the most severe open issues with their
     * existing recommendations, states the Priority and Opportunity
     * Score, and closes with the Suggested Service and Estimated Deal
     * Potential as a soft call to action.
     *
     * @param array<string, array<int, WebsiteHealthIssue>> $websiteHealth
     */
    private function outreachMessage(
        string $url,
        array $websiteHealth,
        BusinessOpportunityScore $score,
        SalesOpportunity $sales,
    ): OutreachMessage {
        $host = (string) (parse_url($url, PHP_URL_HOST) ?: $url);
        $topIssues = $this->topIssues($websiteHealth, 3);

        $subject = match (true) {
            $topIssues === [] => "Great work on {$host} — a couple of ways to go even further",
            count($topIssues) === 1 => "{$host}: {$topIssues[0]->issue} could be costing you leads",
            default => sprintf(
                '%s: %s + %d more issue(s) worth a look',
                $host,
                $topIssues[0]->issue,
                count($topIssues) - 1,
            ),
        };

        if ($topIssues === []) {
            $body = "Hi there,\n\nI took a look at {$host} and it's in solid shape overall — nothing major stood "
                . "out in a quick audit. If you're open to it, I'd love to share a few smaller refinements that "
                . "could help even further, particularly around {$sales->suggestedService}.\n\nWorth a quick chat?";

            return new OutreachMessage(subject: $subject, message: $body);
        }

        $issueLines = array_map(
            static fn (WebsiteHealthIssue $issue): string => '- ' . $issue->issue
                . ($issue->recommendation !== null ? ": {$issue->recommendation}" : ''),
            $topIssues,
        );

        $body = "Hi there,\n\nI ran a quick audit of {$host} and a few things stood out:\n\n"
            . implode("\n", $issueLines)
            . "\n\nThat puts the site at {$score->priority} priority for improvement (opportunity score "
            . "{$score->opportunityScore}/100). A {$sales->suggestedService} would be the natural starting point "
            . "here, typically in the {$sales->estimatedDealPotential} range.\n\n"
            . 'Happy to walk through the full findings on a quick call if useful.';

        return new OutreachMessage(subject: $subject, message: $body);
    }

    /**
     * The most severe currently-failing/warning issues across every
     * $websiteHealth category, most severe first, capped at $limit.
     * Passing issues are excluded — there's nothing to pitch there.
     *
     * @param array<string, array<int, WebsiteHealthIssue>> $websiteHealth
     * @return array<int, WebsiteHealthIssue>
     */
    private function topIssues(array $websiteHealth, int $limit): array
    {
        $nonPassing = array_values(array_filter(
            array_merge(...array_values($websiteHealth)),
            static fn (WebsiteHealthIssue $issue): bool => $issue->status !== BusinessOpportunityCheckStatus::PASS,
        ));

        usort(
            $nonPassing,
            static fn (WebsiteHealthIssue $a, WebsiteHealthIssue $b): int => $b->severity->scoreWeight()
                <=> $a->severity->scoreWeight(),
        );

        return array_slice($nonPassing, 0, $limit);
    }
}
