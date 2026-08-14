<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * A discovered site's own lifecycle within this module — distinct from
 * App\Audit\Enums\AuditStatus, which tracks a single audit *run's*
 * pipeline progress (queued → fetching → ... → completed/failed). This
 * enum instead tracks how current/complete a discovered_websites row's
 * own data is over time.
 *
 * No `status` column exists on discovered_websites yet — Phase A1's
 * migration (create_discovered_websites_table) didn't include one, and
 * this enum is deliberately defined ahead of that column landing in a
 * later phase's migration, the same phased/iterative approach the rest
 * of this module is being built with. Not yet cast on
 * App\Models\DiscoveredWebsite for that reason.
 */
enum WebsiteStatus: string
{
    /**
     * A site has been found (has at minimum a domain/url), but no
     * enrichment (technology detection, scoring, contact info, ...)
     * has run against it yet.
     */
    case DISCOVERED = 'discovered';

    /**
     * Enrichment has run and populated at least some of the
     * technographic/scoring/contact fields.
     */
    case ENRICHED = 'enriched';

    /**
     * Was previously enriched, but its data is old enough that it
     * should be re-enriched before being relied on (e.g. for outreach
     * or lead scoring) — the discovery-module equivalent of a cache
     * entry past its TTL, not an error state.
     */
    case STALE = 'stale';

    /**
     * Explicitly excluded from active search results (e.g. no longer a
     * real business, a duplicate of another row, or manually dismissed)
     * without being deleted outright.
     */
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DISCOVERED => 'Discovered',
            self::ENRICHED => 'Enriched',
            self::STALE => 'Stale',
            self::ARCHIVED => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DISCOVERED => 'bg-secondary',
            self::ENRICHED => 'bg-success',
            self::STALE => 'bg-warning',
            self::ARCHIVED => 'bg-secondary',
        };
    }
}