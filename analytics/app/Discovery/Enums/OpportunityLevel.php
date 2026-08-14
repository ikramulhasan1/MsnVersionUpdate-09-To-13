<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * A three-tier classification of a discovered site's own numeric
 * opportunity_score (0-100) — the discovery-module counterpart to how
 * App\Audit\BusinessOpportunity\BusinessOpportunityAnalyzer already
 * turns its own opportunity score into a High/Medium/Low priority (see
 * that class's businessOpportunityScore()). self::fromScore() reuses
 * the exact same 60/30 thresholds that method already established,
 * rather than inventing new ones, so "High opportunity" means the same
 * thing whether it's read off a discovered site here or an audited
 * site there.
 *
 * Deliberately NOT cast directly onto
 * App\Models\DiscoveredWebsite::$opportunity_score — that column stays
 * a plain 0-100 integer (the real, storable, sortable/filterable
 * value); this enum is a derived display/filter-bucket classification
 * computed from it via fromScore(), not a stored value of its own.
 */
enum OpportunityLevel: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    /**
     * Mirrors BusinessOpportunityAnalyzer::businessOpportunityScore()'s
     * own priority thresholds exactly (>=60 High, >=30 Medium, else
     * Low). Null (no opportunity_score computed yet for this site)
     * maps to Low rather than throwing, matching this module's own
     * "nullable until enriched" convention for every scoring column.
     */
    public static function fromScore(?int $score): self
    {
        return match (true) {
            $score === null => self::LOW,
            $score >= 60 => self::HIGH,
            $score >= 30 => self::MEDIUM,
            default => self::LOW,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::HIGH => 'High',
            self::MEDIUM => 'Medium',
            self::LOW => 'Low',
        };
    }

    /**
     * Matches the same High→red/Medium→amber/Low→green convention
     * already established for Priority pills elsewhere in this
     * codebase (see resources/views/audit/pdf/partials/summary.blade.php's
     * Priority pill coloring) — Low is green/success here, not a
     * neutral gray, since a Low-opportunity site is a genuinely good
     * outcome (a healthy site with little to sell against), the same
     * reasoning that pill coloring documents.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::HIGH => 'bg-danger',
            self::MEDIUM => 'bg-warning',
            self::LOW => 'bg-success',
        };
    }
}