<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * A single row of the "Technology Stack" worksheet: one detected
 * technology, its category, and (where known) version and detection
 * confidence. Built only from entries already marked detected in
 * TechnologyResult::$technologyStack — undetected technologies are not
 * rows here.
 */
final readonly class TechnologyRow
{
    public function __construct(
        public string $technology,
        public string $category,
        public ?string $version,
        public ?int $confidenceScore,
    ) {
    }
}
