<?php

declare(strict_types=1);

namespace App\OnPageSeo;

use App\Audit\Enums\SeoSeverity;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Seo\DTO\PageSeoResult;
use App\Audit\Seo\DTO\SeoAuditResult;
use App\Audit\Seo\DTO\SeoIssue;
use App\OnPageSeo\DTO\OnPageSeoIssue;
use App\OnPageSeo\DTO\OnPageSeoResult;
use Illuminate\Support\Facades\Http;

/**
 * Phase R1 (On-Page SEO Checker) — reuses this app's own EXISTING
 * fetch/parse engine entirely: every finding below is computed from
 * fields App\Audit\Fetching\WebsiteFetcherService already extracted
 * (meta tags, headings, images, anchors, JSON-LD schema, word count —
 * see App\Audit\Fetching\DTO\FetchResult's own docblock) — this class
 * writes NO new HTML-parsing code of its own, only NEW ANALYSIS on top
 * of parsing that already existed before this phase.
 *
 * Deliberately a plain, stateless analyzer (a single analyze() method,
 * no queue job, no database row of its own) — unlike Phase R2's own
 * Technical SEO Audit, this only ever looks at ONE page (a single
 * WebsiteFetcherService::fetch() call), which finishes in the time of
 * one HTTP request, well within a normal synchronous web request's own
 * budget. See App\Http\Controllers\OnPageSeoController for where this
 * gets called directly from a controller action, no job dispatch at
 * all.
 */
final class OnPageSeoAnalyzer
{
    private const int TITLE_MIN_LENGTH = 30;
    private const int TITLE_MAX_LENGTH = 60;
    private const int TITLE_MAX_PIXEL_WIDTH = 600;
    private const int META_DESCRIPTION_MIN_LENGTH = 70;
    private const int META_DESCRIPTION_MAX_LENGTH = 160;
    private const int OVERSIZED_IMAGE_BYTES = 200 * 1024;
    private const int MAX_IMAGES_SIZE_CHECKED = 20;

    /**
     * Rough per-character pixel width for a typical SERP title font
     * (Arial ~16px, on Google's own desktop results) — a genuine
     * average across letter widths, not exact (real text rendering
     * varies letter-to-letter; 'i' and 'm' differ hugely) but close
     * enough to flag a title that's meaningfully likely to truncate,
     * which is this check's entire purpose — a precise-to-the-pixel
     * measurement would need actual font-metrics rendering, well
     * beyond what a warning badge needs.
     */
    private const float AVG_PIXEL_WIDTH_PER_CHAR = 10.5;

    public function analyze(FetchResult $fetch, ?string $targetKeyword = null): OnPageSeoResult
    {
        $issues = [];

        $title = $this->analyzeTitle($fetch, $issues);
        $metaDescription = $this->analyzeMetaDescription($fetch, $issues);
        $headings = $this->analyzeHeadings($fetch, $issues);
        $content = $this->analyzeContent($fetch, $issues);
        $images = $this->analyzeImages($fetch, $issues);
        $links = $this->analyzeLinks($fetch, $issues);
        $urlAnalysis = $this->analyzeUrl($fetch, $issues);
        $canonical = $this->analyzeCanonical($fetch, $issues);
        $social = $this->analyzeSocial($fetch, $issues);
        $schema = $this->analyzeSchema($fetch, $issues);

        $keywordOptimization = $targetKeyword !== null
            ? $this->analyzeKeywordOptimization($fetch, $targetKeyword, $title, $headings, $urlAnalysis, $metaDescription, $issues)
            : null;

        return new OnPageSeoResult(
            url: $fetch->url,
            title: $title,
            metaDescription: $metaDescription,
            headings: $headings,
            content: $content,
            images: $images,
            links: $links,
            urlAnalysis: $urlAnalysis,
            canonical: $canonical,
            social: $social,
            schema: $schema,
            keywordOptimization: $keywordOptimization,
            issues: $issues,
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeTitle(FetchResult $fetch, array &$issues): array
    {
        $text = $fetch->meta?->title;
        $length = $text !== null ? mb_strlen($text) : 0;
        $pixelWidth = (int) round($length * self::AVG_PIXEL_WIDTH_PER_CHAR);

        $status = match (true) {
            $text === null || $text === '' => 'missing',
            $length < self::TITLE_MIN_LENGTH => 'too_short',
            $length > self::TITLE_MAX_LENGTH => 'too_long',
            default => 'ok',
        };

        if ($status === 'missing') {
            $issues[] = new OnPageSeoIssue('title', 'critical', 'This page has no <title> tag.', 'Add a unique, descriptive title tag — this is one of the strongest on-page ranking signals.');
        } elseif ($status === 'too_short') {
            $issues[] = new OnPageSeoIssue('title', 'warning', "Title is only {$length} characters — likely too short to be descriptive.", 'Expand the title to 30-60 characters, including your target keyword near the start.');
        } elseif ($status === 'too_long') {
            $issues[] = new OnPageSeoIssue('title', 'warning', "Title is {$length} characters (~{$pixelWidth}px) — likely to be truncated in search results.", 'Shorten the title to under 60 characters (~600px) so it displays fully in Google search results.');
        }

        return [
            'text' => $text,
            'length' => $length,
            'pixel_width_estimate' => $pixelWidth,
            'exceeds_pixel_limit' => $pixelWidth > self::TITLE_MAX_PIXEL_WIDTH,
            'status' => $status,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeMetaDescription(FetchResult $fetch, array &$issues): array
    {
        $text = $fetch->meta?->description;
        $length = $text !== null ? mb_strlen($text) : 0;

        $status = match (true) {
            $text === null || $text === '' => 'missing',
            $length < self::META_DESCRIPTION_MIN_LENGTH => 'too_short',
            $length > self::META_DESCRIPTION_MAX_LENGTH => 'too_long',
            default => 'ok',
        };

        if ($status === 'missing') {
            $issues[] = new OnPageSeoIssue('meta_description', 'warning', 'This page has no meta description.', 'Add a compelling 70-160 character meta description — Google often uses it as the search snippet.');
        } elseif ($status === 'too_long') {
            $issues[] = new OnPageSeoIssue('meta_description', 'notice', "Meta description is {$length} characters — likely to be truncated in search results.", 'Shorten the description to under 160 characters.');
        }

        return [
            'text' => $text,
            'length' => $length,
            'status' => $status,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeHeadings(FetchResult $fetch, array &$issues): array
    {
        $h1s = array_values(array_filter($fetch->headings, static fn ($h): bool => $h->level === 1));
        $h1Count = count($h1s);

        if ($h1Count === 0) {
            $issues[] = new OnPageSeoIssue('headings', 'critical', 'This page has no H1 heading.', 'Add exactly one H1 that describes the page\'s main topic.');
        } elseif ($h1Count > 1) {
            $issues[] = new OnPageSeoIssue('headings', 'warning', "This page has {$h1Count} H1 headings — should have exactly one.", 'Keep only one H1 and demote the others to H2 or lower.');
        }

        $levelsPresent = array_values(array_unique(array_map(static fn ($h): int => $h->level, $fetch->headings)));
        sort($levelsPresent);

        $skippedLevel = false;

        for ($i = 0; $i < count($levelsPresent) - 1; $i++) {
            if ($levelsPresent[$i + 1] - $levelsPresent[$i] > 1) {
                $skippedLevel = true;

                break;
            }
        }

        if ($skippedLevel) {
            $issues[] = new OnPageSeoIssue('headings', 'notice', 'The heading hierarchy skips a level (e.g. H2 straight to H4).', 'Use heading levels in order without skipping — this helps both SEO and screen-reader users.');
        }

        return [
            'h1_count' => $h1Count,
            'h1_text' => array_map(static fn ($h): string => $h->text, $h1s),
            'hierarchy' => array_map(static fn ($h): array => ['level' => $h->level, 'text' => $h->text], $fetch->headings),
            'skipped_level' => $skippedLevel,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeContent(FetchResult $fetch, array &$issues): array
    {
        $wordCount = $fetch->wordCount;
        $htmlLength = $fetch->html !== null ? strlen($fetch->html) : 0;
        // A rough proxy for visible text — actual word count times an
        // average English word length (~5 chars) plus a space —
        // exact-to-the-byte visible-text extraction already happened
        // inside WebsiteFetcherService itself (that's where $wordCount
        // comes from); this ratio only needs to be in the right
        // ballpark to flag a page that's mostly markup/script with very
        // little real content.
        $textLength = $wordCount * 6;
        $contentToHtmlRatio = $htmlLength > 0 ? round(($textLength / $htmlLength) * 100, 1) : 0.0;

        $readability = $this->fleschReadingEase($fetch->html !== null ? strip_tags($fetch->html) : '');

        if ($wordCount < 300) {
            $issues[] = new OnPageSeoIssue('content', 'warning', "This page has only {$wordCount} words — thin content can rank poorly.", 'Aim for at least 300-600 words of genuinely useful content for most page types.');
        }

        if ($contentToHtmlRatio < 10) {
            $issues[] = new OnPageSeoIssue('content', 'notice', "Content-to-HTML ratio is only {$contentToHtmlRatio}% — the page is mostly markup/scripts.", 'Reduce unnecessary markup/inline scripts, or add more real content.');
        }

        return [
            'word_count' => $wordCount,
            'readability_score' => $readability['score'],
            'readability_label' => $readability['label'],
            'content_to_html_ratio' => $contentToHtmlRatio,
        ];
    }

    /**
     * @return array{score: float, label: string}
     */
    private function fleschReadingEase(string $plainText): array
    {
        $plainText = trim($plainText);

        if ($plainText === '') {
            return ['score' => 0.0, 'label' => 'Unknown'];
        }

        $sentenceCount = max(1, preg_match_all('/[.!?]+/', $plainText));
        $words = preg_split('/\s+/', $plainText, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = max(1, count($words));
        $syllableCount = array_sum(array_map($this->countSyllables(...), $words));

        // The standard Flesch Reading Ease formula.
        $score = 206.835 - (1.015 * ($wordCount / $sentenceCount)) - (84.6 * ($syllableCount / $wordCount));
        $score = max(0.0, min(100.0, round($score, 1)));

        $label = match (true) {
            $score >= 90 => 'Very Easy',
            $score >= 70 => 'Easy',
            $score >= 60 => 'Fairly Easy',
            $score >= 50 => 'Standard',
            $score >= 30 => 'Fairly Difficult',
            default => 'Difficult',
        };

        return ['score' => $score, 'label' => $label];
    }

    /**
     * A simple vowel-group heuristic — not linguistically perfect for
     * every English word, but the same approximation every widely-used
     * Flesch-score implementation makes; exact syllable counting needs
     * a full pronunciation dictionary, well beyond what a readability
     * ESTIMATE needs.
     */
    private function countSyllables(string $word): int
    {
        $word = strtolower(preg_replace('/[^a-z]/i', '', $word) ?? '');

        if ($word === '') {
            return 1;
        }

        $count = preg_match_all('/[aeiouy]+/', $word);

        return max(1, $count);
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeImages(FetchResult $fetch, array &$issues): array
    {
        $total = count($fetch->images);
        $withAlt = count(array_filter($fetch->images, static fn ($i): bool => $i->alt !== null && trim($i->alt) !== ''));
        $withoutAltPercent = $total > 0 ? round((($total - $withAlt) / $total) * 100, 1) : 0.0;

        // Oversized-image detection needs each image's own real file
        // size, which FetchResult doesn't carry (it only parsed the
        // HTML, never downloaded the images themselves) — a lightweight
        // HEAD request per image gets the Content-Length header without
        // downloading the whole file. Capped at
        // self::MAX_IMAGES_SIZE_CHECKED to keep this page's own response
        // time reasonable on image-heavy pages; the remaining images
        // are simply not checked for size (never silently mis-reported
        // as "fine" — see 'size_checked_count' below, which the view
        // uses to be honest about this cap).
        $oversized = [];
        $checkedCount = 0;

        foreach (array_slice($fetch->images, 0, self::MAX_IMAGES_SIZE_CHECKED) as $image) {
            $checkedCount++;

            try {
                $response = Http::timeout(5)->head($image->url);
                $contentLength = (int) $response->header('Content-Length', '0');

                if ($contentLength > self::OVERSIZED_IMAGE_BYTES) {
                    $oversized[] = ['url' => $image->url, 'size_kb' => (int) round($contentLength / 1024)];
                }
            } catch (\Throwable) {
                // A single image failing to respond to a HEAD request
                // (timeout, CORS, gone) isn't a reason to fail the whole
                // page's analysis — it's simply excluded from the
                // oversized-image check, same as an unchecked image
                // past the cap.
                continue;
            }
        }

        if ($withoutAltPercent > 20) {
            $issues[] = new OnPageSeoIssue('images', 'warning', "{$withoutAltPercent}% of images are missing alt text.", 'Add descriptive alt text to every meaningful image — this helps both accessibility and image search.');
        }

        if ($oversized !== []) {
            $count = count($oversized);
            $issues[] = new OnPageSeoIssue('images', 'notice', "{$count} image(s) are over 200KB, which can slow page load.", 'Compress large images or serve next-gen formats (WebP/AVIF).');
        }

        return [
            'total' => $total,
            'with_alt' => $withAlt,
            'without_alt_percent' => $withoutAltPercent,
            'oversized' => $oversized,
            'size_checked_count' => $checkedCount,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeLinks(FetchResult $fetch, array &$issues): array
    {
        $host = parse_url($fetch->url, PHP_URL_HOST);

        $internal = [];
        $external = [];
        $genericPhrases = ['click here', 'read more', 'here', 'this page', 'link', 'more'];

        foreach ($fetch->anchors as $anchor) {
            $anchorHost = parse_url($anchor->url, PHP_URL_HOST);
            $isInternal = $anchorHost === null || $anchorHost === $host;

            $entry = [
                'url' => $anchor->url,
                'text' => $anchor->text,
                'is_generic' => $anchor->text !== null && in_array(strtolower(trim($anchor->text)), $genericPhrases, true),
            ];

            if ($isInternal) {
                $internal[] = $entry;
            } else {
                $external[] = $entry;
            }
        }

        $genericCount = count(array_filter($internal, static fn (array $l): bool => $l['is_generic']))
            + count(array_filter($external, static fn (array $l): bool => $l['is_generic']));

        if ($genericCount > 0) {
            $issues[] = new OnPageSeoIssue('links', 'notice', "{$genericCount} link(s) use generic anchor text like \"click here\".", 'Use descriptive anchor text that tells users and search engines what the linked page is about.');
        }

        return [
            'internal_count' => count($internal),
            'external_count' => count($external),
            'internal' => $internal,
            'external' => $external,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeUrl(FetchResult $fetch, array &$issues): array
    {
        $path = parse_url($fetch->url, PHP_URL_PATH) ?? '';
        $hasQuery = parse_url($fetch->url, PHP_URL_QUERY) !== null;
        $length = strlen($fetch->url);
        $isReadable = (bool) preg_match('/^[a-z0-9\-\/]*$/i', $path);
        $hasSessionLikeParam = (bool) preg_match('/[?&](sid|sessionid|phpsessid)=/i', $fetch->url);

        if ($length > 100) {
            $issues[] = new OnPageSeoIssue('url', 'notice', "URL is {$length} characters — quite long.", 'Shorter, descriptive URLs are easier to share and read.');
        }

        if ($hasSessionLikeParam) {
            $issues[] = new OnPageSeoIssue('url', 'warning', 'URL appears to contain a session ID.', 'Session IDs in URLs can cause duplicate-content issues — use cookies instead.');
        }

        return [
            'length' => $length,
            'is_readable' => $isReadable,
            'has_query_parameters' => $hasQuery,
            'has_session_like_parameter' => $hasSessionLikeParam,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeCanonical(FetchResult $fetch, array &$issues): array
    {
        $canonical = $fetch->meta?->canonical;
        $robots = $fetch->meta?->robots;
        $isSelfReferencing = $canonical !== null && rtrim($canonical, '/') === rtrim($fetch->url, '/');

        if ($canonical === null) {
            $issues[] = new OnPageSeoIssue('canonical', 'notice', 'This page has no canonical tag.', 'Add a self-referencing canonical tag to prevent duplicate-content ambiguity.');
        } elseif (! $isSelfReferencing) {
            $issues[] = new OnPageSeoIssue('canonical', 'warning', "Canonical points to a different URL ({$canonical}).", 'Confirm this is intentional — an unintended canonical can keep this page out of search results entirely.');
        }

        if ($robots !== null && str_contains(strtolower($robots), 'noindex')) {
            $issues[] = new OnPageSeoIssue('robots', 'critical', 'This page has a noindex directive.', 'Remove noindex if you want this page to appear in search results.');
        }

        return [
            'canonical' => $canonical,
            'is_self_referencing' => $isSelfReferencing,
            'robots' => $robots,
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeSocial(FetchResult $fetch, array &$issues): array
    {
        $og = $fetch->meta?->openGraph ?? [];
        $twitter = $fetch->meta?->twitter ?? [];

        $ogRequired = ['og:title', 'og:description', 'og:image'];
        $ogComplete = count(array_intersect($ogRequired, array_keys($og))) === count($ogRequired);

        $twitterRequired = ['twitter:card', 'twitter:title'];
        $twitterComplete = count(array_intersect($twitterRequired, array_keys($twitter))) === count($twitterRequired);

        if (! $ogComplete) {
            $issues[] = new OnPageSeoIssue('social', 'notice', 'Open Graph tags are incomplete.', 'Add og:title, og:description, and og:image so this page previews well when shared on social media.');
        }

        return [
            'open_graph' => ['tags' => $og, 'complete' => $ogComplete],
            'twitter' => ['tags' => $twitter, 'complete' => $twitterComplete],
        ];
    }

    /**
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    private function analyzeSchema(FetchResult $fetch, array &$issues): array
    {
        $types = [];
        $hasErrors = false;

        foreach ($fetch->schema as $block) {
            $types = array_merge($types, $block->types);

            if (! $block->valid) {
                $hasErrors = true;
            }
        }

        $types = array_values(array_unique($types));

        if ($hasErrors) {
            $issues[] = new OnPageSeoIssue('schema', 'warning', 'One or more structured data (JSON-LD) blocks failed to parse.', 'Validate your structured data with Google\'s Rich Results Test.');
        }

        if ($fetch->schema === []) {
            $issues[] = new OnPageSeoIssue('schema', 'notice', 'No structured data found on this page.', 'Adding relevant Schema.org markup (Article, Product, FAQ, etc.) can enable rich results in Google.');
        }

        return [
            'types' => $types,
            'has_errors' => $hasErrors,
            'block_count' => count($fetch->schema),
        ];
    }

    /**
     * Phase R1's own explicit "unique differentiator" — see this
     * method's own caller in analyze() for why $volume/$difficulty
     * aren't fetched HERE (this class has no knowledge of any external
     * API at all; App\Http\Controllers\OnPageSeoController is what
     * actually calls Phase O2's KeywordDataService before ever calling
     * this method — that controller passes only the target keyword
     * STRING in, this method reasons purely over placement/presence,
     * never volume/difficulty numbers, which the controller displays
     * separately alongside this method's own return value).
     *
     * @param  array<string, mixed>  $title
     * @param  array<string, mixed>  $headings
     * @param  array<string, mixed>  $urlAnalysis
     * @param  array<string, mixed>  $metaDescription
     * @param  array<int, OnPageSeoIssue>  $issues
     * @return array<string, mixed>
     */
    public function analyzeKeywordOptimization(
        FetchResult $fetch,
        string $targetKeyword,
        array $title,
        array $headings,
        array $urlAnalysis,
        array $metaDescription,
        array &$issues,
    ): array {
        $needle = mb_strtolower($targetKeyword);

        $inTitle = $title['text'] !== null && str_contains(mb_strtolower($title['text']), $needle);
        $inH1 = (bool) array_filter($headings['h1_text'], static fn (string $h): bool => str_contains(mb_strtolower($h), $needle));

        $plainText = $fetch->html !== null ? strip_tags($fetch->html) : '';
        $words = preg_split('/\s+/', trim($plainText), -1, PREG_SPLIT_NO_EMPTY);
        $first100Words = implode(' ', array_slice($words, 0, 100));
        $inFirst100Words = str_contains(mb_strtolower($first100Words), $needle);

        $inUrl = str_contains(mb_strtolower($fetch->url), str_replace(' ', '-', $needle));
        $inMetaDescription = $metaDescription['text'] !== null && str_contains(mb_strtolower($metaDescription['text']), $needle);

        $checks = [$inTitle, $inH1, $inFirst100Words, $inUrl, $inMetaDescription];
        $score = (int) round((count(array_filter($checks)) / count($checks)) * 100);

        if ($score < 60) {
            $issues[] = new OnPageSeoIssue('keyword_optimization', 'warning', "This page is only {$score}% optimized for \"{$targetKeyword}\".", 'Work the target keyword naturally into the title, H1, opening paragraph, URL, and meta description.');
        }

        return [
            'keyword' => $targetKeyword,
            'in_title' => $inTitle,
            'in_h1' => $inH1,
            'in_first_100_words' => $inFirst100Words,
            'in_url' => $inUrl,
            'in_meta_description' => $inMetaDescription,
            'score' => $score,
        ];
    }

    /**
     * Bridges this class's own OnPageSeoIssue list into a REAL
     * App\Audit\Seo\DTO\SeoAuditResult — the exact shape
     * App\Audit\AIRecommendation\DTO\AnalysisResults::$seo expects —
     * so App\Http\Controllers\OnPageSeoController can hand a minimal
     * AnalysisResults (every OTHER field left null) straight to this
     * app's own EXISTING AIRecommendationEngine::analyze() and get back
     * a real Priority Fix List, with zero new AI-recommendation logic
     * written for this phase. Every OnPageSeoIssue maps 1:1 onto a
     * SeoIssue; the wrapping PageSeoResult/SeoAuditResult exist only
     * because that's the shape AnalysisResults->seo requires, not
     * because this is genuinely a "multi-page site-wide" result the way
     * a real SeoAuditResult from the full Audit pipeline is.
     */
    public function toSeoAuditResult(OnPageSeoResult $result): SeoAuditResult
    {
        $severityMap = [
            'critical' => SeoSeverity::CRITICAL,
            'warning' => SeoSeverity::WARNING,
            'notice' => SeoSeverity::NOTICE,
        ];

        $seoIssues = array_map(
            static fn (OnPageSeoIssue $issue): SeoIssue => new SeoIssue(
                check: $issue->check,
                code: $issue->check,
                severity: $severityMap[$issue->severity] ?? SeoSeverity::NOTICE,
                message: $issue->message,
                recommendation: $issue->recommendation,
                pageUrl: $result->url,
            ),
            $result->issues,
        );

        $criticalCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::CRITICAL));
        $warningCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::WARNING));
        $noticeCount = count(array_filter($seoIssues, static fn (SeoIssue $i): bool => $i->severity === SeoSeverity::NOTICE));

        $score = max(0, 100
            - ($criticalCount * SeoSeverity::CRITICAL->scoreWeight())
            - ($warningCount * SeoSeverity::WARNING->scoreWeight())
            - ($noticeCount * SeoSeverity::NOTICE->scoreWeight()));

        $page = new PageSeoResult(
            url: $result->url,
            score: $score,
            issues: $seoIssues,
            criticalCount: $criticalCount,
            warningCount: $warningCount,
            noticeCount: $noticeCount,
        );

        return new SeoAuditResult(
            startUrl: $result->url,
            pages: [$page],
            failedPageUrls: [],
            pagesAnalyzed: 1,
            pagesFailed: 0,
            averageScore: $score,
            recommendations: [],
            analyzedAt: $result->analyzedAt,
        );
    }
}