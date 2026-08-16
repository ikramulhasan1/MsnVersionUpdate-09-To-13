<?php

declare(strict_types=1);

namespace App\Discovery\Analytics\DTO;

/**
 * The Website Discovery module's "Search Analytics" mini-dashboard
 * data (Phase I1) — see App\Discovery\Analytics\SearchAnalyticsService
 * for how each field is computed.
 */
final readonly class DiscoverySearchAnalytics
{
    /**
     * @param  int  $totalCount  every matching site — a real COUNT(*) query,
     *                           not limited to the sample below.
     * @param  int  $sampledCount  how many rows $poorSeoCount/$highOpportunityCount/
     *                             $technologyBreakdown were actually computed from — equal to
     *                             $totalCount unless it exceeds SearchAnalyticsService's own sample
     *                             cap, in which case those three figures are a sample, not exact.
     * @param  int  $poorSeoCount  sites with a seo_score below SearchAnalyticsService's
     *                             threshold (same default as App\Discovery\Scoring\ServiceOpportunityDetector's
     *                             own SEO rule, for consistency).
     * @param  int  $highOpportunityCount  sites whose live-computed
     *                                     App\Discovery\Scoring\OpportunityScorer score maps to
     *                                     App\Discovery\Enums\OpportunityLevel::HIGH.
     * @param  array<string, int>  $technologyBreakdown  detected CMS name => count,
     *                                                   sorted highest first, capped to a small number of categories for a
     *                                                   readable chart.
     */
    public function __construct(
        public int $totalCount,
        public int $sampledCount,
        public int $poorSeoCount,
        public int $highOpportunityCount,
        public array $technologyBreakdown,
    ) {}

    /**
     * Whether $poorSeoCount/$highOpportunityCount/$technologyBreakdown
     * are exact (every matching site was examined) or only a sample.
     */
    public function isSampled(): bool
    {
        return $this->sampledCount < $this->totalCount;
    }
}
