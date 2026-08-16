<?php

declare(strict_types=1);

namespace App\Discovery\Scoring\DTO;

/**
 * The Website Discovery module's counterpart to
 * App\Audit\Lead\DTO\ProspectQualificationResult — same shape
 * (score/grade/breakdown/summary, JsonSerializable), computed purely
 * from OpportunityScorer's own already-documented formula. See that
 * class's own docblock for exactly how each $breakdown point is
 * derived.
 *
 * Unlike ProspectQualificationResult, this is never wrapped in a
 * nullable outer type: OpportunityScorer::score() always has a
 * DiscoveredWebsite to work from, and every individual bucket already
 * degrades to 0 (not a fabricated guess) when its own underlying data
 * is missing — there's no single "nothing to work with at all" case
 * the way ProspectQualificationScorer has for a missing
 * BusinessOpportunityResult, so grade here is a plain string, not
 * ?string.
 */
final readonly class OpportunityScoreResult implements \JsonSerializable
{
    /**
     * @param  int  $score  0-100, higher means more service-sales opportunity
     *                      (more real gaps to sell against), not a health/quality score —
     *                      see OpportunityScorer.
     * @param  string  $grade  letter grade (A-F) derived from $score.
     * @param  array<string, int>  $breakdown  points contributed by each input,
     *                                         keyed 'seo', 'performance', 'mobile', 'accessibility',
     *                                         'technology_age' — always present and summing to $score, so the
     *                                         score is auditable rather than a black box.
     * @param  string  $summary  human-readable overview of the score.
     */
    public function __construct(
        public int $score,
        public string $grade,
        public array $breakdown,
        public string $summary,
    ) {}

    /**
     * @return array{score: int, grade: string, breakdown: array<string, int>, summary: string}
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'breakdown' => $this->breakdown,
            'summary' => $this->summary,
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
