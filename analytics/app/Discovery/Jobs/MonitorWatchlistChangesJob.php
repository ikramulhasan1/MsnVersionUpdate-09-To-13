<?php

declare(strict_types=1);

namespace App\Discovery\Jobs;

use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Audit\Security\DTO\SecurityResult;
use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Technology\TechnologyDetector;
use App\Discovery\Enums\WatchlistChangeType;
use App\Discovery\Enums\WebsiteConnectivityStatus;
use App\Discovery\Jobs\Concerns\BuildsSinglePageCrawlResult;
use App\Models\DiscoveredWebsite;
use App\Models\DiscoveryWatchlistChange;
use App\Models\DiscoveryWatchlistItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Re-checks one watchlisted site's SEO score, Performance score,
 * Technology stack, SSL status, and online/offline status, logging one
 * discovery_watchlist_changes row per metric that changed since the
 * last check — the "watchlist monitoring" job (Phase G2). Explicitly
 * STRUCTURE + detection logic only per this phase's own scope — no
 * notification channel (email, etc.) reads these change rows yet; a
 * future phase is expected to add one, querying
 * DiscoveryWatchlistChange the same way any other reader of this log
 * table would.
 *
 * Reuses the exact same homepage-only "quick scan" technique
 * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob (Phase D0) already
 * established — same analyzers, same single-page CrawlResult
 * construction (now shared via
 * App\Discovery\Jobs\Concerns\BuildsSinglePageCrawlResult, extracted
 * once this job needed the identical logic) — rather than a heavier
 * multi-page recheck; a watchlist monitor firing periodically for
 * potentially many sites needs to stay as cheap per-site as the
 * original enrichment did.
 *
 * Also writes the freshly observed values back onto the
 * DiscoveredWebsite row (seo_score, performance_score, ssl_status,
 * connectivity_status, and the technology columns) — the same
 * "detect AND persist" behavior EnrichDiscoveredWebsiteJob has, so a
 * watchlisted site's row stays current between full re-enrichments,
 * not just its change log.
 *
 * Dispatched per DiscoveryWatchlistItem (matching
 * EnrichDiscoveredWebsiteJob's own per-DiscoveredWebsite dispatch
 * shape) — nothing in this module loops over the watchlist and
 * dispatches this job yet; that periodic trigger (an artisan command +
 * routes/console.php schedule entry, the same shape Phase F4 already
 * established for RunScheduledDiscoverySearchJob) is left to a future
 * phase, matching this phase's own "structure first" scope.
 */
final class MonitorWatchlistChangesJob implements ShouldQueue
{
    use BuildsSinglePageCrawlResult;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly DiscoveryWatchlistItem $watchlistItem,
    ) {}

    public function handle(
        WebsiteFetcherServiceInterface $fetcher,
        SeoAnalyzerServiceInterface $seoAnalyzer,
        PerformanceAnalyzer $performanceAnalyzer,
        SecurityAnalyzer $securityAnalyzer,
        TechnologyDetector $technologyDetector,
    ): void {
        $website = $this->watchlistItem->discoveredWebsite;

        if ($website === null) {
            // The site was deleted out from under this watchlist entry
            // (cascadeOnDelete() should normally remove the watchlist
            // row too — see database/migrations/2026_08_14_000002_create_discovery_watchlist_table.php
            // — but a stale queued job payload could still reference a
            // row that's since been cleaned up). Nothing to check.
            return;
        }

        $now = now();
        $fetch = $fetcher->fetch($website->url);

        $newConnectivity = $fetch->success ? WebsiteConnectivityStatus::ONLINE : WebsiteConnectivityStatus::OFFLINE;

        $this->logChangeIfDifferent(
            $website,
            WatchlistChangeType::CONNECTIVITY,
            $website->connectivity_status?->value,
            $newConnectivity->value,
            $now,
        );

        if (! $fetch->success) {
            // A failed fetch has nothing to score/detect technology
            // from — record the connectivity change (above) and the
            // new connectivity_status, and stop; every other column
            // stays exactly as it was, the same "don't overwrite real
            // data with nothing" reasoning
            // EnrichDiscoveredWebsiteJob::handle() already documents
            // for its own failed-fetch case.
            $website->update(['connectivity_status' => $newConnectivity]);

            return;
        }

        $page = $this->crawledPageFrom($fetch);

        $seoResult = $seoAnalyzer->analyze($this->singlePageCrawlResult($fetch, $page));
        $performanceResult = $performanceAnalyzer->analyze($page);
        $securityResult = $securityAnalyzer->analyze($fetch);
        $technologyResult = $technologyDetector->detect($fetch);

        $this->logChangeIfDifferent(
            $website,
            WatchlistChangeType::SEO_SCORE,
            $this->toComparableString($website->seo_score),
            $this->toComparableString($seoResult->averageScore),
            $now,
        );

        $this->logChangeIfDifferent(
            $website,
            WatchlistChangeType::PERFORMANCE_SCORE,
            $this->toComparableString($website->performance_score),
            $this->toComparableString($performanceResult->score),
            $now,
        );

        $newSslStatus = $this->sslStatusFrom($securityResult);

        $this->logChangeIfDifferent(
            $website,
            WatchlistChangeType::SSL_STATUS,
            $website->ssl_status,
            $newSslStatus,
            $now,
        );

        $newCms = $this->technologyColumnValue($technologyResult, ['CMS']);
        $newFramework = $this->technologyColumnValue(
            $technologyResult,
            ['Backend Framework', 'JavaScript Framework', 'CSS Framework'],
        );
        $newEcommercePlatform = $this->technologyColumnValue($technologyResult, ['Ecommerce']);
        $newServer = $technologyResult->serverHeader;
        $newCdn = $this->technologyColumnValue($technologyResult, ['Infrastructure']);

        $this->logChangeIfDifferent(
            $website,
            WatchlistChangeType::TECHNOLOGY,
            $this->technologySnapshot(
                $website->cms,
                $website->framework,
                $website->ecommerce_platform,
                $website->server,
                $website->cdn,
            ),
            $this->technologySnapshot($newCms, $newFramework, $newEcommercePlatform, $newServer, $newCdn),
            $now,
        );

        $website->update([
            'connectivity_status' => $newConnectivity,
            'seo_score' => $seoResult->averageScore,
            'performance_score' => $performanceResult->score,
            'ssl_status' => $newSslStatus,
            'cms' => $newCms,
            'framework' => $newFramework,
            'ecommerce_platform' => $newEcommercePlatform,
            'server' => $newServer,
            'cdn' => $newCdn,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }

    /**
     * Writes nothing when $oldValue and $newValue are the same (a
     * "check that found nothing different" produces zero rows — see
     * this table's own migration docblock for why it's a log of
     * changes, not of checks).
     */
    private function logChangeIfDifferent(
        DiscoveredWebsite $website,
        WatchlistChangeType $type,
        ?string $oldValue,
        ?string $newValue,
        Carbon $detectedAt,
    ): void {
        if ($oldValue === $newValue) {
            return;
        }

        DiscoveryWatchlistChange::query()->create([
            'discovered_website_id' => $website->id,
            'change_type' => $type,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'detected_at' => $detectedAt,
        ]);
    }

    private function toComparableString(?int $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }

    /**
     * The check named "SSL" (see SecurityAnalyzer::CHECK_NAMES) mapped
     * to its own SecurityCheckStatus value ('pass'/'warning'/'fail') —
     * null if that check isn't present in this result for any reason.
     */
    private function sslStatusFrom(SecurityResult $securityResult): ?string
    {
        foreach ($securityResult->checks as $check) {
            if ($check->check === 'SSL') {
                return $check->status->value;
            }
        }

        return null;
    }

    /**
     * A single comparable string representing all five technology
     * columns together — see WatchlistChangeType::TECHNOLOGY's own
     * docblock for why these are logged as one combined change rather
     * than five separate ones. json_encode() failing (only possible
     * for genuinely invalid UTF-8 in one of these strings) falls back
     * to '{}' rather than null/false, so this always returns a value
     * two snapshots can be compared with the simple === check
     * logChangeIfDifferent() already uses.
     */
    private function technologySnapshot(
        ?string $cms,
        ?string $framework,
        ?string $ecommercePlatform,
        ?string $server,
        ?string $cdn,
    ): string {
        return json_encode([
            'cms' => $cms,
            'framework' => $framework,
            'ecommerce_platform' => $ecommercePlatform,
            'server' => $server,
            'cdn' => $cdn,
        ]) ?: '{}';
    }
}
