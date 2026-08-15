<?php

declare(strict_types=1);

namespace App\Audit\Seo;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Crawler\DTO\CrawlResult;
use App\Audit\Crawler\DTO\LinkInventoryEntry;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\Seo\Contracts\SeoAnalyzerServiceInterface;
use App\Audit\Seo\DTO\PageSeoResult;
use App\Audit\Seo\DTO\SeoAuditResult;
use App\Audit\Seo\DTO\SeoIssue;

final class SeoAnalyzerService implements SeoAnalyzerServiceInterface
{
    /**
     * Outbound-link count above which a page is flagged as linking out
     * excessively (a soft heuristic, not a hard SEO rule).
     */
    private const int EXCESSIVE_EXTERNAL_LINKS = 100;

    /**
     * Cap on how many site-wide recommendations are returned — beyond this
     * the list stops being actionable and starts being noise.
     */
    private const int MAX_RECOMMENDATIONS = 15;

    /**
     * Every SeoIssue::$code this analyzer can produce, mapped to a
     * short human-readable label. This is a snapshot alongside — not
     * generated from — each check*() method's own inline `code:`
     * values below; update it here too whenever a check*() method's
     * code changes or a new one is added.
     *
     * Public so App\Discovery\Taxonomy\IssueFilterOptions (Website
     * Discovery module) can reuse this exact vocabulary for its "SEO
     * Issues" filter checkboxes, so a discovered site's issues are
     * described with exactly the same terms an audit of that same
     * site would use — no second, hand-duplicated list to keep in
     * sync by hand.
     *
     * @var array<string, string>
     */
    public const array ISSUE_LABELS = [
        'title_missing' => 'Missing Title Tag',
        'title_too_short' => 'Title Too Short',
        'title_too_long' => 'Title Too Long',
        'description_missing' => 'Missing Meta Description',
        'description_too_short' => 'Meta Description Too Short',
        'description_too_long' => 'Meta Description Too Long',
        'keywords_missing' => 'Missing Meta Keywords',
        'keywords_stuffing' => 'Meta Keywords Stuffing',
        'canonical_missing' => 'Missing Canonical Tag',
        'canonical_points_elsewhere' => 'Canonical Points Elsewhere',
        'robots_noindex' => 'Page Set to Noindex',
        'robots_nofollow' => 'Page Set to Nofollow',
        'open_graph_incomplete' => 'Incomplete Open Graph Tags',
        'twitter_card_missing' => 'Missing Twitter Card Tags',
        'schema_missing' => 'Missing Structured Data',
        'schema_invalid' => 'Invalid Structured Data',
        'heading_h1_missing' => 'Missing H1 Heading',
        'heading_h1_multiple' => 'Multiple H1 Headings',
        'heading_level_skipped' => 'Heading Level Skipped',
        'heading_empty' => 'Empty Heading',
        'alt_missing' => 'Missing Image Alt Text',
        'image_missing_dimensions' => 'Image Missing Dimensions',
        'internal_links_none' => 'No Internal Links',
        'external_links_excessive' => 'Excessive External Links',
        'broken_links_found' => 'Broken Links Found',
        'thin_content' => 'Thin Content',
        'duplicate_title' => 'Duplicate Title Tag',
        'duplicate_description' => 'Duplicate Meta Description',
    ];

    public function __construct(
        private readonly int $titleMinLength = 30,
        private readonly int $titleMaxLength = 60,
        private readonly int $descriptionMinLength = 70,
        private readonly int $descriptionMaxLength = 160,
        private readonly int $thinContentWordCount = 300,
    ) {}

    public function analyze(CrawlResult $crawlResult): SeoAuditResult
    {
        $analyzedAt = (new \DateTimeImmutable)->format(DATE_ATOM);

        $successfulPages = array_values(array_filter(
            $crawlResult->pages,
            static fn (CrawledPage $page): bool => $page->success,
        ));
        $failedPages = array_values(array_filter(
            $crawlResult->pages,
            static fn (CrawledPage $page): bool => ! $page->success,
        ));

        $titleMap = $this->buildDuplicateMap(
            $successfulPages,
            static fn (CrawledPage $page): ?string => $page->title,
        );
        $descriptionMap = $this->buildDuplicateMap(
            $successfulPages,
            static fn (CrawledPage $page): ?string => $page->meta?->description,
        );
        $brokenLinksByPage = $this->buildBrokenLinksByPage($crawlResult);

        $pageResults = [];

        foreach ($successfulPages as $page) {
            $pageResults[] = $this->analyzePage(
                page: $page,
                duplicateTitleCount: $this->duplicateCount($titleMap, $page->title),
                duplicateDescriptionCount: $this->duplicateCount($descriptionMap, $page->meta?->description),
                brokenLinks: $brokenLinksByPage[$page->url] ?? [],
            );
        }

        $averageScore = $pageResults !== []
            ? (int) round(
                array_sum(array_map(static fn (PageSeoResult $r): int => $r->score, $pageResults)) / count($pageResults)
            )
            : 0;

        return new SeoAuditResult(
            startUrl: $crawlResult->startUrl,
            pages: $pageResults,
            failedPageUrls: array_map(static fn (CrawledPage $page): string => $page->url, $failedPages),
            pagesAnalyzed: count($pageResults),
            pagesFailed: count($failedPages),
            averageScore: $averageScore,
            recommendations: $this->buildRecommendations($pageResults),
            analyzedAt: $analyzedAt,
        );
    }

    /**
     * @param  array<int, LinkInventoryEntry>  $brokenLinks  broken links found on this specific page
     */
    private function analyzePage(
        CrawledPage $page,
        int $duplicateTitleCount,
        int $duplicateDescriptionCount,
        array $brokenLinks,
    ): PageSeoResult {
        $issues = [];

        $this->checkTitle($page, $issues);
        $this->checkDescription($page, $issues);
        $this->checkKeywords($page, $issues);
        $this->checkCanonical($page, $issues);
        $this->checkRobots($page, $issues);
        $this->checkOpenGraph($page, $issues);
        $this->checkTwitterCard($page, $issues);
        $this->checkSchema($page, $issues);
        $this->checkHeadings($page, $issues);
        $this->checkAltText($page, $issues);
        $this->checkImageSeo($page, $issues);
        $this->checkInternalLinks($page, $issues);
        $this->checkExternalLinks($page, $issues);
        $this->checkBrokenLinks($page, $brokenLinks, $issues);
        $this->checkThinContent($page, $issues);
        $this->checkDuplicateTitle($page, $duplicateTitleCount, $issues);
        $this->checkDuplicateDescription($page, $duplicateDescriptionCount, $issues);

        [$critical, $warning, $notice] = $this->countBySeverity($issues);

        return new PageSeoResult(
            url: $page->url,
            score: $this->score($issues),
            issues: $issues,
            criticalCount: $critical,
            warningCount: $warning,
            noticeCount: $notice,
        );
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkTitle(CrawledPage $page, array &$issues): void
    {
        $title = $page->title !== null ? trim($page->title) : '';

        if ($title === '') {
            $issues[] = new SeoIssue(
                check: 'title',
                code: 'title_missing',
                severity: SeoSeverity::CRITICAL,
                message: 'The page has no <title> tag.',
                recommendation: 'Add a unique, descriptive <title> tag to every indexable page.',
                pageUrl: $page->url,
                context: 'title tag',
            );

            return;
        }

        $length = mb_strlen($title);

        if ($length < $this->titleMinLength) {
            $issues[] = new SeoIssue(
                check: 'title',
                code: 'title_too_short',
                severity: SeoSeverity::WARNING,
                message: "Title is {$length} characters (minimum {$this->titleMinLength} required).",
                recommendation: "Expand the title to roughly {$this->titleMinLength}-{$this->titleMaxLength} characters.",
                pageUrl: $page->url,
                context: 'title tag',
            );
        } elseif ($length > $this->titleMaxLength) {
            $issues[] = new SeoIssue(
                check: 'title',
                code: 'title_too_long',
                severity: SeoSeverity::WARNING,
                message: "Title is {$length} characters (maximum {$this->titleMaxLength} recommended) and may be truncated in search results.",
                recommendation: "Trim the title to roughly {$this->titleMinLength}-{$this->titleMaxLength} characters.",
                pageUrl: $page->url,
                context: 'title tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkDescription(CrawledPage $page, array &$issues): void
    {
        $description = $page->meta?->description !== null ? trim($page->meta->description) : '';

        if ($description === '') {
            $issues[] = new SeoIssue(
                check: 'description',
                code: 'description_missing',
                severity: SeoSeverity::CRITICAL,
                message: 'The page has no meta description.',
                recommendation: 'Add a unique meta description that summarizes the page and encourages clicks.',
                pageUrl: $page->url,
                context: 'meta description tag',
            );

            return;
        }

        $length = mb_strlen($description);

        if ($length < $this->descriptionMinLength) {
            $issues[] = new SeoIssue(
                check: 'description',
                code: 'description_too_short',
                severity: SeoSeverity::WARNING,
                message: "Meta description is {$length} characters (minimum {$this->descriptionMinLength} required).",
                recommendation: "Expand it to roughly {$this->descriptionMinLength}-{$this->descriptionMaxLength} characters.",
                pageUrl: $page->url,
                context: 'meta description tag',
            );
        } elseif ($length > $this->descriptionMaxLength) {
            $issues[] = new SeoIssue(
                check: 'description',
                code: 'description_too_long',
                severity: SeoSeverity::WARNING,
                message: "Meta description is {$length} characters (maximum {$this->descriptionMaxLength} recommended) and may be truncated in search results.",
                recommendation: "Trim it to roughly {$this->descriptionMinLength}-{$this->descriptionMaxLength} characters.",
                pageUrl: $page->url,
                context: 'meta description tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkKeywords(CrawledPage $page, array &$issues): void
    {
        $keywords = $page->meta?->keywords !== null ? trim($page->meta->keywords) : '';

        if ($keywords === '') {
            $issues[] = new SeoIssue(
                check: 'keywords',
                code: 'keywords_missing',
                severity: SeoSeverity::NOTICE,
                message: 'No meta keywords tag found.',
                recommendation: 'Meta keywords carry little ranking weight today — safe to skip, optional to add.',
                pageUrl: $page->url,
                context: 'meta keywords tag',
            );

            return;
        }

        $count = count(array_filter(array_map('trim', explode(',', $keywords))));

        if ($count > 10) {
            $issues[] = new SeoIssue(
                check: 'keywords',
                code: 'keywords_stuffing',
                severity: SeoSeverity::WARNING,
                message: "Meta keywords tag lists {$count} terms, which can read as keyword stuffing.",
                recommendation: 'Trim the keywords list to the handful of terms most relevant to the page.',
                pageUrl: $page->url,
                context: 'meta keywords tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkCanonical(CrawledPage $page, array &$issues): void
    {
        $canonical = $page->canonical !== null ? trim($page->canonical) : '';

        if ($canonical === '') {
            $issues[] = new SeoIssue(
                check: 'canonical',
                code: 'canonical_missing',
                severity: SeoSeverity::WARNING,
                message: 'No canonical tag found.',
                recommendation: 'Add a self-referencing canonical tag to help prevent duplicate-content issues.',
                pageUrl: $page->url,
                context: 'canonical tag',
            );

            return;
        }

        $pageUrl = $page->finalUrl ?? $page->url;

        if (rtrim($canonical, '/') !== rtrim($pageUrl, '/')) {
            $issues[] = new SeoIssue(
                check: 'canonical',
                code: 'canonical_points_elsewhere',
                severity: SeoSeverity::NOTICE,
                message: "Canonical tag points to \"{$canonical}\" instead of this page's own URL \"{$pageUrl}\".",
                recommendation: 'Confirm this is intentional — otherwise point the canonical back at this page.',
                pageUrl: $page->url,
                elementUrl: $canonical,
                context: 'canonical tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkRobots(CrawledPage $page, array &$issues): void
    {
        if ($page->noIndex) {
            $issues[] = new SeoIssue(
                check: 'robots',
                code: 'robots_noindex',
                severity: SeoSeverity::CRITICAL,
                message: 'Page is marked noindex and will be excluded from search results.',
                recommendation: 'Remove the noindex directive if this page should be discoverable in search.',
                pageUrl: $page->url,
                context: 'meta robots tag',
            );
        }

        if ($page->noFollow) {
            $issues[] = new SeoIssue(
                check: 'robots',
                code: 'robots_nofollow',
                severity: SeoSeverity::WARNING,
                message: 'Page is marked nofollow — its outgoing links pass no authority.',
                recommendation: 'Remove the nofollow directive unless deliberately blocking link equity from this page.',
                pageUrl: $page->url,
                context: 'meta robots tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkOpenGraph(CrawledPage $page, array &$issues): void
    {
        $og = $page->meta?->openGraph ?? [];

        $missing = array_values(array_filter(
            ['og:title', 'og:description', 'og:image'],
            static fn (string $key): bool => ! isset($og[$key]) || trim((string) $og[$key]) === '',
        ));

        if ($missing === []) {
            return;
        }

        $issues[] = new SeoIssue(
            check: 'open_graph',
            code: 'open_graph_incomplete',
            severity: SeoSeverity::WARNING,
            message: 'Missing Open Graph tag(s): '.implode(', ', $missing).'.',
            recommendation: 'Add the missing Open Graph tags so shared links render correctly on social platforms.',
            pageUrl: $page->url,
            context: implode(', ', $missing),
        );
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkTwitterCard(CrawledPage $page, array &$issues): void
    {
        $twitter = $page->meta?->twitter ?? [];

        if (! isset($twitter['twitter:card']) || trim((string) $twitter['twitter:card']) === '') {
            $issues[] = new SeoIssue(
                check: 'twitter_card',
                code: 'twitter_card_missing',
                severity: SeoSeverity::NOTICE,
                message: 'No twitter:card meta tag found.',
                recommendation: 'Add a twitter:card tag so links preview correctly when shared on X/Twitter.',
                pageUrl: $page->url,
                context: 'twitter:card tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkSchema(CrawledPage $page, array &$issues): void
    {
        if ($page->schema === []) {
            $issues[] = new SeoIssue(
                check: 'schema',
                code: 'schema_missing',
                severity: SeoSeverity::NOTICE,
                message: 'No structured data (JSON-LD) found on the page.',
                recommendation: 'Add relevant Schema.org markup (e.g. Article, Product, Organization) to qualify for rich results.',
                pageUrl: $page->url,
                context: 'JSON-LD structured data',
            );

            return;
        }

        $invalidBlocks = array_values(array_filter(
            $page->schema,
            static fn (SchemaBlock $block): bool => ! $block->valid,
        ));
        $invalidCount = count($invalidBlocks);

        if ($invalidCount > 0) {
            $first = $invalidBlocks[0];

            $issues[] = new SeoIssue(
                check: 'schema',
                code: 'schema_invalid',
                severity: SeoSeverity::WARNING,
                message: $invalidCount === 1
                    ? 'One JSON-LD structured data block failed to parse.'
                    : "{$invalidCount} JSON-LD structured data blocks failed to parse.",
                recommendation: 'Validate the structured data with a JSON-LD linter and fix the malformed block(s).',
                pageUrl: $page->url,
                domPath: $first->domPath,
                context: 'invalid JSON-LD block',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkHeadings(CrawledPage $page, array &$issues): void
    {
        $h1Headings = array_values(array_filter(
            $page->headings,
            static fn (Heading $heading): bool => $heading->level === 1,
        ));
        $h1Count = count($h1Headings);

        if ($h1Count === 0) {
            $issues[] = new SeoIssue(
                check: 'headings',
                code: 'heading_h1_missing',
                severity: SeoSeverity::CRITICAL,
                message: 'Page has no H1 heading.',
                recommendation: "Add exactly one H1 that describes the page's main topic.",
                pageUrl: $page->url,
                context: 'H1 heading',
            );
        } elseif ($h1Count > 1) {
            $extraH1 = $h1Headings[1];

            $issues[] = new SeoIssue(
                check: 'headings',
                code: 'heading_h1_multiple',
                severity: SeoSeverity::WARNING,
                message: "Page has {$h1Count} H1 headings.",
                recommendation: 'Keep a single H1 per page and demote the others to H2/H3.',
                pageUrl: $page->url,
                elementUrl: $extraH1->pageUrl,
                domPath: $extraH1->domPath,
                context: 'H1 #2',
            );
        }

        $skipped = $this->findSkippedHeading($page->headings);

        if ($skipped !== null) {
            [$previousLevel, $heading] = $skipped;

            $issues[] = new SeoIssue(
                check: 'headings',
                code: 'heading_level_skipped',
                severity: SeoSeverity::WARNING,
                message: "Heading level jumps from H{$previousLevel} to H{$heading->level} without an intervening H"
                    .($previousLevel + 1).'.',
                recommendation: 'Keep the heading hierarchy sequential so it accurately reflects page structure.',
                pageUrl: $page->url,
                elementUrl: $heading->pageUrl,
                domPath: $heading->domPath,
                context: "H{$heading->level} heading",
            );
        }

        $emptyHeadings = array_values(array_filter(
            $page->headings,
            static fn (Heading $heading): bool => trim($heading->text) === '',
        ));
        $emptyCount = count($emptyHeadings);

        if ($emptyCount > 0) {
            $first = $emptyHeadings[0];

            $issues[] = new SeoIssue(
                check: 'headings',
                code: 'heading_empty',
                severity: SeoSeverity::WARNING,
                message: $emptyCount === 1
                    ? 'One heading tag has no text.'
                    : "{$emptyCount} heading tags have no text.",
                recommendation: 'Remove empty heading tags or give them meaningful text.',
                pageUrl: $page->url,
                elementUrl: $first->pageUrl,
                domPath: $first->domPath,
                context: "H{$first->level} tag",
            );
        }
    }

    /**
     * Finds the first place in document order where the heading level jumps
     * by more than one (e.g. H2 straight to H4 with no H3 in between).
     *
     * @param  array<int, Heading>  $headings
     * @return array{0: int, 1: Heading}|null the previous level and the
     *                                        offending heading, or null if the hierarchy is sequential
     */
    private function findSkippedHeading(array $headings): ?array
    {
        $previousLevel = null;

        foreach ($headings as $heading) {
            if ($previousLevel !== null && $heading->level > $previousLevel + 1) {
                return [$previousLevel, $heading];
            }

            $previousLevel = $heading->level;
        }

        return null;
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkAltText(CrawledPage $page, array &$issues): void
    {
        $missingImages = array_values(array_filter(
            $page->images,
            static fn (ImageAsset $image): bool => $image->alt === null || trim($image->alt) === '',
        ));
        $missing = count($missingImages);

        if ($missing > 0) {
            $first = $missingImages[0];

            $issues[] = new SeoIssue(
                check: 'alt',
                code: 'alt_missing',
                severity: SeoSeverity::WARNING,
                message: $missing === 1
                    ? "One image is missing alt text: {$first->url}."
                    : "{$missing} images are missing alt text, including {$first->url}.",
                recommendation: 'Add descriptive alt text to every meaningful image (alt="" is fine for purely decorative ones).',
                pageUrl: $page->url,
                elementUrl: $first->url,
                domPath: $first->domPath,
                context: 'image missing alt text',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkImageSeo(CrawledPage $page, array &$issues): void
    {
        $missingDimensionImages = array_values(array_filter(
            $page->images,
            static fn (ImageAsset $image): bool => $image->width === null || $image->height === null,
        ));
        $missingDimensions = count($missingDimensionImages);

        if ($missingDimensions > 0) {
            $first = $missingDimensionImages[0];

            $issues[] = new SeoIssue(
                check: 'image_seo',
                code: 'image_missing_dimensions',
                severity: SeoSeverity::NOTICE,
                message: $missingDimensions === 1
                    ? "One image has no width/height attributes: {$first->url}."
                    : "{$missingDimensions} images have no width/height attributes, including {$first->url}.",
                recommendation: 'Set explicit width and height on images to avoid layout shift while they load.',
                pageUrl: $page->url,
                elementUrl: $first->url,
                domPath: $first->domPath,
                context: 'image missing width/height',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkInternalLinks(CrawledPage $page, array &$issues): void
    {
        if ($page->internalLinkUrls === []) {
            $issues[] = new SeoIssue(
                check: 'internal_link',
                code: 'internal_links_none',
                severity: SeoSeverity::WARNING,
                message: 'Page has no internal links to other pages on the site.',
                recommendation: 'Link to related pages so crawlers and users can navigate onward from here.',
                pageUrl: $page->url,
                context: 'internal links',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkExternalLinks(CrawledPage $page, array &$issues): void
    {
        $count = count($page->externalLinkUrls);

        if ($count > self::EXCESSIVE_EXTERNAL_LINKS) {
            $issues[] = new SeoIssue(
                check: 'external_link',
                code: 'external_links_excessive',
                severity: SeoSeverity::NOTICE,
                message: "Page links out to {$count} external URLs.",
                recommendation: 'Review whether all outbound links are necessary — an excessive number can dilute page authority.',
                pageUrl: $page->url,
                context: 'external links',
            );
        }
    }

    /**
     * @param  array<int, LinkInventoryEntry>  $brokenLinks
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkBrokenLinks(CrawledPage $page, array $brokenLinks, array &$issues): void
    {
        if ($brokenLinks === []) {
            return;
        }

        $count = count($brokenLinks);
        $sample = implode(', ', array_slice(
            array_map(static fn (LinkInventoryEntry $link): string => $link->url, $brokenLinks),
            0,
            3,
        ));

        $issues[] = new SeoIssue(
            check: 'broken_link',
            code: 'broken_links_found',
            severity: SeoSeverity::CRITICAL,
            message: $count === 1
                ? "One broken link found on this page: {$sample}."
                : "{$count} broken links found on this page, including: {$sample}.",
            recommendation: 'Fix or remove the broken link(s) — they hurt both crawlability and user experience.',
            pageUrl: $page->url,
            elementUrl: $brokenLinks[0]->url,
            context: 'broken link',
        );
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkThinContent(CrawledPage $page, array &$issues): void
    {
        if ($page->wordCount < $this->thinContentWordCount) {
            $issues[] = new SeoIssue(
                check: 'thin_content',
                code: 'thin_content',
                severity: SeoSeverity::WARNING,
                message: "Page has only {$page->wordCount} words of body content"
                    ." (below the {$this->thinContentWordCount}-word guideline).",
                recommendation: 'Expand the page with more substantive, unique content, or consolidate it with a related page.',
                pageUrl: $page->url,
                context: 'body content',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkDuplicateTitle(CrawledPage $page, int $duplicateCount, array &$issues): void
    {
        if ($duplicateCount > 0) {
            $issues[] = new SeoIssue(
                check: 'duplicate_title',
                code: 'duplicate_title',
                severity: SeoSeverity::WARNING,
                message: $duplicateCount === 1
                    ? 'This title is also used on 1 other page.'
                    : "This title is also used on {$duplicateCount} other pages.",
                recommendation: 'Give each page a unique, descriptive title.',
                pageUrl: $page->url,
                context: 'title tag',
            );
        }
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function checkDuplicateDescription(CrawledPage $page, int $duplicateCount, array &$issues): void
    {
        if ($duplicateCount > 0) {
            $issues[] = new SeoIssue(
                check: 'duplicate_description',
                code: 'duplicate_description',
                severity: SeoSeverity::WARNING,
                message: $duplicateCount === 1
                    ? 'This meta description is also used on 1 other page.'
                    : "This meta description is also used on {$duplicateCount} other pages.",
                recommendation: 'Give each page a unique meta description.',
                pageUrl: $page->url,
                context: 'meta description tag',
            );
        }
    }

    /**
     * @param  array<int, CrawledPage>  $pages
     * @param  \Closure(CrawledPage): ?string  $extractor
     * @return array<string, array<int, string>> normalized value => page URLs sharing it
     */
    private function buildDuplicateMap(array $pages, \Closure $extractor): array
    {
        $map = [];

        foreach ($pages as $page) {
            $value = $extractor($page);

            if ($value === null || trim($value) === '') {
                continue;
            }

            $key = mb_strtolower(trim($value));
            $map[$key][] = $page->url;
        }

        return $map;
    }

    /**
     * @param  array<string, array<int, string>>  $map
     */
    private function duplicateCount(array $map, ?string $value): int
    {
        if ($value === null || trim($value) === '') {
            return 0;
        }

        $count = count($map[mb_strtolower(trim($value))] ?? []);

        return $count > 1 ? $count - 1 : 0;
    }

    /**
     * @return array<string, array<int, LinkInventoryEntry>> page URL => broken links found on it
     */
    private function buildBrokenLinksByPage(CrawlResult $crawlResult): array
    {
        $map = [];

        foreach ($crawlResult->brokenLinks as $broken) {
            foreach ($broken->foundOnPages as $pageUrl) {
                $map[$pageUrl][] = $broken;
            }
        }

        return $map;
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     */
    private function score(array $issues): int
    {
        $deduction = array_sum(array_map(
            static fn (SeoIssue $issue): int => $issue->severity->scoreWeight(),
            $issues,
        ));

        return max(0, min(100, 100 - $deduction));
    }

    /**
     * @param  array<int, SeoIssue>  $issues
     * @return array{0: int, 1: int, 2: int} critical, warning, notice counts
     */
    private function countBySeverity(array $issues): array
    {
        $critical = 0;
        $warning = 0;
        $notice = 0;

        foreach ($issues as $issue) {
            match ($issue->severity) {
                SeoSeverity::CRITICAL => $critical++,
                SeoSeverity::WARNING => $warning++,
                SeoSeverity::NOTICE => $notice++,
            };
        }

        return [$critical, $warning, $notice];
    }

    /**
     * Aggregate every page's issues into a deduplicated, prioritized list
     * of site-wide recommendations — critical issues first, then by how
     * many pages each issue affects.
     *
     * @param  array<int, PageSeoResult>  $pageResults
     * @return array<int, string>
     */
    private function buildRecommendations(array $pageResults): array
    {
        /** @var array<string, array{severity: SeoSeverity, recommendation: string, pages: int}> $grouped */
        $grouped = [];

        foreach ($pageResults as $pageResult) {
            foreach ($pageResult->issues as $issue) {
                if (! isset($grouped[$issue->code])) {
                    $grouped[$issue->code] = [
                        'severity' => $issue->severity,
                        'recommendation' => $issue->recommendation ?? $issue->message,
                        'pages' => 0,
                    ];
                }

                $grouped[$issue->code]['pages']++;
            }
        }

        $severityOrder = [
            SeoSeverity::CRITICAL->value => 0,
            SeoSeverity::WARNING->value => 1,
            SeoSeverity::NOTICE->value => 2,
        ];

        uasort($grouped, static function (array $a, array $b) use ($severityOrder): int {
            $bySeverity = $severityOrder[$a['severity']->value] <=> $severityOrder[$b['severity']->value];

            return $bySeverity !== 0 ? $bySeverity : $b['pages'] <=> $a['pages'];
        });

        $recommendations = [];

        foreach ($grouped as $entry) {
            $pagesLabel = $entry['pages'] === 1 ? '1 page' : "{$entry['pages']} pages";
            $recommendations[] = "{$entry['recommendation']} (affects {$pagesLabel})";
        }

        return array_slice($recommendations, 0, self::MAX_RECOMMENDATIONS);
    }
}
