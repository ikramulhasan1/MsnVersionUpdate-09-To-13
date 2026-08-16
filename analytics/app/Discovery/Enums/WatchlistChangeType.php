<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * What kind of change App\Discovery\Jobs\MonitorWatchlistChangesJob
 * detected between one check and the next — the
 * discovery_watchlist_changes.change_type column's own vocabulary
 * (Phase G2).
 *
 * TECHNOLOGY covers cms/framework/ecommerce_platform/server/cdn
 * together as one change (a single before/after JSON snapshot of all
 * five, in old_value/new_value — see that job's own
 * technologySnapshot() docblock), not five separate change types: the
 * prompt asked for "Technology" changes as one category, and a site
 * swapping CMS often changes several of those columns at once anyway,
 * so five near-simultaneous log rows would mostly just be noise.
 */
enum WatchlistChangeType: string
{
    case SEO_SCORE = 'seo_score';
    case PERFORMANCE_SCORE = 'performance_score';
    case TECHNOLOGY = 'technology';
    case SSL_STATUS = 'ssl_status';
    case CONNECTIVITY = 'connectivity';

    public function label(): string
    {
        return match ($this) {
            self::SEO_SCORE => 'SEO Score Changed',
            self::PERFORMANCE_SCORE => 'Performance Score Changed',
            self::TECHNOLOGY => 'Technology Changed',
            self::SSL_STATUS => 'SSL Status Changed',
            self::CONNECTIVITY => 'Online/Offline Status Changed',
        };
    }
}
