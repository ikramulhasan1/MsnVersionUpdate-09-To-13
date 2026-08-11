<?php

declare(strict_types=1);

namespace App\Audit\Validation;

use App\Audit\Validation\Contracts\DnsResolverInterface;
use App\Audit\Validation\Contracts\SslInspectorInterface;
use App\Audit\Validation\Contracts\UrlValidatorServiceInterface;
use App\Audit\Validation\DTO\UrlValidationResult;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

final class UrlValidatorService implements UrlValidatorServiceInterface
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly DnsResolverInterface $dnsResolver,
        private readonly SslInspectorInterface $sslInspector,
        private readonly int $timeoutSeconds = 15,
    ) {
    }

    public function validate(string $url): UrlValidationResult
    {
        $errors = [];
        $checkedAt = (new \DateTimeImmutable())->format(DATE_ATOM);

        // 1. Structural format check — nothing else runs if this fails.
        $isFormatValid = $this->isFormatValid($url);

        if (! $isFormatValid) {
            $errors[] = 'The URL is not well-formed (expected e.g. https://example.com).';

            return new UrlValidationResult(
                url: $url,
                isValid: false,
                isFormatValid: false,
                isHttps: false,
                dnsResolved: false,
                domainExists: false,
                ipAddress: null,
                reachable: false,
                statusCode: null,
                redirected: false,
                finalUrl: null,
                redirectChain: [],
                responseTimeMs: null,
                ssl: null,
                errors: $errors,
                checkedAt: $checkedAt,
            );
        }

        $host = (string) parse_url($url, PHP_URL_HOST);
        $isHttps = strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';

        // 2. DNS checks.
        $dnsResolved = $this->dnsResolver->hasAnyRecord($host);
        $ipAddress = $this->dnsResolver->resolveIp($host);
        $domainExists = $ipAddress !== null;

        if (! $dnsResolved) {
            $errors[] = "DNS lookup failed for host \"{$host}\".";

            return new UrlValidationResult(
                url: $url,
                isValid: false,
                isFormatValid: true,
                isHttps: $isHttps,
                dnsResolved: false,
                domainExists: false,
                ipAddress: null,
                reachable: false,
                statusCode: null,
                redirected: false,
                finalUrl: null,
                redirectChain: [],
                responseTimeMs: null,
                ssl: null,
                errors: $errors,
                checkedAt: $checkedAt,
            );
        }

        // 3. HTTP reachability, status code and redirect chain.
        [$reachable, $statusCode, $finalUrl, $redirectChain, $responseTimeMs, $httpErrors]
            = $this->probeHttp($url);

        $errors = [...$errors, ...$httpErrors];

        // 4. SSL certificate inspection (only meaningful for https URLs).
        $ssl = $isHttps && $dnsResolved
            ? $this->sslInspector->inspect($host, $this->timeoutSeconds)
            : null;

        if ($ssl !== null && ! $ssl->valid && $ssl->error !== null) {
            $errors[] = "SSL check failed: {$ssl->error}";
        }

        $isValid = $isFormatValid && $dnsResolved && $reachable;

        return new UrlValidationResult(
            url: $url,
            isValid: $isValid,
            isFormatValid: true,
            isHttps: $isHttps,
            dnsResolved: $dnsResolved,
            domainExists: $domainExists,
            ipAddress: $ipAddress,
            reachable: $reachable,
            statusCode: $statusCode,
            redirected: count($redirectChain) > 0,
            finalUrl: $finalUrl,
            redirectChain: $redirectChain,
            responseTimeMs: $responseTimeMs,
            ssl: $ssl,
            errors: $errors,
            checkedAt: $checkedAt,
        );
    }

    private function isFormatValid(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = parse_url($url, PHP_URL_HOST);

        return in_array($scheme, ['http', 'https'], true) && ! empty($host);
    }

    /**
     * @return array{0: bool, 1: ?int, 2: ?string, 3: array<int, string>, 4: ?int, 5: array<int, string>}
     */
    private function probeHttp(string $url): array
    {
        $errors = [];
        $start = microtime(true);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => $this->timeoutSeconds,
                'connect_timeout' => $this->timeoutSeconds,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 5,
                    'track_redirects' => true,
                ],
                'verify' => true,
            ]);

            $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

            $redirectChain = array_filter(array_map(
                'trim',
                explode(',', $response->getHeaderLine('X-Guzzle-Redirect-History'))
            ));

            $finalUrl = $redirectChain !== [] ? end($redirectChain) : $url;

            return [true, $response->getStatusCode(), $finalUrl, array_values($redirectChain), $responseTimeMs, $errors];
        } catch (ConnectException $exception) {
            $errors[] = str_contains(strtolower($exception->getMessage()), 'timed out')
                ? "Connection timed out after {$this->timeoutSeconds}s."
                : 'Connection failed: ' . $exception->getMessage();

            return [false, null, null, [], null, $errors];
        } catch (RequestException $exception) {
            $errors[] = 'Request failed: ' . $exception->getMessage();

            return [false, null, null, [], null, $errors];
        } catch (GuzzleException $exception) {
            $errors[] = 'Unexpected HTTP error: ' . $exception->getMessage();

            return [false, null, null, [], null, $errors];
        }
    }
}
