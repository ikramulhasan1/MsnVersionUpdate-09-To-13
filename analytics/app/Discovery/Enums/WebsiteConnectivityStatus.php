<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * A discovered site's live reachability/connectivity outcome — the
 * "Website Status" checkbox filter in the Advanced Filters panel
 * (resources/views/discovery/partials/search-panel.blade.php). Not to
 * be confused with WebsiteStatus (this same namespace), which tracks a
 * discovered_websites row's own enrichment *lifecycle*
 * (discovered/enriched/stale/archived) — this enum instead describes
 * what happened the last time this module tried to actually reach the
 * site, the same kind of outcome App\Audit\Fetching\WebsiteFetcherService
 * already distinguishes for a single audited page (a successful fetch
 * vs. a timeout, DNS failure, etc.), generalized here into a small
 * fixed vocabulary a checkbox filter can present.
 *
 * No column on discovered_websites stores this yet — Phase A1's
 * migration didn't include one, and (matching WebsiteStatus's own
 * precedent) this enum is defined ahead of that column landing in a
 * later phase's migration/enrichment logic. Phase C1 only needs it to
 * drive the filter checkboxes; it isn't cast on
 * App\Models\DiscoveredWebsite yet.
 */
enum WebsiteConnectivityStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case TIMEOUT = 'timeout';
    case REDIRECT = 'redirect';
    case SSL_ERROR = 'ssl_error';
    case DNS_ERROR = 'dns_error';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
            self::TIMEOUT => 'Timeout',
            self::REDIRECT => 'Redirect',
            self::SSL_ERROR => 'SSL Error',
            self::DNS_ERROR => 'DNS Error',
        };
    }
}