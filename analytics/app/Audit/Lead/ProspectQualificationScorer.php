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
 *
 *  - 'business_signals' (0-25 pts, $businessSignalsMaxPoints): derived
 *    from BusinessSignalsResult::$signals, the fraction of detected
 *    signals (careers/hiring/blog_update/funding/new_product) that are
 *    true: points = round((count(true signals) / count(all signals)) * 25).
 *
 *  - 'technology_upgrade_opportunities' (0-15 pts, $technologyMaxPoints):
 *    derived from AnalysisResults::$technologyUpgradeOpportunities — a
 *    flat 5 points ($technologyPointsPerOpportunity) per real
 *    opportunity found, capped at 15: points =
 *    min(count(opportunities) * 5, 15). This bucket's own data is
 *    always genuinely "available" even when it finds zero
 *    opportunities — an empty result here means "checked, found none",
 *    never "couldn't check" — unlike the two buckets above, which can
 *    each genuinely be unavailable (see PRODUCTION INCIDENT below).
 *
 * The three max points above always sum to 100 with the constructor
 * defaults.
 *
 * PRODUCTION INCIDENT (Phase M6) — read before reverting to "null
 * BusinessOpportunityResult means the whole score is null": this used
 * to return null outright whenever $results->businessOpportunity was
 * null (e.g. the entry page's own multi-page analysis failed, or timed
 * out — a real, non-rare failure mode, not an edge case), even when
 * BusinessSignalsResult and/or real technology upgrade opportunities
 * WERE available. That threw away two-fifths of this score's own
 * inputs whenever the single hardest-to-compute one happened to be
 * missing, showing "no Prospect Qualification data at all" for an
 * audit that actually had real, usable signal.
 *
 * The fix: only return null when literally NONE of the three inputs
 * are available (matching the same "any subset present is enough"
 * rule App\Audit\Export\Support\AnalysisResultsToDashboardCategories::leadIntelligence()
 * already applies for the SAME reason). Otherwise, $score sums
 * whichever buckets ARE computable (an unavailable bucket contributes
 * 0, same as before), but $maxPossibleScore now honestly reflects
 * that a partial score isn't out of 100 — it's out of however many
 * points were actually reachable — and $isPartial/$availableBuckets
 * (see ProspectQualificationResult's own docblock for both) let a
 * caller show that honestly rather than silently presenting a partial
 * score as if it were a complete one. $grade is deliberately left null
 * for a partial result (see ProspectQualificationResult::$grade's own
 * docblock) — a letter grade implies "graded on the full rubric",
 * which a partial result, by definition, wasn't.
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
        $businessOpportunityAvailable = $results->businessOpportunity !== null;
        $businessSignalsAvailable = $results->businessSignals !== null
            && $results->businessSignals->signals !== [];

        // Only genuinely nothing to work with returns null now — see
        // this class's own PRODUCTION INCIDENT docblock above.
        if (
            ! $businessOpportunityAvailable
            && ! $businessSignalsAvailable
            && $results->technologyUpgradeOpportunities === []
        ) {
            return null;
        }

        $breakdown = [
            'website_issues' => $this->websiteIssuesPoints($results->businessOpportunity?->score),
            'business_signals' => $this->businessSignalsPoints($results->businessSignals?->signals ?? []),
            'technology_upgrade_opportunities' => $this->technologyPoints($results->technologyUpgradeOpportunities),
        ];

        $availableBuckets = [
            'website_issues' => $businessOpportunityAvailable,
            'business_signals' => $businessSignalsAvailable,
            // Always available — see this class's own docblock for why
            // an empty opportunities list is a real, complete answer,
            // not missing data.
            'technology_upgrade_opportunities' => true,
        ];

        $maxPossibleScore = ($businessOpportunityAvailable ? $this->websiteIssuesMaxPoints : 0)
            + ($businessSignalsAvailable ? $this->businessSignalsMaxPoints : 0)
            + $this->technologyMaxPoints;

        $fullMaxScore = $this->websiteIssuesMaxPoints + $this->businessSignalsMaxPoints + $this->technologyMaxPoints;
        $isPartial = $maxPossibleScore < $fullMaxScore;

        $score = array_sum($breakdown);
        $grade = $isPartial ? null : $this->grade($score);

        return new ProspectQualificationResult(
            score: $score,
            grade: $grade,
            breakdown: $breakdown,
            summary: $this->summary($score, $grade, $breakdown, $maxPossibleScore, $isPartial),
            maxPossibleScore: $maxPossibleScore,
            isPartial: $isPartial,
            availableBuckets: $availableBuckets,
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
    private function summary(int $score, ?string $grade, array $breakdown, int $maxPossibleScore, bool $isPartial): string
    {
        if ($isPartial) {
            return sprintf(
                'Prospect qualification score %d/%d (partial — not every scoring input was available for this '
                    .'audit) — %d point(s) from website issues, %d point(s) from business signals, %d point(s) '
                    .'from technology upgrade opportunities.',
                $score,
                $maxPossibleScore,
                $breakdown['website_issues'],
                $breakdown['business_signals'],
                $breakdown['technology_upgrade_opportunities'],
            );
        }

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