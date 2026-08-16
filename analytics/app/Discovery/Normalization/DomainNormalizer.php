<?php

declare(strict_types=1);

namespace App\Discovery\Normalization;

/**
 * Turns a URL into a canonical "same site" key — the Website Discovery
 * module's Phase I2 answer to a real gap Audit's own url_hash never
 * had to solve: Audit's url_hash (see database/migrations/*_add_url_hash_to_audits_table.php's
 * own docblock) is just md5(raw url), indexed but NOT unique, because
 * the same URL genuinely CAN be audited more than once on purpose (a
 * re-audit). Website Discovery's own url_hash IS unique (see
 * database/migrations/2026_08_14_000000_create_discovered_websites_table.php,
 * ->unique() was already there from Phase A1) because the opposite is
 * true here: the same SITE should only ever be discovered once, no
 * matter how many superficially different URL strings point at it.
 * DiscoveredWebsite::booted() previously hashed the raw url exactly
 * like Audit does — meaning "http://example.com",
 * "https://example.com", "https://www.example.com", and
 * "https://example.com/" all hashed DIFFERENTLY and could each be
 * saved as their own "different" site, silently defeating that
 * already-unique constraint. This class is the one step further than
 * Audit's own pattern that Website Discovery specifically needs.
 *
 * Normalizes exactly three things, deliberately no more:
 *   - Scheme (http:// vs https://) is dropped entirely — a site is the
 *     same site whether or not it happens to redirect to HTTPS.
 *   - A leading "www." on the host is stripped, and the host is
 *     lowercased (hostnames are case-insensitive; paths are not, so
 *     only the host is lowercased, never the path).
 *   - A trailing slash on the path is stripped, so
 *     "example.com/about" and "example.com/about/" normalize
 *     identically.
 * Query strings and fragments are left untouched on purpose — the
 * prompt this class was built for named exactly these three
 * normalizations, and a query string can legitimately identify a
 * different resource, not just a different way of writing the same
 * one.
 */
final class DomainNormalizer
{
    public function normalize(string $url): string
    {
        $trimmed = trim($url);

        if ($trimmed === '') {
            return '';
        }

        // Not every caller is guaranteed to pass a URL with a scheme
        // (DiscoveredWebsite::$url normally always has one, but this
        // class has no way to enforce that) — parse_url() misreads a
        // scheme-less "example.com/path" as an all-path, no-host URL,
        // so a scheme is added ONLY for parsing purposes here and never
        // appears in the normalized result regardless.
        $parseable = preg_match('#^https?://#i', $trimmed) === 1 ? $trimmed : 'https://'.$trimmed;
        $parts = parse_url($parseable);

        $host = strtolower($parts['host'] ?? '');
        $host = preg_replace('/^www\./', '', $host) ?? $host;

        $path = rtrim($parts['path'] ?? '', '/');

        return $host.$path;
    }

    public function hash(string $url): string
    {
        return md5($this->normalize($url));
    }
}
