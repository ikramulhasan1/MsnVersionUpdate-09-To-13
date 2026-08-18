<?php

declare(strict_types=1);

namespace App\Audit\BusinessSignals;

use App\Audit\BusinessSignals\DTO\BusinessSignalsResult;
use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\SchemaBlock;

/**
 * Detects a fixed set of business signals from already-crawled pages:
 * careers, hiring, blog_update, funding, new_product.
 *
 * IMPORTANT SCOPE NOTE: CrawledPage carries only structured extracted
 * data (headings, anchors, meta, schema.org JSON-LD) — never raw HTML
 * or body prose, by design (the crawler discards raw markup once a
 * page is parsed, to stay memory-reasonable across large multi-page
 * crawls). So unlike a plain-text keyword scan, every signal here is
 * detected from structured, attributable sources only:
 *   - careers: a crawled page whose URL, title, or heading text
 *     matches career/job/hiring vocabulary, OR a page carrying a
 *     schema.org JobPosting block.
 *   - hiring: a stronger claim than "a careers page exists" — only
 *     true when a JobPosting schema block was actually found (a
 *     careers *page* existing doesn't by itself mean active listings;
 *     the schema block is the honest signal that a specific role is
 *     posted).
 *   - blog_update: a page under /blog/ or /news/ whose schema.org
 *     Article/BlogPosting block has a datePublished or dateModified
 *     within the last 90 days. A blog section existing with no
 *     parseable recent date does NOT set this true — see
 *     self::BLOG_FRESHNESS_WINDOW_DAYS.
 *   - funding, new_product: always false. Detecting these honestly
 *     needs an external data source (e.g. Crunchbase) this class does
 *     not have; guessing from on-page structured data alone would risk
 *     false positives directly feeding a sales-prioritization score,
 *     which is worse than reporting nothing.
 *
 * social_presence is deliberately NOT produced here — it's added by an
 * enrichment step in AssembleAnalysisResultsJob once ContactInfoResult
 * is available, to avoid this detector depending on ContactInfoExtractor.
 */
final class BusinessSignalsDetector
{
    private const int BLOG_FRESHNESS_WINDOW_DAYS = 90;

    private const string CAREERS_URL_PATTERN = '/career|jobs|hiring/i';

    private const string CAREERS_TEXT_PATTERN = '/careers?\b|job\s*openings?|we\'?re\s+hiring|join\s+our\s+team/i';

    public function analyze(CrawledPage $page, array $crawledPages): BusinessSignalsResult
    {
        [$careersDetected, $careersDetail, $careersPageUrl] = $this->detectCareers($crawledPages);
        [$hiringDetected, $hiringDetail, $hiringPageUrl] = $this->detectHiring($crawledPages);
        [$blogUpdateDetected, $blogUpdateDetail, $blogUpdatePageUrl] = $this->detectBlogUpdate($crawledPages);

        return new BusinessSignalsResult(
            url: $page->url,
            signals: [
                'careers' => $careersDetected,
                'hiring' => $hiringDetected,
                'blog_update' => $blogUpdateDetected,
                'funding' => false,
                'new_product' => false,
            ],
            signalDetails: [
                'careers' => $careersDetail,
                'hiring' => $hiringDetail,
                'blog_update' => $blogUpdateDetail,
                'funding' => 'Not detected — funding signals require an external data source (e.g. Crunchbase) this platform does not currently integrate.',
                'new_product' => 'Not detected — new-product signals require an external data source this platform does not currently integrate.',
            ],
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
            // Phase M4 — see BusinessSignalsResult::$signalPageUrls's own
            // docblock: the SAME page each detect*() method already cited
            // inside its own $*Detail prose string, now also exposed as
            // its own structured value. funding/new_product have no page
            // to point at (never detected at all — see this class's own
            // docblock), so they're omitted here rather than set to null
            // for a key that would never be looked up anyway.
            signalPageUrls: [
                'careers' => $careersPageUrl,
                'hiring' => $hiringPageUrl,
                'blog_update' => $blogUpdatePageUrl,
            ],
        );
    }

    /**
     * @param array<int, CrawledPage> $crawledPages
     * @return array{0: bool, 1: ?string, 2: ?string}
     */
    private function detectCareers(array $crawledPages): array
    {
        foreach ($crawledPages as $crawledPage) {
            if ($this->hasJobPostingSchema($crawledPage)) {
                return [true, "JobPosting schema found on {$crawledPage->url}", $crawledPage->url];
            }

            if (preg_match(self::CAREERS_URL_PATTERN, $crawledPage->url) === 1) {
                return [true, "Careers-related URL found: {$crawledPage->url}", $crawledPage->url];
            }

            if ($crawledPage->title !== null && preg_match(self::CAREERS_TEXT_PATTERN, $crawledPage->title) === 1) {
                return [true, "Careers-related page title found on {$crawledPage->url}: \"{$crawledPage->title}\"", $crawledPage->url];
            }

            foreach ($crawledPage->headings as $heading) {
                /** @var Heading $heading */
                if (preg_match(self::CAREERS_TEXT_PATTERN, $heading->text) === 1) {
                    return [true, "Careers-related heading found on {$crawledPage->url}: \"{$heading->text}\"", $crawledPage->url];
                }
            }

            foreach ($crawledPage->anchors as $anchor) {
                if ($anchor->text !== null && preg_match(self::CAREERS_TEXT_PATTERN, $anchor->text) === 1) {
                    return [true, "Careers-related link found on {$crawledPage->url}: \"{$anchor->text}\"", $crawledPage->url];
                }
            }
        }

        return [false, null, null];
    }

    /**
     * @param array<int, CrawledPage> $crawledPages
     * @return array{0: bool, 1: ?string, 2: ?string}
     */
    private function detectHiring(array $crawledPages): array
    {
        foreach ($crawledPages as $crawledPage) {
            $jobPostingCount = $this->jobPostingSchemaCount($crawledPage);

            if ($jobPostingCount > 0) {
                return [true, "{$jobPostingCount} JobPosting schema block(s) found on {$crawledPage->url}", $crawledPage->url];
            }
        }

        return [false, 'A careers page may exist, but no schema.org JobPosting block was found confirming an active, specific listing.', null];
    }

    /**
     * @param array<int, CrawledPage> $crawledPages
     * @return array{0: bool, 1: ?string, 2: ?string}
     */
    private function detectBlogUpdate(array $crawledPages): array
    {
        $blogPageFound = false;
        $cutoff = (new \DateTimeImmutable)->modify('-'.self::BLOG_FRESHNESS_WINDOW_DAYS.' days');

        foreach ($crawledPages as $crawledPage) {
            if (! $this->looksLikeBlogUrl($crawledPage->url)) {
                continue;
            }

            $blogPageFound = true;
            $publishedAt = $this->latestArticleDate($crawledPage);

            if ($publishedAt !== null && $publishedAt >= $cutoff) {
                $formatted = $publishedAt->format('Y-m-d');

                return [true, "Blog/news page at {$crawledPage->url} has a schema.org publish/modified date of {$formatted}, within the last ".self::BLOG_FRESHNESS_WINDOW_DAYS.' days.', $crawledPage->url];
            }
        }

        if ($blogPageFound) {
            return [false, 'A blog/news section was found, but no schema.org date within the last '.self::BLOG_FRESHNESS_WINDOW_DAYS.' days could be confirmed.', null];
        }

        return [false, null, null];
    }

    private function looksLikeBlogUrl(string $url): bool
    {
        return preg_match('#/(blog|news)(/|$)#i', $url) === 1;
    }

    private function hasJobPostingSchema(CrawledPage $page): bool
    {
        return $this->jobPostingSchemaCount($page) > 0;
    }

    private function jobPostingSchemaCount(CrawledPage $page): int
    {
        $count = 0;

        foreach ($page->schema as $block) {
            /** @var SchemaBlock $block */
            if ($block->valid && in_array('JobPosting', $block->types, true)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Scans this page's valid Article/BlogPosting schema blocks for the
     * most recent datePublished/dateModified value, searching nested
     * @graph arrays the same way SchemaBlock::$types already does at
     * parse time. Returns null when no parseable date is present,
     * rather than guessing from the page's own crawl date or similar.
     */
    private function latestArticleDate(CrawledPage $page): ?\DateTimeImmutable
    {
        $latest = null;

        foreach ($page->schema as $block) {
            /** @var SchemaBlock $block */
            if (! $block->valid || $block->data === null) {
                continue;
            }

            $isArticleType = array_intersect(['Article', 'BlogPosting', 'NewsArticle'], $block->types) !== [];

            if (! $isArticleType) {
                continue;
            }

            foreach ($this->extractDateStrings($block->data) as $dateString) {
                try {
                    $date = new \DateTimeImmutable($dateString);
                } catch (\Exception) {
                    continue;
                }

                if ($latest === null || $date > $latest) {
                    $latest = $date;
                }
            }
        }

        return $latest;
    }

    /**
     * @param array<mixed> $data
     * @return array<int, string>
     */
    private function extractDateStrings(array $data): array
    {
        $dates = [];

        foreach (['datePublished', 'dateModified'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $dates[] = $data[$key];
            }
        }

        if (isset($data['@graph']) && is_array($data['@graph'])) {
            foreach ($data['@graph'] as $node) {
                if (is_array($node)) {
                    $dates = [...$dates, ...$this->extractDateStrings($node)];
                }
            }
        }

        return $dates;
    }
}