<?php

declare(strict_types=1);

namespace App\Audit\Technology;

use App\Audit\Technology\DTO\TechnologyResult;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;

/**
 * Turns an already-computed TechnologyResult into a list of "this is
 * old enough to be worth upgrading" opportunities, for the Lead
 * Intelligence features (Prospect Qualification Score, Outreach
 * Draft, dashboard/export cards).
 *
 * Runs strictly after TechnologyDetector — it never fetches or parses
 * anything itself, only reads TechnologyResult::$detections — which is
 * why it's wired into AssembleAnalysisResultsJob (assembly time, once
 * $results->technology exists) rather than AnalyzeChunkJob.
 *
 * A technology is only flagged when self::THRESHOLDS names it AND
 * TechnologyDetector reported a real, non-null version for it. If the
 * version is null (Laravel, Shopify, and every analytics/pixel/CDN
 * detection never expose one — see TechnologyDetector's per-method
 * docblocks — or a versioned technology simply wasn't fingerprinted
 * this time), no opportunity is reported for it: silence is correct
 * here, never a guessed "probably outdated".
 */
final class TechnologyUpgradeAnalyzer
{
    /**
     * Known-outdated version thresholds, keyed by the same technology
     * slug TechnologyResult::$detections uses. A technology is flagged
     * once its detected version is strictly below 'below_version'.
     *
     * Deliberately excludes every technology TechnologyDetector never
     * assigns a public version to (laravel, shopify, google_analytics,
     * google_tag_manager, facebook_pixel, microsoft_clarity, google_ads,
     * cloudflare) — a threshold for a slug that can never carry a
     * version would never fire and would only make this table look
     * more complete than it is.
     *
     * 'below_version' reflects each vendor's currently supported major
     * release line as of this table's last review (August 2026, per
     * each vendor's own release/support-policy pages). Vendors ship new
     * majors and retire old ones on their own schedules, so this table
     * is a starting default to be reviewed periodically against current
     * vendor support pages — not a permanently-accurate fact.
     *
     * @var array<string, array{below_version: string, suggested_service: string}>
     */
    private const array THRESHOLDS = [
        'wordpress' => [
            'below_version' => '6.4',
            'suggested_service' => 'WordPress core upgrade to the latest 6.x release',
        ],
        'woocommerce' => [
            'below_version' => '8.0',
            'suggested_service' => 'WooCommerce upgrade to the latest supported release',
        ],
        'bootstrap' => [
            'below_version' => '5.0',
            'suggested_service' => 'Front-end upgrade from Bootstrap 4 to Bootstrap 5',
        ],
        'jquery' => [
            'below_version' => '3.0',
            'suggested_service' => 'jQuery upgrade to the 3.x line, or removal where the rest of the stack no longer needs it',
        ],
        'vue' => [
            'below_version' => '3.0',
            'suggested_service' => 'Migration from Vue 2 to Vue 3',
        ],
        'angular' => [
            'below_version' => '17.0',
            'suggested_service' => 'Angular upgrade to a currently-supported major version',
        ],
        'nextjs' => [
            'below_version' => '13.0',
            'suggested_service' => 'Next.js upgrade to the App Router-based 13.x+ line',
        ],
        'nuxt' => [
            'below_version' => '3.0',
            'suggested_service' => 'Migration from Nuxt 2 to Nuxt 3',
        ],
        'tailwind' => [
            'below_version' => '3.0',
            'suggested_service' => 'Tailwind CSS upgrade to a current major version',
        ],
    ];

    /**
     * @return array<int, TechnologyUpgradeOpportunity>
     */
    public function analyze(TechnologyResult $technology): array
    {
        $opportunities = [];

        foreach (self::THRESHOLDS as $slug => $rule) {
            $detection = $technology->detections[$slug] ?? null;

            if ($detection === null || $detection->version === null) {
                continue;
            }

            if (version_compare($detection->version, $rule['below_version'], '>=')) {
                continue;
            }

            $opportunities[] = new TechnologyUpgradeOpportunity(
                slug: $slug,
                technology: $detection->technology,
                detectedVersion: $detection->version,
                reason: sprintf(
                    '%s %s is below %s, the release line this table currently treats as up to date (last reviewed August 2026); '
                        .'releases older than that typically fall outside the vendor\'s active security-support window.',
                    $detection->technology,
                    $detection->version,
                    $rule['below_version'],
                ),
                suggestedService: $rule['suggested_service'],
            );
        }

        return $opportunities;
    }
}
