<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Security;

use App\Audit\Enums\SecurityCheckStatus;
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
        }
    }

    public function test_http_url_fails_the_https_check(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make(url: 'http://example.com/'));

        $this->assertSame(SecurityCheckStatus::FAIL, $result->checks['https']->status);
    }

    public function test_missing_security_headers_are_flagged(): void
    {
        $result = $this->analyzer()->analyze(
            FetchResultFactory::make(includeDefaultSecurityHeaders: false),
        );

        $this->assertSame(SecurityCheckStatus::FAIL, $result->checks['security_headers']->status);
        $this->assertSame(SecurityCheckStatus::FAIL, $result->checks['hsts']->status);
        $this->assertLessThan(100, $result->score);
    }

    public function test_invalid_ssl_certificate_fails_and_lowers_the_score(): void
    {
        $result = $this->analyzer(new SslInfo(valid: false, error: 'self-signed certificate'))
            ->analyze(FetchResultFactory::make());

        $this->assertSame(SecurityCheckStatus::FAIL, $result->checks['ssl']->status);
        $this->assertLessThan(100, $result->score);
    }

    public function test_ssl_certificate_expiring_soon_is_a_warning_not_a_failure(): void
    {
        $result = $this->analyzer(new SslInfo(valid: true, daysUntilExpiry: 5))
            ->analyze(FetchResultFactory::make());

        $this->assertSame(SecurityCheckStatus::WARNING, $result->checks['ssl']->status);
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $result = $this->analyzer()->analyze(FetchResultFactory::make());

        $decoded = json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['url', 'checks', 'score', 'grade', 'summary', 'analyzed_at'], array_keys($decoded));
    }
}
