<?php

declare(strict_types=1);

namespace App\Audit\Lead;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;

/**
 * Turns an already-assembled AnalysisResults into a single "how good a
 * sales lead is this" score for the Lead Intelligence features
 * (dashboard card, Excel/PDF export, Outreach Draft prioritization).
 *
 * Runs strictly after every analyzer it reads from — BusinessOpportunity,
 * BusinessSignals, and TechnologyUpgradeAnalyzer — which is why it's
 * wired into AssembleAnalysisResultsJob (assembly time, once $results is
 * already built) rather than AnalyzeChunkJob, the same reasoning
 * TechnologyUpgradeAnalyzer documents for itself.
 *
 * Every point in $breakdown is traceable to a real, already-computed
 * analyzer output — never a new heuristic invented at this layer. With
 * the constructor defaults below (also the documented default
 * weighting), the formula per bucket is:
 *
 *  - 'website_issues' (0-60 pts, $websiteIssuesMaxPoints): derived from
 *    BusinessOpportunityResult::$score, which is a 0-100 *health* score
 *    (higher = fewer problems). Inverted here because a website with
 *    more real problems is a *stronger* sales lead, not a weaker one:
 *    points = round((100 - businessOpportunity->score) * (60 / 100)).
 *    When BusinessOpportunityResult::$score itself is null (no business
 *    opportunity checks have run yet), this bucket contributes 0 —
 *    unknown health is never treated as "unhealthy" for scoring purposes.
 *
 *  - 'business_signals' (0-25 pts, $businessSignalsMaxPoints): derived
 *    from BusinessSignalsResult::$signals, the fraction of detected
 *    signals (careers/hiring/blog_update/funding/new_product) that are
 *    true: points = round((count(true signals) / count(all signals)) * 25).
 *    0 when $businessSignals is null or carries no signals at all.
 *
 *  - 'technology_upgrade_opportunities' (0-15 pts, $technologyMaxPoints):
 *    derived from AnalysisResults::$technologyUpgradeOpportunities — a
 *    flat 5 points ($technologyPointsPerOpportunity) per real
 *    opportunity found, capped at 15: points =
 *    min(count(opportunities) * 5, 15).
 *
 * The three max points above always sum to 100 with the constructor
 * defaults, so $score is simply array_sum($breakdown) — never a
 * separately-fabricated number. Every weight is constructor-injected so
 * it can be tuned (e.g. against real close-rate data, per Group S) without
 * editing this class.
 */
final class ProspectQualificationScorer
{
    public function __construct(
        private readonly int $websiteIssuesMaxPoints = 60,
        private readonly int $businessSignalsMaxPoints = 25,
        private readonly int $technologyMaxPoints = 15,
        private readonly int $technologyPointsPerOpportunity = 5,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function score(AnalysisResults $results): ?ProspectQualificationResult
    {
        // Website-issue data is this score's primary input — without a
        // BusinessOpportunityResult there is nothing real to qualify
        // against, so a null here means "not computable", never a
        // fabricated 0.
        if ($results->businessOpportunity === null) {
            return null;
        }

        $breakdown = [
            'website_issues' => $this->websiteIssuesPoints($results->businessOpportunity->score),
            'business_signals' => $this->businessSignalsPoints($results->businessSignals?->signals ?? []),
            'technology_upgrade_opportunities' => $this->technologyPoints($results->technologyUpgradeOpportunities),
        ];

        $score = array_sum($breakdown);
        $grade = $this->grade($score);

        return new ProspectQualificationResult(
            score: $score,
            grade: $grade,
            breakdown: $breakdown,
            summary: $this->summary($score, $grade, $breakdown),
        );
    }

    private function websiteIssuesPoints(?int $businessOpportunityScore): int
    {
        if ($businessOpportunityScore === null) {
            return 0;
        }

        $healthScore = max(0, min(100, $businessOpportunityScore));

        return (int) round((100 - $healthScore) * ($this->websiteIssuesMaxPoints / 100));
    }

    /**
     * @param  array<string, bool>  $signals
     */
    private function businessSignalsPoints(array $signals): int
    {
        if ($signals === []) {
            return 0;
        }

        $detectedCount = count(array_filter($signals));
        $totalCount = count($signals);

        return (int) round(($detectedCount / $totalCount) * $this->businessSignalsMaxPoints);
    }

    /**
     * @param  array<int, TechnologyUpgradeOpportunity>  $opportunities
     */
    private function technologyPoints(array $opportunities): int
    {
        return min(count($opportunities) * $this->technologyPointsPerOpportunity, $this->technologyMaxPoints);
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
            'Prospect qualification score %d/100 (grade %s) — %d point(s) from website issues, '
                .'%d point(s) from business signals, %d point(s) from technology upgrade opportunities.',
            $score,
            $grade,
            $breakdown['website_issues'],
            $breakdown['business_signals'],
            $breakdown['technology_upgrade_opportunities'],
        );
    }
}
