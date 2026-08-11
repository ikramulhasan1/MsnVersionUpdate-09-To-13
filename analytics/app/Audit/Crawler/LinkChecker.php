<?php

declare(strict_types=1);

namespace App\Audit\Crawler;

use App\Audit\Crawler\Contracts\LinkCheckerInterface;
use App\Audit\Crawler\DTO\LinkCheckResult;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;

final class LinkChecker implements LinkCheckerInterface
{
    /**
     * Servers that don't support HEAD tend to answer with one of these.
     */
    private const array HEAD_UNSUPPORTED_STATUSES = [405, 501];

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly int $timeoutSeconds = 10,
        private readonly string $userAgent = 'WebsiteAuditBot/1.0',
    ) {
    }

    public function check(string $url): LinkCheckResult
    {
        try {
            $status = $this->request('HEAD', $url);

            if (in_array($status, self::HEAD_UNSUPPORTED_STATUSES, true)) {
                $status = $this->request('GET', $url);
            }

            return new LinkCheckResult(
                exists: $status >= 200 && $status < 400,
                statusCode: $status,
                error: null,
            );
        } catch (\Throwable $exception) {
            return new LinkCheckResult(
                exists: false,
                statusCode: null,
                error: $exception->getMessage(),
            );
        }
    }

    /**
     * @param array<int, string> $urls
     * @return array<string, LinkCheckResult>
     */
    public function checkMany(array $urls): array
    {
        if ($urls === []) {
            return [];
        }

        $urls = array_values($urls);

        // Round 1: HEAD every URL concurrently.
        $headStatuses = $this->requestMany('HEAD', $urls);

        // Round 2: any URL whose server doesn't support HEAD gets a
        // concurrent GET fallback, same as check()'s sequential version.
        $needsGetFallback = [];

        foreach ($urls as $url) {
            if (in_array($headStatuses[$url] ?? null, self::HEAD_UNSUPPORTED_STATUSES, true)) {
                $needsGetFallback[] = $url;
            }
        }

        $getStatuses = $needsGetFallback !== [] ? $this->requestMany('GET', $needsGetFallback) : [];

        $results = [];

        foreach ($urls as $url) {
            $status = in_array($url, $needsGetFallback, true)
                ? ($getStatuses[$url] ?? null)
                : ($headStatuses[$url] ?? null);

            $results[$url] = $status === null
                ? new LinkCheckResult(exists: false, statusCode: null, error: 'The request could not be completed')
                : new LinkCheckResult(exists: $status >= 200 && $status < 400, statusCode: $status, error: null);
        }

        return $results;
    }

    /**
     * @param array<int, string> $urls
     * @return array<string, ?int> status code per URL, null on failure
     */
    private function requestMany(string $method, array $urls): array
    {
        $options = [
            'timeout' => $this->timeoutSeconds,
            'connect_timeout' => $this->timeoutSeconds,
            'http_errors' => false,
            'headers' => array_merge(
                ['User-Agent' => $this->userAgent],
                $method === 'GET' ? ['Range' => 'bytes=0-0'] : [],
            ),
            'allow_redirects' => ['max' => 5],
        ];

        /** @var array<int, PromiseInterface|null> $promises */
        $promises = [];

        foreach ($urls as $i => $url) {
            try {
                $promises[$i] = $this->httpClient->requestAsync($method, $url, $options);
            } catch (\Throwable) {
                $promises[$i] = null;
            }
        }

        $settled = Utils::settle(array_filter($promises))->wait();

        $statuses = [];

        foreach ($urls as $i => $url) {
            $outcome = $settled[$i] ?? null;

            $statuses[$url] = $outcome !== null && $outcome['state'] === PromiseInterface::FULFILLED
                ? ($outcome['value'] instanceof ResponseInterface ? $outcome['value']->getStatusCode() : null)
                : null;
        }

        return $statuses;
    }

    private function request(string $method, string $url): int
    {
        $response = $this->httpClient->request($method, $url, [
            'timeout' => $this->timeoutSeconds,
            'connect_timeout' => $this->timeoutSeconds,
            'http_errors' => false,
            'headers' => array_merge(
                ['User-Agent' => $this->userAgent],
                $method === 'GET' ? ['Range' => 'bytes=0-0'] : [],
            ),
            'allow_redirects' => ['max' => 5],
        ]);

        return $response->getStatusCode();
    }
}
