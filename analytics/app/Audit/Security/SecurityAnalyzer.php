<?php

declare(strict_types=1);

namespace App\Audit\Security;

use App\Audit\Enums\SecurityCheckStatus;
use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\FontAsset;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\ScriptLink;
use App\Audit\Security\DTO\SecurityAuditResult;
use App\Audit\Security\DTO\SecurityCheckResult;
use App\Audit\Security\DTO\SecurityResult;
use App\Audit\Validation\Contracts\SslInspectorInterface;

/**
 * Runs the connection/transport and content security checklist — HTTPS,
 * SSL certificate validity, presence of common security response headers,
 * HSTS, XSS protection, Content-Security-Policy, Referrer-Policy, cookie
 * security, mixed content, directory listing exposure, and server
 * information exposure — against a single fetched page via analyze(),
 * then rolls the results up into an overall score, letter grade, and
 * summary. analyzeAll() runs the same checklist across several fetched
 * pages at once and wraps the per-page results in a SecurityAuditResult,
 * mirroring how SeoAnalyzerService reports site-wide rather than
 * single-page results.
 *
 * Takes a FetchResult rather than a CrawledPage because these checks need
 * the raw response headers (Strict-Transport-Security, security headers,
 * Server, X-Powered-By) and raw HTML, which CrawledPage does not carry —
 * the same reasoning PerformanceAnalyzer documents for its own
 * not-yet-available metrics.
 */
final class SecurityAnalyzer
{
    /**
     * @param array<int, string> $requiredSecurityHeaders header names checked
     *        for presence under "Security Headers". Constructor-injected so
     *        the required set can change without editing this class.
     * @param array<int, string> $cspUnsafeTokens directives/sources that weaken a CSP even when present
     * @param array<int, string> $weakReferrerPolicies Referrer-Policy values treated as insufficiently strict
     * @param array<int, string> $serverInfoHeaders response headers checked for version/technology disclosure
     */
    public function __construct(
        private readonly SslInspectorInterface $sslInspector,
        private readonly int $sslTimeoutSeconds = 10,
        private readonly int $sslExpiryWarningDays = 14,
        private readonly int $hstsMinMaxAgeSeconds = 15_552_000, // 180 days
        private readonly array $requiredSecurityHeaders = [
            'Strict-Transport-Security',
            'Content-Security-Policy',
            'X-Content-Type-Options',
            'X-Frame-Options',
            'Referrer-Policy',
        ],
        private readonly array $cspUnsafeTokens = [
            'unsafe-inline',
            'unsafe-eval',
            '*',
        ],
        private readonly array $weakReferrerPolicies = [
            'unsafe-url',
            'no-referrer-when-downgrade',
        ],
        private readonly array $serverInfoHeaders = [
            'Server',
            'X-Powered-By',
        ],
        private readonly int $pointsPass = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsFail = 0,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function analyze(FetchResult $result): SecurityResult
    {
        $checks = [
            'https' => $this->checkHttps($result),
            'ssl' => $this->checkSsl($result),
            'security_headers' => $this->checkSecurityHeaders($result),
            'hsts' => $this->checkHsts($result),
            'xss_protection' => $this->checkXssProtection($result),
            'csp' => $this->checkCsp($result),
            'referrer_policy' => $this->checkReferrerPolicy($result),
            'cookie_security' => $this->checkCookieSecurity($result),
            'mixed_content' => $this->checkMixedContent($result),
            'directory_listing' => $this->checkDirectoryListing($result),
            'server_information_exposure' => $this->checkServerInformationExposure($result),
        ];

        $score = $this->score($checks);
        $grade = $this->grade($score);

        return new SecurityResult(
            url: $result->url,
            checks: $checks,
            score: $score,
            grade: $grade,
            summary: $this->summary($checks, $score, $grade),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * Runs analyze() over several already-fetched pages at once (see
     * AnalyzeChunkJob, which fetches up to config('audit.multi_page_analysis.per_page_limit')
     * pages via WebsiteFetcherServiceInterface::fetchMany() before calling
     * this) and wraps the per-page results in a SecurityAuditResult.
     * Pages whose fetch itself failed are reported in failedPageUrls
     * rather than analyzed, since there's no response to check.
     *
     * @param array<string, FetchResult> $fetchResults keyed by page URL
     */
    public function analyzeAll(array $fetchResults, string $startUrl): SecurityAuditResult
    {
        $pageResults = [];
        $failedPageUrls = [];

        foreach ($fetchResults as $url => $fetchResult) {
            if (! $fetchResult->success) {
                $failedPageUrls[] = $url;

                continue;
            }

            $pageResults[$url] = $this->analyze($fetchResult);
        }

        $averageScore = $pageResults !== []
            ? (int) round(
                array_sum(array_map(static fn (SecurityResult $r): int => $r->score, $pageResults))
                    / count($pageResults)
            )
            : 0;

        return new SecurityAuditResult(
            startUrl: $startUrl,
            pages: $pageResults,
            failedPageUrls: $failedPageUrls,
            pagesAnalyzed: count($pageResults),
            pagesFailed: count($failedPageUrls),
            averageScore: $averageScore,
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    private function checkHttps(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $scheme = strtolower((string) parse_url($targetUrl, PHP_URL_SCHEME));
        $isHttps = $scheme === 'https';

        return new SecurityCheckResult(
            check: 'HTTPS',
            value: $scheme !== '' ? $scheme : null,
            status: $isHttps ? SecurityCheckStatus::PASS : SecurityCheckStatus::FAIL,
            recommendation: $isHttps
                ? null
                : 'Serve the site over HTTPS: install a valid TLS certificate and redirect all HTTP traffic to HTTPS.',
            pageUrl: $targetUrl,
            affectedElements: $isHttps ? null : [
                $this->element($targetUrl, null, $scheme !== '' ? "Page is served over {$scheme}" : 'Page has no discernible URL scheme'),
            ],
        );
    }

    private function checkSsl(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $host = (string) parse_url($targetUrl, PHP_URL_HOST);

        if ($host === '') {
            return new SecurityCheckResult(
                check: 'SSL',
                value: null,
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Could not determine the host to inspect for an SSL certificate.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, 'No host could be parsed from the page URL')],
            );
        }

        $ssl = $this->sslInspector->inspect($host, $this->sslTimeoutSeconds);

        if (! $ssl->valid) {
            return new SecurityCheckResult(
                check: 'SSL',
                value: $ssl->error ?? 'invalid',
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Install a valid SSL certificate from a trusted certificate authority.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element($host, null, $ssl->error ?? 'Certificate is invalid')],
            );
        }

        $daysLeft = $ssl->daysUntilExpiry;

        if ($daysLeft !== null && $daysLeft <= 0) {
            return new SecurityCheckResult(
                check: 'SSL',
                value: 'expired',
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Renew the expired SSL certificate immediately.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element($host, null, 'Certificate expired')],
            );
        }

        if ($daysLeft !== null && $daysLeft <= $this->sslExpiryWarningDays) {
            return new SecurityCheckResult(
                check: 'SSL',
                value: "{$daysLeft} days until expiry",
                status: SecurityCheckStatus::WARNING,
                recommendation: "Renew the SSL certificate soon — it expires in {$daysLeft} day(s).",
                pageUrl: $targetUrl,
                affectedElements: [$this->element($host, null, "Certificate expires in {$daysLeft} day(s)")],
            );
        }

        return new SecurityCheckResult(
            check: 'SSL',
            value: $daysLeft !== null ? "{$daysLeft} days until expiry" : 'valid',
            status: SecurityCheckStatus::PASS,
            recommendation: null,
            pageUrl: $targetUrl,
        );
    }

    private function checkSecurityHeaders(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $present = array_map(strtolower(...), array_keys($result->headers));
        $missing = array_values(array_filter(
            $this->requiredSecurityHeaders,
            static fn (string $header): bool => ! in_array(strtolower($header), $present, true),
        ));

        if ($missing === []) {
            return new SecurityCheckResult(
                check: 'Security Headers',
                value: 'all present',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        $allMissing = count($missing) === count($this->requiredSecurityHeaders);

        return new SecurityCheckResult(
            check: 'Security Headers',
            value: 'missing: ' . implode(', ', $missing),
            status: $allMissing ? SecurityCheckStatus::FAIL : SecurityCheckStatus::WARNING,
            recommendation: 'Add the missing security headers: ' . implode(', ', $missing) . '.',
            pageUrl: $targetUrl,
            affectedElements: array_map(
                fn (string $header): array => $this->element(null, null, "Missing header: {$header}"),
                $missing,
            ),
        );
    }

    private function checkHsts(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $value = $this->headerValue($result, 'Strict-Transport-Security');

        if ($value === null) {
            return new SecurityCheckResult(
                check: 'HSTS',
                value: null,
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Add a Strict-Transport-Security header (e.g. "max-age=31536000; includeSubDomains") '
                    . 'to enforce HTTPS on future visits.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, 'No Strict-Transport-Security header present')],
            );
        }

        $maxAge = $this->extractMaxAge($value);

        if ($maxAge !== null && $maxAge < $this->hstsMinMaxAgeSeconds) {
            return new SecurityCheckResult(
                check: 'HSTS',
                value: $value,
                status: SecurityCheckStatus::WARNING,
                recommendation: "Increase the HSTS max-age to at least {$this->hstsMinMaxAgeSeconds} seconds "
                    . 'for stronger protection.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, "max-age={$maxAge} (below recommended {$this->hstsMinMaxAgeSeconds})")],
            );
        }

        return new SecurityCheckResult(
            check: 'HSTS',
            value: $value,
            status: SecurityCheckStatus::PASS,
            recommendation: null,
            pageUrl: $targetUrl,
        );
    }

    private function targetUrl(FetchResult $result): string
    {
        return $result->finalUrl ?? $result->url;
    }

    private function checkXssProtection(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $value = $this->headerValue($result, 'X-XSS-Protection');

        if ($value === null) {
            return new SecurityCheckResult(
                check: 'XSS Protection',
                value: null,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'X-XSS-Protection is deprecated in modern browsers, but a strong Content-Security-Policy '
                    . 'is the current best defense against XSS — verify that CSP check passes.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, 'No X-XSS-Protection header present')],
            );
        }

        $normalized = trim($value);

        if ($normalized === '0') {
            return new SecurityCheckResult(
                check: 'XSS Protection',
                value: $value,
                status: SecurityCheckStatus::FAIL,
                recommendation: 'X-XSS-Protection is explicitly disabled (0). Remove this override or set it to '
                    . '"1; mode=block", and rely on a strong Content-Security-Policy as the primary defense.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, "X-XSS-Protection: {$value}")],
            );
        }

        if (str_starts_with($normalized, '1') && str_contains(strtolower($normalized), 'mode=block')) {
            return new SecurityCheckResult(
                check: 'XSS Protection',
                value: $value,
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        if (str_starts_with($normalized, '1')) {
            return new SecurityCheckResult(
                check: 'XSS Protection',
                value: $value,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'X-XSS-Protection is enabled but without "mode=block", so the browser may sanitize '
                    . 'rather than block the page. Set it to "1; mode=block", or rely on CSP instead.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, "X-XSS-Protection: {$value}")],
            );
        }

        return new SecurityCheckResult(
            check: 'XSS Protection',
            value: $value,
            status: SecurityCheckStatus::WARNING,
            recommendation: 'X-XSS-Protection has an unrecognized value. Set it to "1; mode=block", or remove it '
                . 'and rely on a strong Content-Security-Policy instead.',
            pageUrl: $targetUrl,
            affectedElements: [$this->element(null, null, "X-XSS-Protection: {$value}")],
        );
    }

    private function checkCsp(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $value = $this->headerValue($result, 'Content-Security-Policy');

        if ($value === null) {
            return new SecurityCheckResult(
                check: 'Content Security Policy',
                value: null,
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Add a Content-Security-Policy header to restrict which sources scripts, styles, and '
                    . 'other resources can be loaded from.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, 'No Content-Security-Policy header present')],
            );
        }

        $lower = strtolower($value);
        $foundUnsafeTokens = array_values(array_filter(
            $this->cspUnsafeTokens,
            static fn (string $token): bool => str_contains($lower, strtolower($token)),
        ));

        if ($foundUnsafeTokens !== []) {
            return new SecurityCheckResult(
                check: 'Content Security Policy',
                value: $value,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'Tighten the CSP: it currently allows ' . implode(', ', $foundUnsafeTokens)
                    . ', which weakens protection against injected scripts.',
                pageUrl: $targetUrl,
                affectedElements: array_map(
                    fn (string $token): array => $this->element(null, null, "CSP allows {$token}"),
                    $foundUnsafeTokens,
                ),
            );
        }

        return new SecurityCheckResult(
            check: 'Content Security Policy',
            value: $value,
            status: SecurityCheckStatus::PASS,
            recommendation: null,
            pageUrl: $targetUrl,
        );
    }

    private function checkReferrerPolicy(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $value = $this->headerValue($result, 'Referrer-Policy');

        if ($value === null) {
            return new SecurityCheckResult(
                check: 'Referrer Policy',
                value: null,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'Add a Referrer-Policy header (e.g. "strict-origin-when-cross-origin") to control how '
                    . 'much referrer information is sent to other sites.',
                pageUrl: $targetUrl,
                affectedElements: [$this->element(null, null, 'No Referrer-Policy header present')],
            );
        }

        // A Referrer-Policy header can list multiple fallback values,
        // comma-separated; if any listed value is weak, the browser may
        // fall back to it, so the whole policy is only as strong as its
        // weakest listed value.
        $listedPolicies = array_map(
            static fn (string $p): string => strtolower(trim($p)),
            explode(',', $value),
        );

        $weak = array_intersect($listedPolicies, array_map(strtolower(...), $this->weakReferrerPolicies));

        if ($weak !== []) {
            return new SecurityCheckResult(
                check: 'Referrer Policy',
                value: $value,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'Referrer-Policy includes ' . implode(', ', array_unique($weak))
                    . ', which leaks the full referrer URL to other origins. Use "strict-origin-when-cross-origin" '
                    . 'or "no-referrer" instead.',
                pageUrl: $targetUrl,
                affectedElements: array_map(
                    fn (string $policy): array => $this->element(null, null, "Referrer-Policy includes {$policy}"),
                    array_values(array_unique($weak)),
                ),
            );
        }

        return new SecurityCheckResult(
            check: 'Referrer Policy',
            value: $value,
            status: SecurityCheckStatus::PASS,
            recommendation: null,
            pageUrl: $targetUrl,
        );
    }

    private function checkCookieSecurity(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $value = $this->headerValue($result, 'Set-Cookie');

        if ($value === null) {
            return new SecurityCheckResult(
                check: 'Cookie Security',
                value: 'no cookies set',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        $cookies = $this->splitCookies($value);
        $missingSecure = [];
        $missingHttpOnly = [];
        $missingSameSite = [];

        foreach ($cookies as $cookie) {
            $name = $this->cookieName($cookie);
            $lower = strtolower($cookie);

            if (! str_contains($lower, 'secure')) {
                $missingSecure[] = $name;
            }

            if (! str_contains($lower, 'httponly')) {
                $missingHttpOnly[] = $name;
            }

            if (! str_contains($lower, 'samesite')) {
                $missingSameSite[] = $name;
            }
        }

        if ($missingSecure !== []) {
            return new SecurityCheckResult(
                check: 'Cookie Security',
                value: $value,
                status: SecurityCheckStatus::FAIL,
                recommendation: 'Set the Secure attribute on cookie(s): ' . implode(', ', $missingSecure)
                    . ' — without it, the cookie can be sent over an unencrypted HTTP connection.',
                pageUrl: $targetUrl,
                affectedElements: array_map(
                    fn (string $name): array => $this->element(null, null, "Cookie \"{$name}\" is missing the Secure attribute"),
                    $missingSecure,
                ),
            );
        }

        if ($missingHttpOnly !== [] || $missingSameSite !== []) {
            $notes = [];
            $affectedElements = [];

            if ($missingHttpOnly !== []) {
                $notes[] = 'HttpOnly missing on: ' . implode(', ', $missingHttpOnly);

                foreach ($missingHttpOnly as $name) {
                    $affectedElements[] = $this->element(null, null, "Cookie \"{$name}\" is missing the HttpOnly attribute");
                }
            }

            if ($missingSameSite !== []) {
                $notes[] = 'SameSite missing on: ' . implode(', ', $missingSameSite);

                foreach ($missingSameSite as $name) {
                    $affectedElements[] = $this->element(null, null, "Cookie \"{$name}\" is missing the SameSite attribute");
                }
            }

            return new SecurityCheckResult(
                check: 'Cookie Security',
                value: $value,
                status: SecurityCheckStatus::WARNING,
                recommendation: 'Strengthen cookie attributes — ' . implode('; ', $notes)
                    . '. HttpOnly blocks JavaScript access (mitigating XSS theft); SameSite mitigates CSRF.',
                pageUrl: $targetUrl,
                affectedElements: $affectedElements,
            );
        }

        return new SecurityCheckResult(
            check: 'Cookie Security',
            value: $value,
            status: SecurityCheckStatus::PASS,
            recommendation: null,
            pageUrl: $targetUrl,
        );
    }

    private function checkMixedContent(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $scheme = strtolower((string) parse_url($targetUrl, PHP_URL_SCHEME));

        if ($scheme !== 'https') {
            return new SecurityCheckResult(
                check: 'Mixed Content',
                value: 'not applicable — page is not served over HTTPS',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        $insecureResources = $this->collectInsecureResources($result);

        if ($insecureResources === []) {
            return new SecurityCheckResult(
                check: 'Mixed Content',
                value: 'no insecure resources found',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        $count = count($insecureResources);
        $sample = implode(', ', array_slice(array_column($insecureResources, 'url'), 0, 3));

        return new SecurityCheckResult(
            check: 'Mixed Content',
            value: $count === 1
                ? "1 insecure resource: {$sample}"
                : "{$count} insecure resources, including: {$sample}",
            status: SecurityCheckStatus::FAIL,
            recommendation: 'Update the insecure (http://) resource URL(s) to https:// — browsers block or warn '
                . 'on mixed content loaded into an HTTPS page.',
            pageUrl: $targetUrl,
            affectedElements: array_map(
                fn (array $resource): array => $this->element($resource['url'], $resource['domPath'], 'Loaded over insecure http://'),
                $insecureResources,
            ),
        );
    }

    /**
     * Collects every CSS, JS, image, and font sub-resource referenced by
     * the page that loads over plain HTTP, together with the DOM path of
     * the tag that references it (when known — see HtmlParser::buildDomPath()
     * upstream). Anchors are deliberately excluded — a hyperlink to an
     * HTTP page is not "mixed content"; only actively fetched
     * sub-resources trigger the browser warning/block this check is
     * about. De-duplicated by URL (first occurrence's domPath wins),
     * since the same asset can legitimately be referenced more than
     * once on a page.
     *
     * @return array<int, array{url: string, domPath: ?string}>
     */
    private function collectInsecureResources(FetchResult $result): array
    {
        /** @var array<int, CssLink|ScriptLink|ImageAsset|FontAsset> $assets */
        $assets = [
            ...$result->cssLinks,
            ...$result->jsLinks,
            ...$result->images,
            ...$result->fonts,
        ];

        $insecure = [];
        $seen = [];

        foreach ($assets as $asset) {
            if (strtolower((string) parse_url($asset->url, PHP_URL_SCHEME)) !== 'http') {
                continue;
            }

            if (isset($seen[$asset->url])) {
                continue;
            }

            $seen[$asset->url] = true;
            $insecure[] = ['url' => $asset->url, 'domPath' => $asset->domPath];
        }

        return $insecure;
    }

    private function checkDirectoryListing(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $title = trim((string) $result->meta?->title);
        $html = (string) $result->html;

        if (! $this->looksLikeDirectoryListing($title, $html)) {
            return new SecurityCheckResult(
                check: 'Directory Listing',
                value: 'no directory listing detected',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        return new SecurityCheckResult(
            check: 'Directory Listing',
            value: $title !== '' ? $title : 'directory listing detected',
            status: SecurityCheckStatus::FAIL,
            recommendation: 'Disable directory autoindexing on the web server (e.g. "Options -Indexes" on Apache, '
                . '"autoindex off;" on nginx) and add an index file to every exposed directory.',
            pageUrl: $targetUrl,
            affectedElements: [$this->element($targetUrl, null, $title !== '' ? $title : 'Directory listing detected')],
        );
    }

    /**
     * Apache/nginx/IIS autoindex pages consistently title themselves
     * "Index of /path" and list a "Parent Directory" link in the body,
     * even across servers/themes, so checking both together keeps this
     * from false-positiving on an unrelated page that merely mentions
     * "index" in its title.
     */
    private function looksLikeDirectoryListing(string $title, string $html): bool
    {
        if (preg_match('/^index of\s+\//i', $title) === 1) {
            return true;
        }

        return preg_match('/index of\s+\//i', $html) === 1
            && str_contains(strtolower($html), 'parent directory');
    }

    private function checkServerInformationExposure(FetchResult $result): SecurityCheckResult
    {
        $targetUrl = $this->targetUrl($result);
        $exposed = [];
        $affectedElements = [];

        foreach ($this->serverInfoHeaders as $header) {
            $value = $this->headerValue($result, $header);

            if ($value !== null && trim($value) !== '') {
                $exposed[] = "{$header}: {$value}";
                $affectedElements[] = $this->element(null, null, "{$header}: {$value}");
            }
        }

        if ($exposed === []) {
            return new SecurityCheckResult(
                check: 'Server Information Exposure',
                value: 'no server/technology details exposed',
                status: SecurityCheckStatus::PASS,
                recommendation: null,
                pageUrl: $targetUrl,
            );
        }

        return new SecurityCheckResult(
            check: 'Server Information Exposure',
            value: implode('; ', $exposed),
            status: SecurityCheckStatus::WARNING,
            recommendation: 'Suppress server/technology version details in response headers ('
                . implode(', ', $this->serverInfoHeaders) . ') to avoid giving attackers a head start on known '
                . 'vulnerabilities for that exact version.',
            pageUrl: $targetUrl,
            affectedElements: $affectedElements,
        );
    }

    /**
     * Splits a flattened, comma-joined Set-Cookie header back into
     * individual cookies. A Set-Cookie value can itself legally contain a
     * comma (inside an Expires date, e.g. "Expires=Wed, 21 Oct 2026..."),
     * so a plain explode(',', ...) would wrongly split it. This only
     * splits on a comma that's followed by what looks like the start of
     * a new cookie (a token immediately followed by "="), which the
     * "Expires=Wed, 21 Oct..." case never matches since "21 Oct 2026" has
     * no "=".
     *
     * @return array<int, string>
     */
    private function splitCookies(string $headerValue): array
    {
        $parts = preg_split('/,(?=\s*[A-Za-z0-9_\-]+=)/', $headerValue) ?: [$headerValue];

        return array_values(array_filter(array_map(trim(...), $parts), static fn (string $c): bool => $c !== ''));
    }

    private function cookieName(string $cookie): string
    {
        $namePart = explode(';', $cookie, 2)[0];
        $name = explode('=', $namePart, 2)[0];

        return trim($name) !== '' ? trim($name) : 'unnamed';
    }

    private function headerValue(FetchResult $result, string $name): ?string
    {
        foreach ($result->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Builds one entry of a SecurityCheckResult's affectedElements list.
     * $url is the specific resource/host implicated (when there is one),
     * $domPath its location in the page's DOM (when known — only
     * meaningful for actual markup-referenced resources like
     * mixed_content's assets), and $detail a short human-readable note
     * of what's wrong with it.
     *
     * @return array{url: ?string, domPath: ?string, detail: ?string}
     */
    private function element(?string $url, ?string $domPath, ?string $detail): array
    {
        return ['url' => $url, 'domPath' => $domPath, 'detail' => $detail];
    }

    private function extractMaxAge(string $headerValue): ?int
    {
        return preg_match('/max-age\s*=\s*(\d+)/i', $headerValue, $matches) === 1
            ? (int) $matches[1]
            : null;
    }

    /**
     * Averages points across every check (pass/warning/fail all map to a
     * point value), then rounds to the nearest whole score. Mirrors
     * PerformanceAnalyzer::score()'s points-averaging approach, minus the
     * "unknown" exclusion — every security check here always resolves to
     * a definite status, so none are excluded.
     *
     * @param array<string, SecurityCheckResult> $checks
     */
    private function score(array $checks): int
    {
        $points = array_map(
            fn (SecurityCheckResult $check): int => $this->pointsFor($check->status),
            $checks,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(SecurityCheckStatus $status): int
    {
        return match ($status) {
            SecurityCheckStatus::PASS => $this->pointsPass,
            SecurityCheckStatus::WARNING => $this->pointsWarning,
            SecurityCheckStatus::FAIL => $this->pointsFail,
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
     * @param array<string, SecurityCheckResult> $checks
     */
    private function summary(array $checks, int $score, string $grade): string
    {
        $counts = ['pass' => 0, 'warning' => 0, 'fail' => 0];

        foreach ($checks as $check) {
            $counts[$check->status->value]++;
        }

        return sprintf(
            'Security score %d/100 (grade %s), based on %d check(s): %d passed, %d warning(s), %d failed.',
            $score,
            $grade,
            count($checks),
            $counts['pass'],
            $counts['warning'],
            $counts['fail'],
        );
    }
}