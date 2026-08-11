<?php

declare(strict_types=1);

namespace App\Audit\Fetching;

final class UrlResolver
{
    /**
     * Resolve a possibly-relative URL found in a page's HTML against that
     * page's URL. Returns null for URLs that aren't fetchable web resources
     * (anchors, mailto:, javascript:, data: URIs, empty strings).
     */
    public static function resolve(string $baseUrl, string $relative): ?string
    {
        $relative = trim($relative);

        if ($relative === '' || str_starts_with($relative, '#')) {
            return null;
        }

        foreach (['data:', 'javascript:', 'mailto:', 'tel:'] as $ignoredScheme) {
            if (stripos($relative, $ignoredScheme) === 0) {
                return null;
            }
        }

        // Already absolute (has a scheme).
        if (preg_match('#^[a-z][a-z0-9+.\-]*://#i', $relative) === 1) {
            return $relative;
        }

        $base = parse_url($baseUrl);

        if ($base === false || ! isset($base['scheme'], $base['host'])) {
            return null;
        }

        $scheme = $base['scheme'];
        $host = $base['host'];
        $port = isset($base['port']) ? ':' . $base['port'] : '';
        $authority = "{$scheme}://{$host}{$port}";

        // Protocol-relative, e.g. //cdn.example.com/lib.css
        if (str_starts_with($relative, '//')) {
            return $scheme . ':' . $relative;
        }

        // Root-relative, e.g. /css/app.css
        if (str_starts_with($relative, '/')) {
            return $authority . self::normalizePath($relative);
        }

        // Document-relative, e.g. images/logo.png or ../fonts/a.woff2
        $basePath = $base['path'] ?? '/';
        $baseDir = str_ends_with($basePath, '/') ? $basePath : (rtrim(dirname($basePath), '/') . '/');

        return $authority . self::normalizePath($baseDir . $relative);
    }

    /**
     * The scheme + host (+ port) portion of a URL, e.g. "https://example.com".
     */
    public static function originOf(string $url): ?string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return "{$parts['scheme']}://{$parts['host']}{$port}";
    }

    /**
     * Collapse "." and ".." segments out of a URL path.
     */
    private static function normalizePath(string $path): string
    {
        $segments = explode('/', $path);
        $resolved = [];

        foreach ($segments as $segment) {
            if ($segment === '.' || $segment === '') {
                continue;
            }

            if ($segment === '..') {
                array_pop($resolved);

                continue;
            }

            $resolved[] = $segment;
        }

        return '/' . implode('/', $resolved);
    }
}
