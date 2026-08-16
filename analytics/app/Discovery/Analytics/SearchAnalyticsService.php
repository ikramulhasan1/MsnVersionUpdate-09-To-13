<?php

declare(strict_types=1);

namespace App\Discovery\Analytics;

use App\Discovery\Analytics\DTO\DiscoverySearchAnalytics;
use App\Discovery\Enums\OpportunityLevel;
use App\Discovery\Scoring\OpportunityScorer;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Search\WebsiteSearchService;
use App\Models\DiscoveredWebsite;
use Illuminate\Support\Collection;

/**
 * Computes the Website Discovery module's "Search Analytics" mini-
 * dashboard (Phase I1) from the current search's own
 * DiscoveryFilterCriteria — reuses WebsiteSearchService::query() (the
 * same filtered query List View/Map View/export already run) rather
 * than building a second, separate filter implementation.
 *
 * $totalCount is a real COUNT(*) query — cheap, no model hydration,
 * always exact regardless of how many sites match. The other three
 * figures (Poor SEO count, High Opportunity count, Technology
 * breakdown) need each matching DiscoveredWebsite's own columns
 * examined in PHP — Poor SEO could be a plain SQL WHERE, but High
 * Opportunity can't (App\Discovery\Scoring\OpportunityScorer's formula
 * lives in PHP, not SQL, and duplicating it as a raw SQL expression
 * here would risk the two drifting out of sync) — so all three are
 * computed together from one capped SAMPLE query
 * (self::SAMPLE_LIMIT), not the full matching set, to keep this
 * service's own cost bounded regardless of how large a search's result
 * set is. DiscoverySearchAnalytics::isSampled() tells a caller whether
 * that sample was actually smaller than the true total.
 */
final class SearchAnalyticsService
{
    private const int SAMPLE_LIMIT = 500;

    /**
     * Matches App\Discovery\Scoring\ServiceOpportunityDetector's own
     * default SEO threshold — "Poor SEO" here means the same thing a
     * detected "SEO Service Opportunity" badge already means on a
     * site's own detail page.
     */
    private const int SEO_POOR_THRESHOLD = 50;

    private const int TECHNOLOGY_BREAKDOWN_LIMIT = 8;

    public function __construct(
        private readonly WebsiteSearchService $websiteSearchService,
        private readonly OpportunityScorer $opportunityScorer = new OpportunityScorer,
    ) {}

    public function analyze(DiscoveryFilterCriteria $criteria): DiscoverySearchAnalytics
    {
        $totalCount = $this->websiteSearchService->query($criteria)->count();

        $sample = $this->websiteSearchService->query($criteria)
            ->limit(self::SAMPLE_LIMIT)
            ->get();

        return new DiscoverySearchAnalytics(
            totalCount: $totalCount,
            sampledCount: $sample->count(),
            poorSeoCount: $this->poorSeoCount($sample),
            highOpportunityCount: $this->highOpportunityCount($sample),
            technologyBreakdown: $this->technologyBreakdown($sample),
        );
    }

    /**
     * @param  Collection<int, DiscoveredWebsite>  $sample
     */
    private function poorSeoCount($sample): int
    {
        return $sample
            ->filter(static fn (DiscoveredWebsite $website): bool => $website->seo_score !== null
                && $website->seo_score < self::SEO_POOR_THRESHOLD)
            ->count();
    }

    /**
     * @param  Collection<int, DiscoveredWebsite>  $sample
     */
    private function highOpportunityCount($sample): int
    {
        return $sample
            ->filter(function (DiscoveredWebsite $website): bool {
                $level = OpportunityLevel::fromScore($this->opportunityScorer->score($website)->score);

                return $level === OpportunityLevel::HIGH;
            })
            ->count();
    }

    /**
     * @param  Collection<int, DiscoveredWebsite>  $sample
     * @return array<string, int>
     */
    private function technologyBreakdown($sample): array
    {
        return $sample
            ->pluck('cms')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(self::TECHNOLOGY_BREAKDOWN_LIMIT)
            ->all();
    }
}
