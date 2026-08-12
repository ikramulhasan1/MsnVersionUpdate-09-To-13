<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Security;

use App\Audit\Enums\SecurityCheckStatus;
use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Security\DTO\SecurityAuditResult;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Validation\DTO\SslInfo;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeSslInspector;
use Tests\Support\FetchResultFactory;

final class SecurityAnalyzerTest extends TestCase
{
    private function analyzer(?SslInfo $ssl = null): SecurityAnalyzer
    {
        return new SecurityAnalyzer(
            sslInspector: new FakeSslInspector($ssl ?? new SslInfo(valid: true, daysUntilExpiry: 200)),
        );
    }

    public function test_fully_secure_page_scores_perfectly(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $this->assertSame(100, $result->score);
        $this->assertSame('A', $result->grade);

        foreach ($result->checks as $check) {
            $this->assertSame(SecurityCheckStatus::PASS, $check->status);
            $this->assertSame('https://example.com/', $check->pageUrl);
        }
    }

    public function test_http_url_fails_the_https_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(url: 'http://example.com/'));

        $check = $result->checks['https'];

        $this->assertSame(SecurityCheckStatus::FAIL, $check->status);
        $this->assertSame('http://example.com/', $check->pageUrl);
        $this->assertNotNull($check->affectedElements);
        $this->assertSame('http://example.com/', $check->affectedElements[0]['url']);
    }

    public function test_missing_security_headers_are_flagged(): void
    {
        $result = $this->analyzer()->analyze(
            FetchResultFactory::make(includeDefaultSecurityHeaders: false),
        );

        $securityHeaders = $result->checks['security_headers'];

        $this->assertSame(SecurityCheckStatus::FAIL, $securityHeaders->status);
        $this->assertSame(SecurityCheckStatus::FAIL, $result->checks['hsts']->status);
        $this->assertLessThan(100, $result->score);

        $this->assertNotNull($securityHeaders->affectedElements);
        $this->assertNotEmpty($securityHeaders->affectedElements);
        $this->assertStringContainsString('Missing header:', $securityHeaders->affectedElements[0]['detail']);
    }

    public function test_invalid_ssl_certificate_fails_and_lowers_the_score(): void
    {
        $result = $this->analyzer(new SslInfo(valid: false, error: 'self-signed certificate'))
            ->analyze(FetchResultFactory::make());

        $ssl = $result->checks['ssl'];

        $this->assertSame(SecurityCheckStatus::FAIL, $ssl->status);
        $this->assertLessThan(100, $result->score);
        $this->assertNotNull($ssl->affectedElements);
        $this->assertSame('example.com', $ssl->affectedElements[0]['url']);
        $this->assertSame('self-signed certificate', $ssl->affectedElements[0]['detail']);
    }

    public function test_ssl_certificate_expiring_soon_is_a_warning_not_a_failure(): void
    {
        $result = $this->analyzer(new SslInfo(valid: true, daysUntilExpiry: 5))
            ->analyze(FetchResultFactory::make());

        $this->assertSame(SecurityCheckStatus::WARNING, $result->checks['ssl']->status);
    }

    public function test_mixed_content_check_reports_the_affected_resource_url_and_dom_path(): void
    {
        $insecureCss = new CssLink(
            url: 'http://example.com/legacy.css',
            pageUrl: 'https://example.com/',
            domPath: 'html > head > link:nth-child(3)',
        );

        $result = $this->analyzer()->analyze(
            FetchResultFactory::make(cssLinks: [$insecureCss]),
        );

        $mixedContent = $result->checks['mixed_content'];

        $this->assertSame(SecurityCheckStatus::FAIL, $mixedContent->status);
        $this->assertNotNull($mixedContent->affectedElements);
        $this->assertSame('http://example.com/legacy.css', $mixedContent->affectedElements[0]['url']);
        $this->assertSame('html > head > link:nth-child(3)', $mixedContent->affectedElements[0]['domPath']);
    }

    public function test_cookie_security_check_lists_the_offending_cookie_name(): void
    {
        $result = $this->analyzer()->analyze(
            FetchResultFactory::make(headers: [
                'Set-Cookie' => 'session=abc123; HttpOnly; SameSite=Strict',
            ]),
        );

        $cookieSecurity = $result->checks['cookie_security'];

        $this->assertSame(SecurityCheckStatus::FAIL, $cookieSecurity->status);
        $this->assertNotNull($cookieSecurity->affectedElements);
        $this->assertStringContainsString('"session"', $cookieSecurity->affectedElements[0]['detail']);
        $this->assertStringContainsString('Secure', $cookieSecurity->affectedElements[0]['detail']);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'checks', 'score', 'grade', 'summary', 'analyzed_at'], array_keys($decoded));
        $this->assertSame(
            ['check', 'value', 'status', 'recommendation', 'page_url', 'affected_elements'],
            array_keys($decoded['checks']['https']),
        );
    }

    public function test_analyze_all_reports_a_result_per_page_and_lists_pages_that_failed_to_fetch(): void
    {
        $ok = FetchResultFactory::make(url: 'https://example.com/ok');
        $failed = FetchResultFactory::make(url: 'https://example.com/broken', success: false, errors: ['Fetch failed']);

        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/ok' => $ok, 'https://example.com/broken' => $failed],
            'https://example.com/',
        );

        $this->assertInstanceOf(SecurityAuditResult::class, $result);
        $this->assertArrayHasKey('https://example.com/ok', $result->pages);
        $this->assertArrayNotHasKey('https://example.com/broken', $result->pages);
        $this->assertSame(['https://example.com/broken'], $result->failedPageUrls);
        $this->assertSame(1, $result->pagesAnalyzed);
        $this->assertSame(1, $result->pagesFailed);
    }

    public function test_analyze_all_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyzeAll(
            ['https://example.com/' => FetchResultFactory::make()],
            'https://example.com/',
        );

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['start_url', 'summary', 'pages', 'failed_page_urls', 'analyzed_at'],
            array_keys($decoded),
        );
        $this->assertSame(
            ['pages_analyzed', 'pages_failed', 'average_score'],
            array_keys($decoded['summary']),
        );
        $this->assertSame(100, $decoded['summary']['average_score']);
    }
}
