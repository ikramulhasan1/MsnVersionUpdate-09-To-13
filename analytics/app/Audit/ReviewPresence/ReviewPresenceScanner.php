<?php

declare(strict_types=1);

namespace App\Audit\ReviewPresence;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\ReviewPresence\DTO\ReviewPresenceResult;

/**
 * Detects whether a site links out to a review-platform profile —
 * Clutch, G2, GoodFirms, or a Google Maps/Business Profile listing.
 *
 * Deliberately does NOT call any of these platforms' APIs to fetch
 * review counts or ratings — that would need a developer agreement/API
 * key with each platform individually, which this class does not have
 * and which is out of scope here. It only reports whether the site
 * itself links to a profile on that platform, honestly labeled as
 * presence detection, never as review data.
 */
final class ReviewPresenceScanner
{
    /**
     * @var array<string, string> platform => URL-substring pattern
     */
    private const array PLATFORM_PATTERNS = [
        'clutch' => '#clutch\.co/profile/#i',
        'g2' => '#g2\.com/products/#i',
        'goodfirms' => '#goodfirms\.co/#i',
        // Google Business Profile / Maps listing links appear in a few
        // different forms depending on how a site links out; g.page is
        // Google's own short-link domain specifically for this.
        'google' => '#(g\.page/|maps\.google\.com|google\.com/maps|business\.google\.com)#i',
    ];

    /**
     * @param  array<int, CrawledPage>  $crawledPages
     */
    public function scan(array $crawledPages): ReviewPresenceResult
    {
        $url = $crawledPages[0]->url ?? '';
        $platforms = [
            'clutch' => null,
            'g2' => null,
            'goodfirms' => null,
            'google' => null,
        ];
        $platformSourcePages = [
            'clutch' => null,
            'g2' => null,
            'goodfirms' => null,
            'google' => null,
        ];

        foreach ($crawledPages as $page) {
            foreach ($page->anchors as $anchor) {
                foreach (self::PLATFORM_PATTERNS as $platform => $pattern) {
                    if ($platforms[$platform] !== null) {
                        continue;
                    }

                    if (preg_match($pattern, $anchor->url) === 1) {
                        $platforms[$platform] = $anchor->url;
                        $platformSourcePages[$platform] = $page->url;
                    }
                }
            }
        }

        return new ReviewPresenceResult(
            url: $url,
            platforms: $platforms,
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
            platformSourcePages: $platformSourcePages,
        );
    }
}
