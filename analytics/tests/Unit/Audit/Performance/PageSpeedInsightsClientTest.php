<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Performance;

use App\Audit\Performance\PageSpeedInsightsClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class PageSpeedInsightsClientTest extends TestCase
{
    private function makeClient(MockHandler $mock): Client
    {
        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    private function lighthouseResponseBody(): string
    {
        return json_encode([
            'lighthouseResult' => [
                'audits' => [
                    'largest-contentful-paint' => ['numericValue' => 2431.5],
                    'cumulative-layout-shift' => ['numericValue' => 0.08],
                    'total-blocking-time' => ['numericValue' => 145.0],
                ],
            ],
        ], JSON_THROW_ON_ERROR);
    }

    public function test_extracts_lcp_cls_and_tbt_from_a_successful_response(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->lighthouseResponseBody()),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $result = $client->fetch('https://example.com');

        $this->assertSame(
            [
                'lcp_ms' => 2431.5,
                'cls' => 0.08,
                'tbt_ms' => 145.0,
                'lcp_resource' => null,
                'cls_resource' => null,
                'tbt_resource' => null,
            ],
            $result,
        );
    }

    public function test_returns_null_without_making_a_request_when_no_api_key_is_configured(): void
    {
        $mock = new MockHandler; // no responses queued — a call would throw "no more items"

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: null);

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_returns_null_when_the_api_key_is_an_empty_string(): void
    {
        $mock = new MockHandler; // must never be called

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: '');

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_returns_null_on_a_non_200_response_instead_of_throwing(): void
    {
        $mock = new MockHandler([
            new Response(429, [], 'Rate limited'),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_returns_null_on_a_connection_failure_instead_of_throwing(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection timed out', new Request('GET', 'https://example.com')),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_returns_null_when_the_response_body_is_not_valid_json(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'not json'),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_returns_null_when_the_lighthouse_audits_key_is_missing(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['lighthouseResult' => []], JSON_THROW_ON_ERROR)),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $this->assertNull($client->fetch('https://example.com'));
    }

    public function test_missing_individual_metrics_are_null_rather_than_failing_the_whole_response(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'lighthouseResult' => [
                    'audits' => [
                        'largest-contentful-paint' => ['numericValue' => 1800.0],
                        // cumulative-layout-shift and total-blocking-time deliberately absent
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $this->assertSame(
            [
                'lcp_ms' => 1800.0,
                'cls' => null,
                'tbt_ms' => null,
                'lcp_resource' => null,
                'cls_resource' => null,
                'tbt_resource' => null,
            ],
            $client->fetch('https://example.com'),
        );
    }

    public function test_extracts_the_affected_resource_url_from_the_related_diagnostic_audit(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'lighthouseResult' => [
                    'audits' => [
                        'largest-contentful-paint' => ['numericValue' => 3100.0],
                        'largest-contentful-paint-element' => [
                            'details' => [
                                'items' => [
                                    ['url' => 'https://example.com/hero.jpg'],
                                ],
                            ],
                        ],
                        'long-tasks' => [
                            'details' => [
                                'items' => [
                                    ['url' => 'https://example.com/analytics.js', 'duration' => 210],
                                ],
                            ],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $result = $client->fetch('https://example.com');

        $this->assertSame('https://example.com/hero.jpg', $result['lcp_resource']);
        $this->assertSame('https://example.com/analytics.js', $result['tbt_resource']);
    }

    public function test_falls_back_to_the_node_selector_when_the_diagnostic_item_has_no_url(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'lighthouseResult' => [
                    'audits' => [
                        'cumulative-layout-shift' => ['numericValue' => 0.15],
                        'layout-shift-elements' => [
                            'details' => [
                                'items' => [
                                    ['node' => ['selector' => 'html > body > div.banner', 'snippet' => '<div class="banner">']],
                                ],
                            ],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $result = $client->fetch('https://example.com');

        $this->assertSame('html > body > div.banner', $result['cls_resource']);
    }

    public function test_affected_resource_is_null_when_the_diagnostic_audit_has_no_items(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'lighthouseResult' => [
                    'audits' => [
                        'largest-contentful-paint' => ['numericValue' => 2000.0],
                        'largest-contentful-paint-element' => [
                            'details' => ['items' => []],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $client = new PageSpeedInsightsClient($this->makeClient($mock), apiKey: 'test-key');

        $result = $client->fetch('https://example.com');

        $this->assertNull($result['lcp_resource']);
    }
}
