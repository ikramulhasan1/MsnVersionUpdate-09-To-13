<?php

declare(strict_types=1);

namespace App\Audit\Lead\DTO;

/**
 * Rolled-up "how good a sales lead is this" score, computed by
 * ProspectQualificationScorer purely from already-computed real fields
 * on AnalysisResults (BusinessOpportunityResult::$score,
 * BusinessSignalsResult::$signals,
 * AnalysisResults::$technologyUpgradeOpportunities) — never a new
 * heuristic invented at this layer. See ProspectQualificationScorer's
 * class docblock for the exact point-weighting formula that produces
 * $breakdown and $score.
 *
 * Deliberately has no url/analyzedAt of its own: it lives on
 * AnalysisResults (which already carries $url) as a single site-level
 * roll-up, the same pattern BusinessOpportunityScore uses for its own
 * nested aggregate.
 */
final readonly class ProspectQualificationResult implements \JsonSerializable
{
    /**
     * priority() threshold below which a score is 'High'. 75 was chosen
     * to line up with ProspectQualificationScorer's own grade bands (A
     * starts at 90, B at 75) so 'High' roughly tracks an A/B-range lead.
     */
    private const int HIGH_PRIORITY_THRESHOLD = 75;

    /**
     * priority() threshold below which a score is 'Medium' (and at or
     * above which it's 'Low'). Set to line up with the scorer's C-grade
     * band (60) plus a little headroom, so a lead with at least a
     * handful of real issues/signals/opportunities to sell against
     * still counts as worth a look rather than being buried in 'Low'.
     */
    private const int MEDIUM_PRIORITY_THRESHOLD = 45;

    // Both thresholds above are a starting default, not a data-validated
    // cutoff — there's no close-rate history behind them yet. Once real
    // outreach/close data exists for audits run through this scorer,
    // these should be tuned against it rather than left as-is indefinitely.

    /**
     * @param  int  $score  0-100, higher means a stronger sales lead (more real
     *         issues/signals/upgrade opportunities to sell against), not a
     *         quality/health score — see ProspectQualificationScorer.
     * @param  ?string  $grade  letter grade (A-F) derived from $score.
     * @param  array<string, int>  $breakdown  points contributed by each input,
     *         keyed 'website_issues', 'business_signals',
     *         'technology_upgrade_opportunities' — always present and summing
     *         to $score, so the score is auditable rather than a black box.
     * @param  string  $summary  human-readable overview of the qualification result.
     */
    public function __construct(
        public int $score,
        public ?string $grade,
        public array $breakdown,
        public string $summary,
    ) {
    }

    /**
     * Maps $score to a coarse sales-priority bucket: 'High' (score >=
     * self::HIGH_PRIORITY_THRESHOLD), 'Medium' (score >=
     * self::MEDIUM_PRIORITY_THRESHOLD), otherwise 'Low'.
     *
     * Typed ?string (rather than string) to mirror the original "null
     * when score is null" requirement this was specified against — but
     * on an already-constructed ProspectQualificationResult, $score is
     * always a real int (the whole result is ?ProspectQualificationResult
     * and simply doesn't exist when ProspectQualificationScorer had
     * nothing to score), so in practice this never actually returns
     * null. Callers holding a ?ProspectQualificationResult should check
     * that nullability before calling priority(), the same way they
     * already check before reading ->score.
     */
    public function priority(): ?string
    {
        return match (true) {
            $this->score >= self::HIGH_PRIORITY_THRESHOLD => 'High',
            $this->score >= self::MEDIUM_PRIORITY_THRESHOLD => 'Medium',
            default => 'Low',
        };
    }

    /**
     * @return array{score: int, grade: ?string, breakdown: array<string, int>, summary: string, priority: ?string}
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'breakdown' => $this->breakdown,
            'summary' => $this->summary,
            'priority' => $this->priority(),
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
