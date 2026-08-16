<?php

declare(strict_types=1);

namespace App\Discovery\Scoring;

use App\Discovery\Enums\OpportunityFilter;
use App\Discovery\Scoring\DTO\ServiceOpportunity;
use App\Models\DiscoveredWebsite;

/**
 * Evaluates a DiscoveredWebsite against the exact six rules
 * App\Discovery\Enums\OpportunityFilter::criterion() already documented
 * back in Phase C4 — that enum's own docblock said it was "defined
 * ahead of a future OpportunityFilterService" that would actually apply
 * those criteria; this class is that service, finally built.
 *
 * Each rule below, and how honest it can be with the data this module
 * actually has:
 *
 *  - SEO (score < $seoThreshold, default 50): real, direct — reads
 *    DiscoveredWebsite::$seo_score.
 *  - PERFORMANCE (score < $performanceThreshold, default 40): real,
 *    direct — reads $performance_score.
 *  - MOBILE (score < $mobileThreshold, default 50): real formula, but
 *    effectively dormant today — no current enrichment job populates
 *    $mobile_score (see App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's
 *    own docblock), so this rule won't fire for most sites yet; it's
 *    still implemented correctly and ready the moment something starts
 *    populating that column, the same "structure ahead of the data"
 *    reasoning App\Discovery\Scoring\OpportunityScorer's own mobile
 *    bucket already documents.
 *  - SECURITY (not served over HTTPS): real, direct — checks
 *    $url's own scheme; every discovered site has a URL from the
 *    moment it's created, so this rule can fire for any site,
 *    enriched or not.
 *  - TECHNOLOGY (running an outdated CMS/framework version): this
 *    module stores no VERSION numbers for detected technology (only
 *    names — see TechnologyDetector::TECHNOLOGY_NAMES), so an actual
 *    "outdated version" check isn't possible with current data.
 *    hasTechnologyOpportunity() uses the closest honest proxy instead:
 *    enrichment ran (seo_score is set) but found no CMS and no
 *    framework at all — a site with no recognizable modern technology
 *    stack is itself a reasonable signal of dated technology, even
 *    without a version number to point to.
 *  - DESIGN (outdated/poorly structured design/UX signals): always
 *    false today — this module's lightweight enrichment captures no
 *    UX/design-specific signal at all, unlike the full Audit engine's
 *    own UiUxAnalyzer. Kept as an explicit, always-false rule (rather
 *    than omitted) so the method — and the six rules the prompt asked
 *    for — has a real place to plug a genuine signal into once one
 *    exists, without this class's shape changing.
 */
final class ServiceOpportunityDetector
{
    public function __construct(
        private readonly int $seoThreshold = 50,
        private readonly int $performanceThreshold = 40,
        private readonly int $mobileThreshold = 50,
    ) {}

    /**
     * @return array<int, ServiceOpportunity>
     */
    public function detect(DiscoveredWebsite $website): array
    {
        $opportunities = [];

        if ($this->hasSeoOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::SEO,
                serviceName: 'SEO Service Opportunity',
                reason: sprintf(
                    'SEO score is %d, below the %d threshold.',
                    $website->seo_score,
                    $this->seoThreshold,
                ),
            );
        }

        if ($this->hasPerformanceOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::PERFORMANCE,
                serviceName: 'Performance Service Opportunity',
                reason: sprintf(
                    'Performance score is %d, below the %d threshold.',
                    $website->performance_score,
                    $this->performanceThreshold,
                ),
            );
        }

        if ($this->hasMobileOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::MOBILE,
                serviceName: 'Mobile Service Opportunity',
                reason: sprintf(
                    'Mobile score is %d, below the %d threshold.',
                    $website->mobile_score,
                    $this->mobileThreshold,
                ),
            );
        }

        if ($this->hasSecurityOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::SECURITY,
                serviceName: 'Security Service Opportunity',
                reason: 'Site is not served over HTTPS.',
            );
        }

        if ($this->hasTechnologyOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::TECHNOLOGY,
                serviceName: 'Technology Service Opportunity',
                reason: 'No modern CMS or framework was detected on this site.',
            );
        }

        if ($this->hasDesignOpportunity($website)) {
            $opportunities[] = new ServiceOpportunity(
                type: OpportunityFilter::DESIGN,
                serviceName: 'Design Service Opportunity',
                reason: 'Outdated or poorly structured design/UX signals detected.',
            );
        }

        return $opportunities;
    }

    private function hasSeoOpportunity(DiscoveredWebsite $website): bool
    {
        return $website->seo_score !== null && $website->seo_score < $this->seoThreshold;
    }

    private function hasPerformanceOpportunity(DiscoveredWebsite $website): bool
    {
        return $website->performance_score !== null && $website->performance_score < $this->performanceThreshold;
    }

    private function hasMobileOpportunity(DiscoveredWebsite $website): bool
    {
        return $website->mobile_score !== null && $website->mobile_score < $this->mobileThreshold;
    }

    private function hasSecurityOpportunity(DiscoveredWebsite $website): bool
    {
        return ! str_starts_with(strtolower($website->url), 'https://');
    }

    private function hasTechnologyOpportunity(DiscoveredWebsite $website): bool
    {
        return $website->seo_score !== null
            && $website->cms === null
            && $website->framework === null;
    }

    /**
     * Always false — see this class's own docblock for why: no signal
     * for this rule exists anywhere in this module's data yet.
     */
    private function hasDesignOpportunity(DiscoveredWebsite $website): bool
    {
        return false;
    }
}
