<?php

declare(strict_types=1);

namespace App\Discovery\Export;

use App\Discovery\Export\DTO\DiscoveryExportRow;
use App\Discovery\Scoring\OpportunityScorer;
use App\Models\DiscoveredWebsite;
use Illuminate\Support\Collection;

/**
 * Maps a Collection of DiscoveredWebsite into a Collection of
 * DiscoveryExportRow — the one place every export format (Excel, CSV,
 * PDF, JSON — Phase H2) gets its row data from, so all four formats
 * always show identical columns/values for the same result set.
 *
 * 'Opportunity Score' is computed live via OpportunityScorer rather
 * than read off DiscoveredWebsite::$opportunity_score — that column is
 * never populated by any current job (see
 * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob's own docblock), the
 * same reason result-card.blade.php/discovery/show.blade.php (Phase
 * G3) already switched to a live OpportunityScorer call instead of
 * that column.
 *
 * 'Technology' combines framework/ecommerce_platform/server/cdn (NOT
 * cms, which gets its own column, matching the prompt's own explicit
 * "Technology, CMS" column split) — the same four-of-five technology
 * columns result-card.blade.php's own $technologies collection reads,
 * minus cms.
 */
final class DiscoveredWebsitesToExportRows
{
    public function __construct(
        private readonly OpportunityScorer $opportunityScorer = new OpportunityScorer,
    ) {}

    /**
     * @param  Collection<int, DiscoveredWebsite>  $websites
     * @return Collection<int, DiscoveryExportRow>
     */
    public function map(Collection $websites): Collection
    {
        return $websites->map(fn (DiscoveredWebsite $website): DiscoveryExportRow => new DiscoveryExportRow(
            businessName: $website->business_name ?? $website->domain,
            website: $website->url,
            industry: $website->industry,
            country: $website->country,
            city: $website->city,
            technology: $this->technologySummary($website),
            cms: $website->cms,
            seoScore: $website->seo_score,
            performanceScore: $website->performance_score,
            securityScore: $website->security_score,
            accessibilityScore: $website->accessibility_score,
            mobileScore: $website->mobile_score,
            opportunityScore: $this->opportunityScorer->score($website)->score,
            email: $website->email,
            phone: $website->phone,
            socialLinks: $this->socialLinksSummary($website),
        ));
    }

    private function technologySummary(DiscoveredWebsite $website): ?string
    {
        $parts = collect([$website->framework, $website->ecommerce_platform, $website->server, $website->cdn])
            ->filter();

        return $parts->isEmpty() ? null : $parts->implode(', ');
    }

    private function socialLinksSummary(DiscoveredWebsite $website): ?string
    {
        $profiles = $website->social_profiles;

        if (empty($profiles)) {
            return null;
        }

        return collect($profiles)
            ->map(static fn (string $url, string $platform): string => sprintf('%s: %s', ucfirst($platform), $url))
            ->implode('; ');
    }
}
