<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Analysis" worksheet: one individual check/metric
 * for one category. Deliberately excludes the `recommendation` /
 * `suggestions` fields carried by the underlying analyzer DTOs — those
 * are out of scope for this export pass.
 */
final readonly class AnalysisRow
{
    public function __construct(
        public string $category,
        public string $check,
        public ?string $value,
        public string $status,
    ) {
    }
}
