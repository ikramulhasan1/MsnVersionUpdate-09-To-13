<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Summary" worksheet: one metric/label paired
 * with its human-readable value. A flat label/value shape rather than
 * one column per metric, since the Summary sheet mixes scalars
 * (scores, counts) with a free-text narrative and recommendation — a
 * two-column report reads cleanly for all of them at once.
 */
final readonly class SummaryRow
{
    public function __construct(
        public string $label,
        public string $value,
    ) {
    }
}
