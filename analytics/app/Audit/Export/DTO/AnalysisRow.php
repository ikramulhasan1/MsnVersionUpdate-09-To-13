<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Analysis" worksheet: one individual check/metric
 * for one category. Deliberately excludes the `recommendation` /
 * `suggestions` fields carried by the underlying analyzer DTOs — those
 * are out of scope for this export pass.
 *
 * $pageUrl and $elementLocation surface the same per-check location
 * data AnalysisResultsToDashboardCategories exposes to the dashboard
 * (see that class's 'location' shape) — $elementLocation is a single
 * flattened, human-readable string (DOM path / resource URL / detail,
 * semicolon-separated across multiple affected elements when there is
 * more than one) rather than a nested structure, since a worksheet cell
 * needs plain text, not an array.
 */
final readonly class AnalysisRow
{
    public function __construct(
        public string $category,
        public string $check,
        public ?string $value,
        public string $status,
        public ?string $pageUrl = null,
        public ?string $elementLocation = null,
    ) {
    }
}