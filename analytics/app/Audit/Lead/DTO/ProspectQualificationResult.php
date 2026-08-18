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
     * @param  int  $score  raw points from whatever buckets were actually
     *         computable (see $isPartial below) — higher means a stronger
     *         sales lead (more real issues/signals/upgrade opportunities
     *         to sell against), not a quality/health score — see
     *         ProspectQualificationScorer.
     * @param  ?string  $grade  letter grade (A-F) derived from $score —
     *         ALWAYS null when $isPartial is true (Phase M6): a letter
     *         grade implies "graded against the full 100-point rubric",
     *         which would be a fabricated claim of completeness for a
     *         result some of whose inputs were genuinely unavailable.
     * @param  array<string, int>  $breakdown  points contributed by each input,
     *         keyed 'website_issues', 'business_signals',
     *         'technology_upgrade_opportunities' — always present and summing
     *         to $score, so the score is auditable rather than a black box.
     * @param  string  $summary  human-readable overview of the qualification result.
     * @param  int  $maxPossibleScore  (Phase M6) the sum of ONLY the
     *         buckets that actually had real data to score from — 100
     *         when every input was available (the pre-Phase-M6 case, and
     *         still by far the common one), less than 100 when e.g.
     *         BusinessOpportunityResult was null (see
     *         ProspectQualificationScorer's own docblock for exactly why
     *         that happens and how this value is computed). $score /
     *         $maxPossibleScore is this result's own honest completion
     *         ratio — a caller that wants "how much of the full lead
     *         picture is this score actually based on" reads this, not
     *         $score alone.
     * @param  bool  $isPartial  true when $maxPossibleScore < 100 — i.e.
     *         at least one of the three scoring inputs
     *         (BusinessOpportunityResult, a real BusinessSignalsResult,
     *         technology upgrade opportunities) was genuinely
     *         unavailable for this audit, not merely "found nothing".
     * @param  array<string, bool>  $availableBuckets  which of
     *         'website_issues' / 'business_signals' /
     *         'technology_upgrade_opportunities' actually had real data
     *         to score from — a caller rendering $breakdown (e.g.
     *         audit/partials/full-report.blade.php's own Prospect
     *         Qualification card) uses this to show "N/A — no data" for
     *         a bucket that's false here, rather than a misleading
     *         "0/60 pts" that would look identical to "measured and
     *         found zero problems".
     */
    public function __construct(
        public int $score,
        public ?string $grade,
        public array $breakdown,
        public string $summary,
        public int $maxPossibleScore = 100,
        public bool $isPartial = false,
        public array $availableBuckets = [
            'website_issues' => true,
            'business_signals' => true,
            'technology_upgrade_opportunities' => true,
        ],
    ) {
    }

    /**
     * Maps $score to a coarse sales-priority bucket: 'High' (score >=
     * self::HIGH_PRIORITY_THRESHOLD), 'Medium' (score >=
     * self::MEDIUM_PRIORITY_THRESHOLD), otherwise 'Low'.
     *
     * Phase M6 — compares a NORMALIZED score ($score projected onto a
     * full 0-100 scale via $maxPossibleScore) against these thresholds,
     * not raw $score directly: the thresholds themselves were tuned
     * assuming a /100 score, so comparing a partial result's raw,
     * necessarily-smaller $score against them directly would silently
     * bias every partial result toward 'Low' regardless of how
     * genuinely strong a lead the AVAILABLE data suggests. A partial
     * 18/40 (45%) is treated the same as a complete 45/100 here — both
     * read as 'Medium' — which is the fair comparison; only $grade
     * itself (see that property's own docblock) stays withheld for a
     * partial result, since a priority bucket is a coarser, lower-
     * stakes claim than a specific letter grade.
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
        $normalizedScore = $this->maxPossibleScore > 0
            ? ($this->score / $this->maxPossibleScore) * 100
            : 0.0;

        return match (true) {
            $normalizedScore >= self::HIGH_PRIORITY_THRESHOLD => 'High',
            $normalizedScore >= self::MEDIUM_PRIORITY_THRESHOLD => 'Medium',
            default => 'Low',
        };
    }

    /**
     * @return array{score: int, grade: ?string, breakdown: array<string, int>, summary: string, priority: ?string, max_possible_score: int, is_partial: bool, available_buckets: array<string, bool>}
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'breakdown' => $this->breakdown,
            'summary' => $this->summary,
            'priority' => $this->priority(),
            'max_possible_score' => $this->maxPossibleScore,
            'is_partial' => $this->isPartial,
            'available_buckets' => $this->availableBuckets,
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

    /**
     * PRODUCTION INCIDENT — read before removing these two methods:
     * App\Audit\Cache\AuditCacheService stores a whole AnalysisResults
     * object graph (this class included, nested under
     * AnalysisResults::$prospectQualification) via PHP's native
     * serialize()/unserialize() in the `cache` database table. Phase M6
     * added three new typed, readonly properties to this class
     * ($maxPossibleScore, $isPartial, $availableBuckets) — real cache
     * ROWS WRITTEN BEFORE that change simply have no value for any of
     * the three in their own serialized blob. PHP's default
     * unserialize() behavior for a readonly class does NOT run the
     * constructor and does NOT apply the constructor's own default
     * values for a property missing from the serialized data — it
     * leaves that typed property genuinely, permanently uninitialized,
     * so the very first read of it anywhere (e.g.
     * audit/partials/full-report.blade.php's own
     * $prospectQualification->maxPossibleScore) throws "must not be
     * accessed before initialization" — a real production error hit on
     * this exact class the first time a pre-Phase-M6 cached audit was
     * viewed after deploying Phase M6.
     *
     * __serialize()/__unserialize() are PHP's own supported mechanism
     * for exactly this situation: __unserialize() runs as real code
     * (unlike the default unserialize path) and can supply a sensible
     * default for a key that's missing from OLDER serialized data.
     * Defaults chosen here match this class's own pre-Phase-M6
     * behavior — a full, non-partial 100-point result — since every
     * cache row old enough to be missing these keys was necessarily
     * written before partial results existed at all.
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'breakdown' => $this->breakdown,
            'summary' => $this->summary,
            'maxPossibleScore' => $this->maxPossibleScore,
            'isPartial' => $this->isPartial,
            'availableBuckets' => $this->availableBuckets,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->score = $data['score'];
        $this->grade = $data['grade'];
        $this->breakdown = $data['breakdown'];
        $this->summary = $data['summary'];
        $this->maxPossibleScore = $data['maxPossibleScore'] ?? 100;
        $this->isPartial = $data['isPartial'] ?? false;
        $this->availableBuckets = $data['availableBuckets'] ?? [
            'website_issues' => true,
            'business_signals' => true,
            'technology_upgrade_opportunities' => true,
        ];
    }
}