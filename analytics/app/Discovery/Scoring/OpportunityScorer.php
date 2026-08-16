<?php

declare(strict_types=1);

namespace App\Discovery\Scoring;

use App\Discovery\Scoring\DTO\OpportunityScoreResult;
use App\Models\DiscoveredWebsite;
use DateTimeInterface;

/**
 * Turns a DiscoveredWebsite's already-computed columns into a single
 * "how much service-sales opportunity does this site represent" score
 * — the Website Discovery module's counterpart to
 * App\Audit\Lead\ProspectQualificationScorer, following that class's
 * own pattern closely: constructor-injected, tunable max-points-per-
 * bucket weights that sum to 100 by default, a $breakdown array whose
 * sum IS $score (never a separately-fabricated number), and a
 * human-readable $summary built from that same breakdown.
 *
 * Every point in $breakdown is traceable to a real, already-populated
 * DiscoveredWebsite column — never a new heuristic invented at this
 * layer. With the constructor defaults below (also the documented
 * default weighting), the formula per bucket is:
 *
 *  - 'seo' (0-25 pts, $seoMaxPoints): derived from
 *    DiscoveredWebsite::$seo_score, a 0-100 *health* score (higher =
 *    better SEO). Inverted here because a site with WORSE SEO is a
 *    STRONGER opportunity to sell SEO work against, not a weaker one:
 *    points = round((100 - seo_score) * (25 / 100)). 0 when
 *    seo_score is null (not yet enriched — see
 *    App\Discovery\Jobs\EnrichDiscoveredWebsiteJob) — unknown health is
 *    never treated as "unhealthy" for scoring purposes, the same
 *    convention ProspectQualificationScorer's own website_issues bucket
 *    documents.
 *
 *  - 'performance' (0-25 pts, $performanceMaxPoints): the identical
 *    inversion, from DiscoveredWebsite::$performance_score.
 *
 *  - 'mobile' (0-20 pts, $mobileMaxPoints): the identical inversion,
 *    from DiscoveredWebsite::$mobile_score. That column is not
 *    populated by any current enrichment job (see
 *    EnrichDiscoveredWebsiteJob's own docblock — it deliberately
 *    writes seo/performance/security/accessibility only, never
 *    mobile_score), so this bucket contributes 0 for essentially every
 *    site today; the formula is still implemented correctly and ready
 *    the moment something starts populating that column.
 *
 *  - 'accessibility' (0-15 pts, $accessibilityMaxPoints): the identical
 *    inversion, from DiscoveredWebsite::$accessibility_score.
 *
 *  - 'technology_age' (0-15 pts, $technologyAgeMaxPoints): NOT a
 *    score inversion — derived from how long it's been since
 *    DiscoveredWebsite::$last_updated_at, on the theory that a site
 *    that hasn't visibly changed in a long time is more likely running
 *    dated technology worth upgrading. Full points at 2+ years
 *    ($staleAfterDays), 60% at 1-2 years, 30% at 6-12 months, 0 under
 *    6 months. 0 when last_updated_at is null (not yet enriched — no
 *    current job populates this column either, the same "unknown, not
 *    fabricated" convention every other bucket here already follows).
 *
 * The five max-points values above always sum to 100 with the
 * constructor defaults, so $score is simply array_sum($breakdown) —
 * never a separately-fabricated number. Every weight is constructor-
 * injected so it can be tuned (e.g. against real close-rate data) without
 * editing this class, the same reasoning
 * ProspectQualificationScorer documents for its own weights.
 */
final class OpportunityScorer
{
    public function __construct(
        private readonly int $seoMaxPoints = 25,
        private readonly int $performanceMaxPoints = 25,
        private readonly int $mobileMaxPoints = 20,
        private readonly int $accessibilityMaxPoints = 15,
        private readonly int $technologyAgeMaxPoints = 15,
        private readonly int $staleAfterDays = 730,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {}

    public function score(DiscoveredWebsite $website): OpportunityScoreResult
    {
        $breakdown = [
            'seo' => $this->invertedPoints($website->seo_score, $this->seoMaxPoints),
            'performance' => $this->invertedPoints($website->performance_score, $this->performanceMaxPoints),
            'mobile' => $this->invertedPoints($website->mobile_score, $this->mobileMaxPoints),
            'accessibility' => $this->invertedPoints($website->accessibility_score, $this->accessibilityMaxPoints),
            'technology_age' => $this->technologyAgePoints($website->last_updated_at),
        ];

        $score = array_sum($breakdown);
        $grade = $this->grade($score);

        return new OpportunityScoreResult(
            score: $score,
            grade: $grade,
            breakdown: $breakdown,
            summary: $this->summary($score, $grade, $breakdown),
        );
    }

    /**
     * Shared by the seo/performance/mobile/accessibility buckets — all
     * four are the identical "0-100 health score, worse health means
     * more points" inversion, just against a different column and a
     * different $maxPoints cap.
     */
    private function invertedPoints(?int $healthScore, int $maxPoints): int
    {
        if ($healthScore === null) {
            return 0;
        }

        $clamped = max(0, min(100, $healthScore));

        return (int) round((100 - $clamped) * ($maxPoints / 100));
    }

    private function technologyAgePoints(?DateTimeInterface $lastUpdatedAt): int
    {
        if ($lastUpdatedAt === null) {
            return 0;
        }

        $daysSinceUpdate = now()->diffInDays($lastUpdatedAt);

        return match (true) {
            $daysSinceUpdate >= $this->staleAfterDays => $this->technologyAgeMaxPoints,
            $daysSinceUpdate >= (int) ($this->staleAfterDays / 2) => (int) round($this->technologyAgeMaxPoints * 0.6),
            $daysSinceUpdate >= (int) ($this->staleAfterDays / 4) => (int) round($this->technologyAgeMaxPoints * 0.3),
            default => 0,
        };
    }

    private function grade(int $score): string
    {
        return match (true) {
            $score >= $this->gradeAThreshold => 'A',
            $score >= $this->gradeBThreshold => 'B',
            $score >= $this->gradeCThreshold => 'C',
            $score >= $this->gradeDThreshold => 'D',
            default => 'F',
        };
    }

    /**
     * @param  array<string, int>  $breakdown
     */
    private function summary(int $score, string $grade, array $breakdown): string
    {
        return sprintf(
            'Opportunity score %d/100 (grade %s) — %d point(s) from SEO, %d from Performance, '
                .'%d from Mobile, %d from Accessibility, %d from Technology Age.',
            $score,
            $grade,
            $breakdown['seo'],
            $breakdown['performance'],
            $breakdown['mobile'],
            $breakdown['accessibility'],
            $breakdown['technology_age'],
        );
    }
}
