<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * A rough employee-count/scale band for the business behind a
 * discovered site — one of the module's advanced filters. Deliberately
 * a small, fixed set of bands (not a raw employee-count number): the
 * discovery data source(s) this module will eventually integrate with
 * generally only expose a size *range*, not an exact headcount, and a
 * bounded enum is what the search UI's filter (a set of checkboxes/a
 * select, not a numeric range input) needs anyway. Cast on
 * App\Models\DiscoveredWebsite::$business_size.
 */
enum BusinessSize: string
{
    case SOLO = 'solo';
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';
    case ENTERPRISE = 'enterprise';

    public function label(): string
    {
        return match ($this) {
            self::SOLO => 'Solo (1 employee)',
            self::SMALL => 'Small (2-10 employees)',
            self::MEDIUM => 'Medium (11-50 employees)',
            self::LARGE => 'Large (51-200 employees)',
            self::ENTERPRISE => 'Enterprise (200+ employees)',
        };
    }
}