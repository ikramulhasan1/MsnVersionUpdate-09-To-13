<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Scores" worksheet: one analyzed category and its
 * overall result. Deliberately excludes recommendations and any other
 * detail — see AnalysisRow for individual check results.
 */
final readonly class ScoreRow
{
    public function __construct(
        public string $category,
        public ?int $score,
        public ?string $grade,
        public string $analyzedAt,
    ) {
    }
}
