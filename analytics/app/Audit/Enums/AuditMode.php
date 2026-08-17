<?php

declare(strict_types=1);

namespace App\Audit\Enums;

/**
 * Phase K1 (Quick Scan Mode) — how thorough one audit run should be,
 * chosen once at submission time (App\Audit\DTO\CreateAuditData) and
 * carried on the Audit row itself (App\Models\Audit::$mode) so every
 * later stage of the pipeline (App\Audit\Jobs\FetchAndCrawlJob,
 * App\Audit\Jobs\AnalyzeChunkJob) can read the SAME decision back
 * rather than needing it passed down through every job's own
 * constructor a second time.
 *
 * FULL is the existing, unchanged behavior this whole app was built
 * around — every page config('audit.crawler.max_pages') allows,
 * every analyzer, real PageSpeed Insights data when configured. QUICK
 * trades real depth for real speed on two specific, deliberately
 * chosen levers (see FetchAndCrawlJob's and AnalyzeChunkJob's own
 * docblocks for exactly where each is applied):
 *   - Crawl is capped to the entry page only
 *     (config('audit.quick_scan.max_pages'), default 1) — no
 *     additional pages are fetched or analyzed at all.
 *   - PageSpeed Insights is skipped even if configured/enabled
 *     globally — Performance analysis falls back to the same
 *     heuristic (non-PSI) checks it already reports 'unknown'
 *     LCP/CLS/FID under when no PSI client is configured at all (see
 *     PerformanceAnalyzer's own docblock) — a real, if less certain,
 *     source of speed: PSI is typically the single slowest step in the
 *     whole pipeline (a real Lighthouse run against the target URL on
 *     Google's own infrastructure), often taking longer than every
 *     other analyzer combined.
 *
 * Every OTHER analyzer (Security, SEO, Accessibility, Content, UI/UX,
 * Technology, Business Opportunity/Signals, Contact Info, Review
 * Presence) still runs in QUICK mode — it is a smaller, faster audit,
 * not a fake one; whatever it reports is real data about the entry
 * page, just not the site-wide, PSI-backed picture FULL mode builds.
 */
enum AuditMode: string
{
    case FULL = 'full';
    case QUICK = 'quick';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Full Audit',
            self::QUICK => 'Quick Scan',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FULL => 'Crawls multiple pages and includes real PageSpeed Insights data. Takes longer, most complete.',
            self::QUICK => 'Homepage only, no PageSpeed Insights call. Much faster, less depth.',
        };
    }
}
