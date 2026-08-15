<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Advanced Filters panel's "Est. Traffic" checkbox group — bucketed
 * monthly-visit ranges, matching the shape of
 * discovered_websites.estimated_traffic_range (see database/migrations/
 * 2026_08_14_000000_create_discovered_websites_table.php: that column
 * is a free-text string precisely because no traffic-estimation data
 * source is wired up yet, so a fixed set of "typical" buckets is used
 * for the filter rather than a numeric range this module can't
 * currently produce or validate a real value against).
 *
 * self::label() always includes the "Est." prefix — never presents a
 * bucket as an exact number — since a discovered site's traffic figure
 * is, and will remain even once a real estimation source is wired up,
 * an estimate rather than a measured value.
 */
enum TrafficRange: string
{
    case UNDER_1K = 'under_1k';
    case ONE_K_TO_10K = '1k_10k';
    case TEN_K_TO_50K = '10k_50k';
    case FIFTY_K_TO_100K = '50k_100k';
    case ONE_HUNDRED_K_TO_500K = '100k_500k';
    case OVER_500K = 'over_500k';

    public function label(): string
    {
        return 'Est. Traffic: '.match ($this) {
            self::UNDER_1K => 'Under 1K/mo',
            self::ONE_K_TO_10K => '1K–10K/mo',
            self::TEN_K_TO_50K => '10K–50K/mo',
            self::FIFTY_K_TO_100K => '50K–100K/mo',
            self::ONE_HUNDRED_K_TO_500K => '100K–500K/mo',
            self::OVER_500K => 'Over 500K/mo',
        };
    }
}