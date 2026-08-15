<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Technology filter section's Server checkbox group
 * (resources/views/discovery/partials/search-panel.blade.php).
 *
 * Deliberately NOT drawn from App\Audit\Technology\TechnologyDetector —
 * unlike CMS/Framework/E-commerce Platform/CDN (see
 * App\Discovery\Taxonomy\TechnologyFilterOptions, which genuinely does
 * reuse TechnologyDetector's own vocabulary for those four), that class
 * has no enumerated server-software list to reuse:
 * TechnologyDetector::serverInfo() only ever surfaces the raw, free-
 * text Server response header (e.g. "nginx/1.24.0", "cloudflare"),
 * never a fixed set of named products. Rather than fabricate a false
 * "reused from the audit engine" list, this is a small, separately
 * curated set of the most common server software instead.
 *
 * No column on discovered_websites is cast to this yet — that column
 * (see database/migrations/2026_08_14_000000_create_discovered_websites_table.php)
 * stays free-text, matching how TechnologyDetector itself treats the
 * Server header (a raw string, not a fixed vocabulary) — this enum
 * only drives the filter checkboxes' fixed set of common choices.
 */
enum ServerSoftware: string
{
    case APACHE = 'apache';
    case NGINX = 'nginx';
    case LITESPEED = 'litespeed';
    case IIS = 'iis';
    case NODE = 'node';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::APACHE => 'Apache',
            self::NGINX => 'Nginx',
            self::LITESPEED => 'LiteSpeed',
            self::IIS => 'Microsoft IIS',
            self::NODE => 'Node.js',
            self::OTHER => 'Other',
        };
    }
}