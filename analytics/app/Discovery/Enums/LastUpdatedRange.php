<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Advanced Filters panel's "Last Updated" recency filter — a
 * bucketed single-select (how recently a discovered site's content
 * last changed) rather than a dual-range date picker: this app has no
 * date-range-picker component or JS date library, and a small fixed
 * set of "within the last ..." buckets is both simpler to build with
 * plain HTML and closer to how a lead-gen search actually gets used
 * ("show me stale sites", not "sites updated between March 3rd and
 * March 19th").
 *
 * Maps to discovered_websites.last_updated_at once a future phase
 * applies this filter to a real query — not read by anything yet
 * beyond the filter UI, matching every other Advanced Filters group's
 * "UI first, query logic later" status in this module so far.
 */
enum LastUpdatedRange: string
{
    case WITHIN_WEEK = 'within_week';
    case WITHIN_MONTH = 'within_month';
    case WITHIN_3_MONTHS = 'within_3_months';
    case WITHIN_6_MONTHS = 'within_6_months';
    case WITHIN_YEAR = 'within_year';
    case OVER_YEAR = 'over_year';

    public function label(): string
    {
        return match ($this) {
            self::WITHIN_WEEK => 'Within the last week',
            self::WITHIN_MONTH => 'Within the last month',
            self::WITHIN_3_MONTHS => 'Within the last 3 months',
            self::WITHIN_6_MONTHS => 'Within the last 6 months',
            self::WITHIN_YEAR => 'Within the last year',
            self::OVER_YEAR => 'Over a year ago',
        };
    }
}