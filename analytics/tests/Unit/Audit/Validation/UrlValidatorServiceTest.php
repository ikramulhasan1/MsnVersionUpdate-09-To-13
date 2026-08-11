<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Validation;

use App\Audit\Validation\DTO\SslInfo;
use App\Audit\Validation\UrlValidatorService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeDnsResolver;
use Tests\Support\FakeSslInspector;

final class UrlValidatorServiceTest extends TestCase
{
    private function makeClient(MockHandler $mock): Client
    {
        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    public function test_rejects_malformed_url_without_any_network_calls(): void
    {
        $mock = new MockHandler(); // no responses queued — a call would throw "no more items"

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(),
            sslInspector: new FakeSslInspector(SslInfo::unavailable('n/a')),
        );

        $result = $service->validate('not a valid url');

        $this->assertFalse($result->isValid);
        $this->assertFalse($result->isFormatValid);
        $this->assertFalse($result->dnsResolved);
        $this->assertNotEmpty($result->errors);
    }

    public function test_valid_reachable_https_url_with_no_redirects(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(hasRecord: true, ip: '93.184.216.34'),
            sslInspector: new FakeSslInspector(new SslInfo(
                valid: true,
                issuer: 'Example CA',
                validFrom: '2026-01-01T00:00:00+00:00',
                validTo: '2027-01-01T00:00:00+00:00',
                daysUntilExpiry: 148,
            )),
        );

        $result = $service->validate('https://example.com');

        $this->assertTrue($result->isValid);
        $this->assertTrue($result->isHttps);
        $this->assertTrue($result->dnsResolved);
        $this->assertTrue($result->domainExists);
        $this->assertSame('93.184.216.34', $result->ipAddress);
        $this->assertTrue($result->reachable);
        $this->assertSame(200, $result->statusCode);
        $this->assertFalse($result->redirected);
        $this->assertNotNull($result->ssl);
        $this->assertTrue($result->ssl->valid);
        $this->assertEmpty($result->errors);
    }

    public function test_fails_fast_when_dns_does_not_resolve(): void
    {
        $mock = new MockHandler(); // must never be called

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(hasRecord: false, ip: null),
            sslInspector: new FakeSslInspector(SslInfo::unavailable('n/a')),
        );

        $result = $service->validate('https://this-domain-does-not-exist.invalid');

        $this->assertFalse($result->isValid);
        $this->assertTrue($result->isFormatValid);
        $this->assertFalse($result->dnsResolved);
        $this->assertFalse($result->domainExists);
        $this->assertFalse($result->reachable);
        $this->assertNull($result->statusCode);
        $this->assertStringContainsString('DNS lookup failed', $result->errors[0]);
    }

    public function test_detects_a_redirect_chain(): void
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-Guzzle-Redirect-History' => 'http://example.com/, https://example.com/',
            ], 'OK'),
        ]);

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(),
            sslInspector: new FakeSslInspector(SslInfo::unavailable('n/a')),
        );

        $result = $service->validate('http://example.com');

        $this->assertTrue($result->reachable);
        $this->assertTrue($result->redirected);
        $this->assertCount(2, $result->redirectChain);
        $this->assertSame('https://example.com/', $result->finalUrl);
    }

    public function test_handles_connection_timeout_gracefully(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection timed out', new Request('GET', 'https://example.com')),
        ]);

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(),
            sslInspector: new FakeSslInspector(SslInfo::unavailable('n/a')),
            timeoutSeconds: 5,
        );

        $result = $service->validate('https://example.com');

        $this->assertFalse($result->isValid);
        $this->assertFalse($result->reachable);
        $this->assertNull($result->statusCode);
        $this->assertNotEmpty($result->errors);
    }

    public function test_ssl_failure_is_reported_but_does_not_alone_flip_overall_validity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(),
            sslInspector: new FakeSslInspector(SslInfo::unavailable('certificate has expired')),
        );

        $result = $service->validate('https://example.com');

        // The site is reachable and DNS resolves, so it's still "valid" overall —
        // but the SSL problem must be visible in both the ssl block and errors.
        $this->assertTrue($result->isValid);
        $this->assertNotNull($result->ssl);
        $this->assertFalse($result->ssl->valid);
        $this->assertStringContainsString('SSL check failed', implode(' ', $result->errors));
    }

    public function test_result_serializes_to_the_expected_json_shape(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);

        $service = new UrlValidatorService(
            httpClient: $this->makeClient($mock),
            dnsResolver: new FakeDnsResolver(),
            sslInspector: new FakeSslInspector(new SslInfo(valid: true, issuer: 'Example CA')),
        );

        $result = $service->validate('https://example.com');
        $decoded = json_decode($result->toJson(), true, flags: JSON_THROW_ON_ERROR);

        foreach ([
            'url', 'valid', 'format_valid', 'https', 'dns_resolved', 'domain_exists',
            'ip_address', 'reachable', 'status_code', 'redirected', 'redirect_count',
            'final_url', 'redirect_chain', 'response_time_ms', 'ssl', 'errors', 'checked_at',
        ] as $key) {
            $this->assertArrayHasKey($key, $decoded);
        }

        $this->assertSame('Example CA', $decoded['ssl']['issuer']);
    }
}
