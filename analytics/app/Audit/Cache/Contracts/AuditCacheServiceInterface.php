<?php

declare(strict_types=1);

namespace App\Audit\Cache\Contracts;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Fetching\DTO\FetchResult;
use Closure;

/**
 * Caches audit data that is expensive to (re)compute, split into two
 * concerns:
 *
 *  - Fetch/crawl results are reusable across audits: they are keyed
 *    by the target URL rather than by audit, so a second audit of the
 *    same URL — and every job within the same audit's pipeline that
 *    needs the same page — can reuse one already-fetched/crawled
 *    result instead of repeating the outbound HTTP work.
 *  - Analysis results and AI recommendations are specific to one
 *    audit run. They are keyed by audit uuid and are how the queued
 *    job pipeline hands its output to whichever job/controller needs
 *    it next, without requiring a dedicated results table yet.
 */
interface AuditCacheServiceInterface
{
    /**
     * Returns the cached FetchResult for $url, computing and caching
     * it via $callback on a cache miss.
     */
    public function rememberFetchResult(string $url, Closure $callback): FetchResult;

    /**
     * Phase K4 (Bulk Audit) — pre-seeds the fetch cache for $url with
     * an already-computed $result, using the SAME cache key
     * rememberFetchResult() itself writes to and reads from. This is
     * what lets App\Audit\Jobs\BulkFetchJob's own concurrent,
     * whole-batch fetch (see that job's own docblock for why fetching
     * concurrently matters for a bulk submission) actually save real
     * time downstream: once this has been called for a URL, the FIRST
     * rememberFetchResult() call any later FetchAndCrawlJob makes for
     * that same URL is a cache HIT — the expensive network fetch
     * itself never runs a second time, sequentially, per audit.
     */
    public function putFetchResult(string $url, FetchResult $result): void;

    /**
     * Returns the cached CrawlResult for $url, computing and caching
     * it via $callback on a cache miss.
     */
    public function rememberCrawlResult(string $url, Closure $callback): CrawlResult;

    public function putAnalysisResults(string $auditUuid, AnalysisResults $results): void;

    public function getAnalysisResults(string $auditUuid): ?AnalysisResults;

    public function putRecommendations(string $auditUuid, AIRecommendationResult $result): void;

    public function getRecommendations(string $auditUuid): ?AIRecommendationResult;

    /**
     * Stores one analyzer's output for $auditUuid under $key (one of
     * {@see \App\Audit\Jobs\AnalyzeChunkJob::ANALYZER_KEYS}). Each key
     * lives in its own cache slot — never a shared/merged structure —
     * so multiple AnalyzeChunkJob chunks writing different keys for
     * the same audit at the same time (parallel processing) can never
     * race or clobber one another's writes.
     */
    public function putAnalysisFragment(string $auditUuid, string $key, object $result): void;

    /**
     * Returns every analyzer fragment written so far for $auditUuid,
     * keyed by analyzer key. Only keys that have actually been cached
     * are present — a chunk that never ran (or is still pending/being
     * retried) is simply absent, not null-filled, so the caller can
     * tell "not run yet" apart from "ran and produced nothing".
     *
     * @return array<string, object>
     */
    public function getAnalysisFragments(string $auditUuid): array;

    /**
     * Removes every analyzer fragment cached for $auditUuid. Called
     * once the fragments have been folded into a single
     * AnalysisResults via putAnalysisResults(), so the short-lived
     * per-analyzer intermediate cache entries don't linger and use
     * memory after a completed/failed audit.
     */
    public function forgetFragments(string $auditUuid): void;

    /**
     * Removes the cached analysis results and recommendations for a
     * single audit (does not touch the URL-keyed fetch/crawl cache,
     * which is intentionally shared across audits).
     */
    public function forget(string $auditUuid): void;

    /**
     * Records how far along $auditUuid's pipeline is, for the progress
     * bar the result page polls. $percent is 0-100; $label is a short
     * human-readable description of the current step (e.g. "Crawling
     * pages (7 of 25)"). Overwrites whatever was stored before — the
     * caller is always reporting the current, not incremental, state.
     */
    public function putProgress(string $auditUuid, int $percent, string $label): void;

    /**
     * @return array{percent: int, label: string}|null null when no
     *         progress has been recorded yet (e.g. the very first
     *         moment after an audit is queued).
     */
    public function getProgress(string $auditUuid): ?array;
}