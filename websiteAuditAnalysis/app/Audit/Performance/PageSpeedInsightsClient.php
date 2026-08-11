<?php

declare(strict_types=1);

namespace App\Audit\Performance;

use GuzzleHttp\ClientInterface;
use Throwable;

/**
 * Thin wrapper around Google's PageSpeed Insights v5 API
 * (https://developers.google.com/speed/docs/insights/v5/reference),
 * used to give PerformanceAnalyzer real Core Web Vitals instead of the
 * "unknown" it otherwise reports for LCP/CLS/FID, which a static HTTP
 * crawler can never determine on its own (see PerformanceAnalyzer's
 * checkLcp/checkCls/checkFid — this class is what would feed them once
 * wired in, not part of this prompt's scope).
 *
 * Deliberately isolated behind fetch(): ?array rather than throwing or
 * returning a typed DTO. Every failure mode — missing/blank API key,
 * network error, timeout, non-200 response, unexpected JSON shape —
 * collapses to null. This must never be allowed to block or fail the
 * audit pipeline just because a third-party API had a bad moment;
 * "we don't have this data" is always an acceptable outcome, a thrown
 * exception is not.
 */
final class PageSpeedInsightsClient
{
    private const string ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly ?string $apiKey = null,
        private readonly int $timeoutSeconds = 20,
        private readonly string $strategy = 'mobile',
    ) {}

    /**
     * @return ?array{lcp_ms: ?float, cls: ?float, tbt_ms: ?float}
     *                                                             Null when the metrics couldn't be obtained for any reason.
     *                                                             Note the key is `tbt_ms` (Total Blocking Time), not
     *                                                             `fid_ms` — PSI v5 no longer reports lab First Input Delay
     *                                                             at all, and labelling a TBT value as FID would misrepresent
     *                                                             what was actually measured. Callers that want an FID proxy
     *                                                             (TBT correlates reasonably well with FID) should read this
     *                                                             key and label it as a proxy themselves, rather than this
     *                                                             client silently renaming one real metric as a different
     *                                                             one.
     */
    public function fetch(string $url): ?array
    {
        if ($this->apiKey === null || $this->apiKey === '') {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                'query' => [
                    'url' => $url,
                    'key' => $this->apiKey,
                    'strategy' => $this->strategy,
                    'category' => 'performance',
                ],
                'timeout' => $this->timeoutSeconds,
                'connect_timeout' => $this->timeoutSeconds,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $decoded = json_decode(
                (string) $response->getBody(),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            $audits = $decoded['lighthouseResult']['audits'] ?? null;

            if (! is_array($audits)) {
                return null;
            }

            return [
                'lcp_ms' => $this->numericAuditValue($audits, 'largest-contentful-paint'),
                'cls' => $this->numericAuditValue($audits, 'cumulative-layout-shift'),
                'tbt_ms' => $this->numericAuditValue($audits, 'total-blocking-time'),
            ];
        } catch (Throwable) {
            // Network error, timeout, malformed JSON, unexpected shape —
            // all of it collapses to "we don't have this data", never a
            // thrown exception the audit pipeline would have to handle.
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $audits
     */
    private function numericAuditValue(array $audits, string $auditKey): ?float
    {
        $value = $audits[$auditKey]['numericValue'] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }
}
