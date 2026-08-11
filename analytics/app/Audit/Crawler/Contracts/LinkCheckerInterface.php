<?php

declare(strict_types=1);

namespace App\Audit\Crawler\Contracts;

use App\Audit\Crawler\DTO\LinkCheckResult;

interface LinkCheckerInterface
{
    /**
     * Lightweight reachability check for a URL the crawler found but isn't
     * going to fully fetch (external links, and internal links beyond the
     * depth/page budget). Must never throw — a network failure just means
     * the link couldn't be verified, which the caller records as broken.
     */
    public function check(string $url): LinkCheckResult;

    /**
     * Concurrent version of check() for several URLs at once. A crawl can
     * easily surface dozens of external links to verify; checking them
     * one at a time was one of the largest contributors to total audit
     * time. Never throws — same guarantee as check().
     *
     * @param array<int, string> $urls
     * @return array<string, LinkCheckResult> keyed by the original URL
     */
    public function checkMany(array $urls): array;
}
