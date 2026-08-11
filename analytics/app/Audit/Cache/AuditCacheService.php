<?php

declare(strict_types=1);

namespace App\Audit\Cache;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Jobs\AnalyzeChunkJob;
use Closure;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Thin wrapper around Laravel's Cache repository (constructor-injected,
 * not the Cache facade, so this class stays easy to substitute/mock)
 * for audit-specific reusable data. See
 * {@see AuditCacheServiceInterface} for the fetch/crawl-vs-results
 * key strategy this class implements.
 */
final class AuditCacheService implements AuditCacheServiceInterface
{
    private const string FETCH_PREFIX = 'audit-cache:fetch:';

    private const string CRAWL_PREFIX = 'audit-cache:crawl:';

    private const string RESULTS_PREFIX = 'audit-cache:results:';

    private const string RECOMMENDATIONS_PREFIX = 'audit-cache:recommendations:';

    private const string FRAGMENT_PREFIX = 'audit-cache:fragment:';

    private const string PROGRESS_PREFIX = 'audit-cache:progress:';

    /**
     * Generous relative to how long any single audit should actually
     * take — this is just a safety net so a progress entry for an
     * abandoned/failed audit doesn't linger in cache forever, not a
     * value anything times out against.
     */
    private const int PROGRESS_TTL_SECONDS = 3600;

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly int $fetchTtlSeconds = 3600,
        private readonly int $crawlTtlSeconds = 3600,
        private readonly int $resultsTtlSeconds = 86400,
    ) {
    }

    public function rememberFetchResult(string $url, Closure $callback): FetchResult
    {
        $result = $this->cache->remember(
            self::FETCH_PREFIX . $this->keyFor($url),
            $this->fetchTtlSeconds,
            $callback,
        );

        assert($result instanceof FetchResult);

        return $result;
    }

    public function rememberCrawlResult(string $url, Closure $callback): CrawlResult
    {
        $result = $this->cache->remember(
            self::CRAWL_PREFIX . $this->keyFor($url),
            $this->crawlTtlSeconds,
            $callback,
        );

        assert($result instanceof CrawlResult);

        return $result;
    }

    public function putAnalysisResults(string $auditUuid, AnalysisResults $results): void
    {
        $this->cache->put(self::RESULTS_PREFIX . $auditUuid, $results, $this->resultsTtlSeconds);
    }

    public function getAnalysisResults(string $auditUuid): ?AnalysisResults
    {
        $value = $this->cache->get(self::RESULTS_PREFIX . $auditUuid);

        return $value instanceof AnalysisResults ? $value : null;
    }

    public function putRecommendations(string $auditUuid, AIRecommendationResult $result): void
    {
        $this->cache->put(self::RECOMMENDATIONS_PREFIX . $auditUuid, $result, $this->resultsTtlSeconds);
    }

    public function getRecommendations(string $auditUuid): ?AIRecommendationResult
    {
        $value = $this->cache->get(self::RECOMMENDATIONS_PREFIX . $auditUuid);

        return $value instanceof AIRecommendationResult ? $value : null;
    }

    public function putAnalysisFragment(string $auditUuid, string $key, object $result): void
    {
        $this->cache->put($this->fragmentKey($auditUuid, $key), $result, $this->resultsTtlSeconds);
    }

    public function getAnalysisFragments(string $auditUuid): array
    {
        $fragments = [];

        foreach (AnalyzeChunkJob::ANALYZER_KEYS as $key) {
            $value = $this->cache->get($this->fragmentKey($auditUuid, $key));

            if ($value !== null) {
                $fragments[$key] = $value;
            }
        }

        return $fragments;
    }

    public function forgetFragments(string $auditUuid): void
    {
        foreach (AnalyzeChunkJob::ANALYZER_KEYS as $key) {
            $this->cache->forget($this->fragmentKey($auditUuid, $key));
        }
    }

    public function forget(string $auditUuid): void
    {
        $this->cache->forget(self::RESULTS_PREFIX . $auditUuid);
        $this->cache->forget(self::RECOMMENDATIONS_PREFIX . $auditUuid);
    }

    public function putProgress(string $auditUuid, int $percent, string $label): void
    {
        $this->cache->put(
            self::PROGRESS_PREFIX . $auditUuid,
            ['percent' => max(0, min(100, $percent)), 'label' => $label],
            self::PROGRESS_TTL_SECONDS,
        );
    }

    public function getProgress(string $auditUuid): ?array
    {
        $value = $this->cache->get(self::PROGRESS_PREFIX . $auditUuid);

        return is_array($value) && isset($value['percent'], $value['label']) ? $value : null;
    }

    /**
     * URLs can be arbitrarily long (see the 2048-char `url` column on
     * `audits`) and may contain characters a cache key shouldn't carry
     * verbatim, so the key is derived from a hash of the URL rather
     * than the URL itself.
     */
    private function keyFor(string $url): string
    {
        return md5($url);
    }

    /**
     * Each analyzer key gets its own independent cache slot (rather
     * than all fragments living inside one shared array value) so
     * concurrent writes from parallel AnalyzeChunkJob chunks never
     * need to read-modify-write the same cache entry.
     */
    private function fragmentKey(string $auditUuid, string $key): string
    {
        return self::FRAGMENT_PREFIX . $auditUuid . ':' . $key;
    }
}
