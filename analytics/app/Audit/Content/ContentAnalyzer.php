<?php

declare(strict_types=1);

namespace App\Audit\Content;

use App\Audit\Content\DTO\ContentCheckResult;
use App\Audit\Content\DTO\ContentResult;
use App\Audit\Enums\ContentCheckStatus;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\SchemaBlock;

/**
 * Runs a fixed set of basic content-quality checks against a single
 * fetched page.
 *
 * Part 1: word count, estimated reading time, and a lightweight grammar
 * scan.
 *
 * Part 2: duplicate content, AI-generated probability, and keyword
 * density. Like Grammar, these three are static, single-page heuristics
 * — there is no external corpus, search index, or AI-detection model
 * available here, so each is scoped to what a single fetched page can
 * actually reveal:
 *  - Duplicate Content looks for repeated block-level text *within the
 *    page itself* (boilerplate/copy-paste signals), not duplication
 *    against other pages or sites.
 *  - AI Generated Probability is a lightweight stylometric proxy
 *    (sentence-length uniformity, common AI transition phrases, lexical
 *    diversity) — not a trained classifier, and not a reliable detector.
 *  - Keyword Density treats the page's own most-frequent significant
 *    word as an implicit focus keyword (no target keyword is supplied)
 *    and reports how much of the text it accounts for.
 *
 * Part 3: content freshness and blog posting frequency, plus an overall
 * Content Score, letter Grade, and human-readable Summary rolled up
 * across every check. Like the rest of Part 2, these two checks are
 * static, single-page heuristics:
 *  - Content Freshness looks only at date signals present on this one
 *    fetched page — JSON-LD structured data, date-related meta tags,
 *    the HTTP Last-Modified response header, and visible <time
 *    datetime> elements — and reports how recent the most credible of
 *    those signals is. It cannot see an editorial calendar or a CMS's
 *    internal "last saved" timestamp.
 *  - Blog Frequency has no access to a full blog archive or other
 *    pages — only this one fetched page. It can only estimate a
 *    posting cadence when the page itself exposes multiple post dates
 *    (e.g. a blog index/listing page); on a single-post or non-blog
 *    page there simply isn't enough on-page data to estimate a
 *    cadence, and the check says so rather than guessing.
 *
 * The overall score/grade/summary rollup mirrors
 * UiUxAnalyzer/AccessibilityAnalyzer's (and, in turn, SecurityAnalyzer's)
 * constructor-injected points-averaging approach, adapted to this
 * analyzer's Good/Warning/Critical status vocabulary.
 *
 * Takes a FetchResult rather than a CrawledPage for the same reason
 * AccessibilityAnalyzer and UiUxAnalyzer do: several checks need to walk
 * the raw DOM to extract the page's visible body text, which no existing
 * parsed DTO captures. Word Count reuses FetchResult::$wordCount, which
 * HtmlParser already derives from the same non-content-tag-stripped body
 * text this analyzer extracts, so they stay consistent with each other.
 *
 * Every heuristic check documents its own proxy and limitation, the same
 * documentation convention used throughout the other analyzers in this
 * codebase.
 */
final class ContentAnalyzer
{
    /**
     * HTML tags stripped out of a cloned <body> before extracting visible
     * text, since their contents are never rendered as readable content.
     * Matches HtmlParser::NON_CONTENT_TAGS exactly, so Word Count (which
     * reuses FetchResult::$wordCount, itself derived via this same tag
     * list) and Grammar (which extracts body text directly here) stay
     * consistent with each other.
     *
     * @var array<int, string>
     */
    private const array NON_CONTENT_TAGS = ['script', 'style', 'noscript', 'template'];

    /**
     * Average adult silent-reading speed used to estimate reading time
     * from word count.
     */
    private const int WORDS_PER_MINUTE = 200;

    /**
     * Fewer words than this is flagged as critically thin content.
     */
    private const int WORD_COUNT_CRITICAL_MAX = 300;

    /**
     * Fewer words than this (but at least WORD_COUNT_CRITICAL_MAX) is
     * flagged as a warning; at or above this is considered good.
     */
    private const int WORD_COUNT_WARNING_MAX = 600;

    /**
     * An estimated reading time at or below this many minutes is flagged
     * as critically short — the same thin-content concern Word Count
     * raises, viewed through a reading-time lens.
     */
    private const int READING_TIME_CRITICAL_MAX_MINUTES = 1;

    /**
     * An estimated reading time above this many minutes is flagged as a
     * warning — long single-page content is often better broken into
     * sections or multiple pages.
     */
    private const int READING_TIME_WARNING_MAX_MINUTES = 15;

    /**
     * More potential grammar issues than this is flagged as critical
     * rather than a warning.
     */
    private const int GRAMMAR_CRITICAL_MIN_ISSUES = 5;

    /**
     * Maximum number of example issues included in the Grammar check's
     * reported value, to keep it readable.
     */
    private const int GRAMMAR_SAMPLE_LIMIT = 3;

    /**
     * Block-level elements read as candidate "content blocks" for the
     * Duplicate Content check.
     *
     * @var array<int, string>
     */
    private const array DUPLICATE_BLOCK_TAGS = ['p', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    /**
     * A block shorter than this many words is ignored for Duplicate
     * Content — short repeated fragments ("Read More", "Learn More",
     * nav labels) are normal boilerplate, not a content-quality signal.
     */
    private const int DUPLICATE_BLOCK_MIN_WORDS = 6;

    /**
     * One repeated block group is flagged as a warning; at or above this
     * many distinct repeated block groups is flagged as critical.
     */
    private const int DUPLICATE_CRITICAL_MIN_GROUPS = 3;

    /**
     * Minimum number of sentences required before the AI Generated
     * Probability check will attempt an estimate — below this, sentence
     * length variance and phrase frequency are too noisy to be
     * meaningful.
     */
    private const int AI_MIN_SENTENCES = 5;

    /**
     * Sentence-length coefficient of variation (stdev / mean, in words)
     * at or below this is considered unusually uniform — a weak,
     * non-authoritative signal associated with machine-generated text.
     */
    private const float AI_UNIFORM_SENTENCE_CV_MAX = 0.35;

    /**
     * Type-token ratio (unique words / total words) at or below this is
     * considered low lexical diversity — another weak signal, not proof
     * of AI generation on its own.
     */
    private const float AI_LOW_LEXICAL_DIVERSITY_MAX = 0.40;

    /**
     * Stock transition/hedging phrases that appear disproportionately
     * often in generic AI-generated prose. Presence of several of these
     * is a weak signal, not a determination.
     *
     * @var array<int, string>
     */
    private const array AI_COMMON_PHRASES = [
        'in conclusion', 'in today\'s world', 'it is important to note',
        'it\'s important to note', 'furthermore,', 'additionally,', 'moreover,',
        'overall,', 'in summary,', 'as an ai', 'in the fast-paced', 'in the digital age',
        'plays a crucial role', 'plays a vital role', 'delve into', 'let\'s dive in',
        'navigating the', 'unlock the', 'unleash the', 'in this article, we will',
    ];

    /**
     * Composite heuristic score (0-100) at or above this is reported as
     * a "High" AI-generated probability; below AI_SCORE_WARNING_MIN is
     * reported as "Low".
     */
    private const int AI_SCORE_CRITICAL_MIN = 65;

    /**
     * Composite heuristic score (0-100) at or above this (but below
     * AI_SCORE_CRITICAL_MIN) is reported as "Medium".
     */
    private const int AI_SCORE_WARNING_MIN = 35;

    /**
     * Words shorter than this many characters are excluded when picking
     * the implicit focus keyword for Keyword Density — short function
     * words are rarely meaningful even after stopword filtering.
     */
    private const int KEYWORD_MIN_WORD_LENGTH = 3;

    /**
     * Keyword density (%) at or above this is flagged as a warning;
     * below this is considered natural.
     */
    private const float KEYWORD_DENSITY_WARNING_MIN = 2.5;

    /**
     * Keyword density (%) at or above this is flagged as critical
     * keyword stuffing.
     */
    private const float KEYWORD_DENSITY_CRITICAL_MIN = 4.0;

    /**
     * Common English stopwords excluded when picking the implicit focus
     * keyword for Keyword Density, so the result reflects a meaningful
     * word rather than the most-used function word.
     *
     * @var array<int, string>
     */
    private const array STOPWORDS = [
        'the', 'and', 'for', 'are', 'but', 'not', 'you', 'your', 'with', 'this', 'that',
        'have', 'has', 'had', 'was', 'were', 'will', 'would', 'could', 'should', 'can',
        'from', 'they', 'them', 'their', 'our', 'ours', 'its', 'his', 'her', 'she', 'him',
        'all', 'any', 'about', 'into', 'more', 'most', 'some', 'such', 'than', 'then',
        'there', 'these', 'those', 'what', 'when', 'where', 'which', 'who', 'why', 'how',
        'out', 'off', 'over', 'under', 'again', 'further', 'once', 'here', 'both', 'each',
        'few', 'other', 'own', 'same', 'too', 'very', 'just', 'also', 'been', 'being', 'now',
        'get', 'got', 'one', 'two', 'new', 'use', 'used', 'using', 'made', 'make', 'like',
        'page', 'site', 'website', 'home', 'per', 'via',
    ];

    /**
     * JSON-LD schema.org "@type" values (lowercased) treated as
     * date-bearing article/post content for both Content Freshness and
     * Blog Frequency.
     *
     * @var array<int, string>
     */
    private const array DATED_SCHEMA_TYPES = ['article', 'blogposting', 'newsarticle', 'webpage'];

    /**
     * Meta tag name/property keys (lowercased) treated as a page-level
     * "last updated" or "published" date signal for Content Freshness.
     *
     * @var array<int, string>
     */
    private const array FRESHNESS_META_KEYS = [
        'article:modified_time', 'article:published_time', 'last-modified',
        'dc.date.modified', 'dcterms.modified', 'date',
    ];

    /**
     * A most-recent date signal this many days old or newer is
     * considered fresh.
     */
    private const int FRESHNESS_GOOD_MAX_DAYS = 90;

    /**
     * A most-recent date signal this many days old or newer (but older
     * than FRESHNESS_GOOD_MAX_DAYS) is a warning; older still is
     * critical.
     */
    private const int FRESHNESS_WARNING_MAX_DAYS = 365;

    /**
     * Fewer than this many distinct on-page post dates is not enough to
     * estimate a posting cadence for Blog Frequency.
     */
    private const int BLOG_FREQUENCY_MIN_DATES = 2;

    /**
     * An average gap between on-page post dates at or below this many
     * days is considered a frequent, healthy publishing cadence.
     */
    private const int BLOG_FREQUENCY_GOOD_MAX_AVG_DAYS = 14;

    /**
     * An average gap at or below this many days (but above
     * BLOG_FREQUENCY_GOOD_MAX_AVG_DAYS) is a warning; a wider average
     * gap is critical.
     */
    private const int BLOG_FREQUENCY_WARNING_MAX_AVG_DAYS = 60;

    /**
     * Points-averaging and letter-grade thresholds for the overall
     * Content Score. Constructor-injected so scoring can be tuned
     * without editing check logic. Mirrors UiUxAnalyzer's (and, in
     * turn, AccessibilityAnalyzer's and SecurityAnalyzer's)
     * constructor defaults exactly, adapted to this analyzer's
     * Good/Warning/Critical status vocabulary.
     */
    public function __construct(
        private readonly int $pointsGood = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsCritical = 0,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function analyze(FetchResult $result): ContentResult
    {
        $html = (string) $result->html;

        if (trim($html) === '') {
            return $this->emptyResult($result);
        }

        $xpath = new \DOMXPath($this->loadDocument($html));
        $bodyText = $this->extractBodyText($xpath);

        $checks = [
            'word_count' => $this->checkWordCount($result->wordCount),
            'reading_time' => $this->checkReadingTime($result->wordCount),
            'grammar' => $this->checkGrammar($bodyText),
            'duplicate_content' => $this->checkDuplicateContent($xpath),
            'ai_generated_probability' => $this->checkAiGeneratedProbability($bodyText),
            'keyword_density' => $this->checkKeywordDensity($bodyText),
            'content_freshness' => $this->checkContentFreshness($xpath, $result),
            'blog_frequency' => $this->checkBlogFrequency($xpath, $result),
        ];

        $score = $this->score($checks);
        $grade = $this->grade($score);

        return new ContentResult(
            url: $result->url,
            checks: $checks,
            score: $score,
            grade: $grade,
            summary: $this->summary($checks, $score, $grade),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    private function loadDocument(string $html): \DOMDocument
    {
        $previous = libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom;
    }

    private function emptyResult(FetchResult $result): ContentResult
    {
        $noHtml = new ContentCheckResult(
            metric: 'Word Count',
            value: 'no HTML content to analyze',
            status: ContentCheckStatus::WARNING,
            recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
        );

        $checks = [
            'word_count' => $noHtml,
            'reading_time' => new ContentCheckResult(
                metric: 'Reading Time',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'grammar' => new ContentCheckResult(
                metric: 'Grammar',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'duplicate_content' => new ContentCheckResult(
                metric: 'Duplicate Content',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'ai_generated_probability' => new ContentCheckResult(
                metric: 'AI Generated Probability',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'keyword_density' => new ContentCheckResult(
                metric: 'Keyword Density',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'content_freshness' => new ContentCheckResult(
                metric: 'Content Freshness',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
            'blog_frequency' => new ContentCheckResult(
                metric: 'Blog Frequency',
                value: 'no HTML content to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this '
                    . 'analysis.',
            ),
        ];

        $score = $this->score($checks);
        $grade = $this->grade($score);

        return new ContentResult(
            url: $result->url,
            checks: $checks,
            score: $score,
            grade: $grade,
            summary: $this->summary($checks, $score, $grade),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    private function checkWordCount(int $wordCount): ContentCheckResult
    {
        $value = $wordCount === 1 ? '1 word' : "{$wordCount} words";

        if ($wordCount < self::WORD_COUNT_CRITICAL_MAX) {
            return new ContentCheckResult(
                metric: 'Word Count',
                value: $value,
                status: ContentCheckStatus::CRITICAL,
                recommendation: 'Content is very thin (under ' . self::WORD_COUNT_CRITICAL_MAX . ' words). Add '
                    . 'more substantive, original content — thin pages tend to underperform for both readers '
                    . 'and search engines.',
            );
        }

        if ($wordCount < self::WORD_COUNT_WARNING_MAX) {
            return new ContentCheckResult(
                metric: 'Word Count',
                value: $value,
                status: ContentCheckStatus::WARNING,
                recommendation: 'Consider expanding the content further (aim for ' . self::WORD_COUNT_WARNING_MAX
                    . '+ words where relevant) to more fully cover the topic.',
            );
        }

        return new ContentCheckResult(
            metric: 'Word Count',
            value: $value,
            status: ContentCheckStatus::GOOD,
            recommendation: null,
        );
    }

    private function checkReadingTime(int $wordCount): ContentCheckResult
    {
        if ($wordCount === 0) {
            return new ContentCheckResult(
                metric: 'Reading Time',
                value: '0 min read',
                status: ContentCheckStatus::CRITICAL,
                recommendation: 'No readable content was found to estimate a reading time for. Add page content.',
            );
        }

        $minutes = max(1, (int) ceil($wordCount / self::WORDS_PER_MINUTE));
        $value = $minutes === 1 ? '1 min read' : "{$minutes} min read";

        if ($minutes <= self::READING_TIME_CRITICAL_MAX_MINUTES) {
            return new ContentCheckResult(
                metric: 'Reading Time',
                value: $value,
                status: ContentCheckStatus::CRITICAL,
                recommendation: 'Estimated reading time is very short, which usually means the content is too '
                    . 'thin to fully engage readers. Add more substantive content.',
            );
        }

        if ($minutes > self::READING_TIME_WARNING_MAX_MINUTES) {
            return new ContentCheckResult(
                metric: 'Reading Time',
                value: $value,
                status: ContentCheckStatus::WARNING,
                recommendation: 'Estimated reading time is long. Consider breaking the content into sections, '
                    . 'adding a table of contents, or splitting it across multiple pages.',
            );
        }

        return new ContentCheckResult(
            metric: 'Reading Time',
            value: $value,
            status: ContentCheckStatus::GOOD,
            recommendation: null,
        );
    }

    private function checkGrammar(string $text): ContentCheckResult
    {
        if (trim($text) === '') {
            return new ContentCheckResult(
                metric: 'Grammar',
                value: 'no readable text found to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No readable body text was found to check — verify the page actually renders '
                    . 'visible content.',
            );
        }

        $issues = $this->collectGrammarIssues($text);

        if ($issues === []) {
            return new ContentCheckResult(
                metric: 'Grammar',
                value: 'no potential issues detected',
                status: ContentCheckStatus::GOOD,
                recommendation: null,
            );
        }

        $count = count($issues);
        $sample = implode('; ', array_slice($issues, 0, self::GRAMMAR_SAMPLE_LIMIT));
        $value = $count === 1
            ? "1 potential issue detected: {$sample}"
            : "{$count} potential issues detected, including: {$sample}";

        $status = $count >= self::GRAMMAR_CRITICAL_MIN_ISSUES
            ? ContentCheckStatus::CRITICAL
            : ContentCheckStatus::WARNING;

        return new ContentCheckResult(
            metric: 'Grammar',
            value: $value,
            status: $status,
            recommendation: 'This is a lightweight heuristic scan (repeated words, doubled spacing/punctuation, '
                . 'sentences not starting with a capital letter) — it is not a substitute for a full grammar '
                . 'checker. Proofread the flagged spots, or run the content through a dedicated grammar tool '
                . 'for a thorough review.',
        );
    }

    /**
     * Extracts the page's visible body text with non-content tags
     * (script/style/noscript/template) stripped out first, mirroring
     * HtmlParser::countWords()'s approach: clone <body> so the live
     * document the rest of the analyzer reads is never mutated, then
     * remove non-content descendants from the detached clone before
     * reading its normalized text content.
     */
    private function extractBodyText(\DOMXPath $xpath): string
    {
        $bodyNodes = $xpath->query('//body');

        if ($bodyNodes === false || $bodyNodes->length === 0) {
            return '';
        }

        $body = $bodyNodes->item(0);

        if (! $body instanceof \DOMElement) {
            return '';
        }

        $clone = $body->cloneNode(true);

        if (! $clone instanceof \DOMElement) {
            return '';
        }

        $nonContentQuery = implode(' | ', array_map(
            static fn (string $tag): string => ".//{$tag}",
            self::NON_CONTENT_TAGS,
        ));

        foreach (iterator_to_array($xpath->query($nonContentQuery, $clone) ?: []) as $node) {
            $node->parentNode?->removeChild($node);
        }

        return trim((string) preg_replace('/\s+/u', ' ', $clone->textContent));
    }

    /**
     * Lightweight, markup-free heuristic scan of visible body text for
     * common grammar-adjacent issues: immediately repeated words,
     * doubled spacing, doubled sentence-ending punctuation, and
     * sentences that don't start with a capital letter.
     *
     * @return array<int, string>
     */
    private function collectGrammarIssues(string $text): array
    {
        $issues = [];

        if (preg_match_all('/\b(\w+)\s+\1\b/iu', $text, $matches) > 0) {
            foreach (array_unique(array_map('mb_strtolower', $matches[1])) as $word) {
                $issues[] = "repeated word \"{$word} {$word}\"";
            }
        }

        if (preg_match('/[ ]{2,}/', $text) === 1) {
            $issues[] = 'multiple consecutive spaces found';
        }

        if (preg_match('/[!?.]{2,}/', $text) === 1) {
            $issues[] = 'repeated punctuation found (e.g. "!!" or "..")';
        }

        $lowercaseStarts = $this->countLowercaseSentenceStarts($text);

        if ($lowercaseStarts > 0) {
            $issues[] = $lowercaseStarts === 1
                ? '1 sentence does not start with a capital letter'
                : "{$lowercaseStarts} sentences do not start with a capital letter";
        }

        return $issues;
    }

    private function countLowercaseSentenceStarts(string $text): int
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', trim($text)) ?: [];
        $count = 0;

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);

            if ($sentence === '') {
                continue;
            }

            $firstChar = mb_substr($sentence, 0, 1);

            if (preg_match('/^\p{Ll}$/u', $firstChar) === 1) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Flags block-level text (paragraphs, list items, headings) that is
     * repeated verbatim elsewhere on the same page.
     *
     * This is a single-page, static-markup proxy for duplicate content:
     * there is no external corpus or search index here to compare
     * against other pages or sites, so it can only surface internal
     * repetition — copy-pasted paragraphs, boilerplate blocks stuffed
     * into multiple sections, and similar within-page duplication.
     */
    private function checkDuplicateContent(\DOMXPath $xpath): ContentCheckResult
    {
        $blocks = $this->extractDuplicateCandidateBlocks($xpath);

        if ($blocks === []) {
            return new ContentCheckResult(
                metric: 'Duplicate Content',
                value: 'no block-level content found to compare',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No paragraph, list item, or heading content was found to check for internal '
                    . 'repetition — verify the page actually renders visible content.',
            );
        }

        $groups = [];

        foreach ($blocks as $block) {
            $key = mb_strtolower($block);
            $groups[$key] = ($groups[$key] ?? 0) + 1;
        }

        $duplicateGroups = array_filter($groups, static fn (int $count): bool => $count > 1);

        if ($duplicateGroups === []) {
            return new ContentCheckResult(
                metric: 'Duplicate Content',
                value: 'no repeated blocks detected (' . count($blocks) . ' blocks checked)',
                status: ContentCheckStatus::GOOD,
                recommendation: null,
            );
        }

        $groupCount = count($duplicateGroups);
        $repeatedInstances = array_sum($duplicateGroups) - $groupCount;
        $example = mb_substr((string) array_key_first($duplicateGroups), 0, 80);
        $value = $groupCount === 1
            ? "1 block repeated on the page, e.g. \"{$example}...\""
            : "{$groupCount} distinct blocks repeated on the page ({$repeatedInstances} extra instances), "
                . "e.g. \"{$example}...\"";

        $status = $groupCount >= self::DUPLICATE_CRITICAL_MIN_GROUPS
            ? ContentCheckStatus::CRITICAL
            : ContentCheckStatus::WARNING;

        return new ContentCheckResult(
            metric: 'Duplicate Content',
            value: $value,
            status: $status,
            recommendation: 'This checks for text repeated within this page only, not duplication against other '
                . 'pages or sites. Rewrite or consolidate the repeated blocks so each section adds distinct '
                . 'value, and consider a dedicated plagiarism/duplicate-content tool for a cross-site check.',
        );
    }

    /**
     * Extracts trimmed, whitespace-normalized text from block-level
     * elements (paragraphs, list items, headings), skipping anything
     * shorter than DUPLICATE_BLOCK_MIN_WORDS words so that short,
     * legitimately-repeated boilerplate (e.g. "Read More") doesn't
     * trigger false positives.
     *
     * @return array<int, string>
     */
    private function extractDuplicateCandidateBlocks(\DOMXPath $xpath): array
    {
        $query = implode(' | ', array_map(
            static fn (string $tag): string => "//{$tag}",
            self::DUPLICATE_BLOCK_TAGS,
        ));

        $nodes = $xpath->query($query);

        if ($nodes === false) {
            return [];
        }

        $blocks = [];

        foreach ($nodes as $node) {
            $text = trim((string) preg_replace('/\s+/u', ' ', $node->textContent));

            if ($text === '') {
                continue;
            }

            if (str_word_count($text) < self::DUPLICATE_BLOCK_MIN_WORDS) {
                continue;
            }

            $blocks[] = $text;
        }

        return $blocks;
    }

    /**
     * Estimates a heuristic "AI-generated probability" for the page's
     * visible body text.
     *
     * This is NOT a trained AI-detection classifier — no such model is
     * available here. It is a lightweight stylometric proxy built from
     * three weak, individually-inconclusive signals: unusually uniform
     * sentence lengths, frequent use of stock AI transition phrases, and
     * low lexical diversity. Each contributes to a 0-100 composite score
     * that is reported as Low/Medium/High, and the recommendation always
     * makes the heuristic nature explicit.
     */
    private function checkAiGeneratedProbability(string $text): ContentCheckResult
    {
        if (trim($text) === '') {
            return new ContentCheckResult(
                metric: 'AI Generated Probability',
                value: 'no readable text found to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No readable body text was found to check — verify the page actually renders '
                    . 'visible content.',
            );
        }

        $sentences = array_values(array_filter(
            array_map('trim', preg_split('/(?<=[.!?])\s+/u', trim($text)) ?: []),
            static fn (string $sentence): bool => $sentence !== '',
        ));

        if (count($sentences) < self::AI_MIN_SENTENCES) {
            return new ContentCheckResult(
                metric: 'AI Generated Probability',
                value: 'not enough text to estimate (' . count($sentences) . ' sentences found)',
                status: ContentCheckStatus::WARNING,
                recommendation: 'Add more page content to get a meaningful estimate — this heuristic needs at '
                    . 'least ' . self::AI_MIN_SENTENCES . ' sentences to be reliable.',
            );
        }

        $lengths = array_map(static fn (string $sentence): int => str_word_count($sentence), $sentences);
        $cv = $this->coefficientOfVariation($lengths);

        $lowerText = mb_strtolower($text);
        $phraseHits = 0;

        foreach (self::AI_COMMON_PHRASES as $phrase) {
            if (str_contains($lowerText, $phrase)) {
                $phraseHits++;
            }
        }

        $words = array_filter(preg_split('/[^\p{L}\p{N}\']+/u', $lowerText) ?: [], static fn (string $w): bool => $w !== '');
        $totalWords = count($words);
        $lexicalDiversity = $totalWords > 0 ? count(array_unique($words)) / $totalWords : 1.0;

        $score = 0;
        $score += $cv <= self::AI_UNIFORM_SENTENCE_CV_MAX ? 40 : 0;
        $score += match (true) {
            $phraseHits >= 3 => 30,
            $phraseHits >= 1 => 15,
            default => 0,
        };
        $score += $lexicalDiversity <= self::AI_LOW_LEXICAL_DIVERSITY_MAX ? 30 : 0;

        $band = match (true) {
            $score >= self::AI_SCORE_CRITICAL_MIN => 'High',
            $score >= self::AI_SCORE_WARNING_MIN => 'Medium',
            default => 'Low',
        };

        $status = match (true) {
            $score >= self::AI_SCORE_CRITICAL_MIN => ContentCheckStatus::CRITICAL,
            $score >= self::AI_SCORE_WARNING_MIN => ContentCheckStatus::WARNING,
            default => ContentCheckStatus::GOOD,
        };

        $value = "{$band} ({$score}/100) — sentence-length uniformity, stock phrase usage, and lexical "
            . 'diversity signals';

        return new ContentCheckResult(
            metric: 'AI Generated Probability',
            value: $value,
            status: $status,
            recommendation: $status === ContentCheckStatus::GOOD
                ? null
                : 'This is a heuristic stylometric estimate, not a trained AI-detection model — treat it as a '
                    . 'prompt to review, not a verdict. Vary sentence length and structure, cut stock transition '
                    . 'phrases ("furthermore,", "in conclusion", "delve into"), and add specific, concrete '
                    . 'detail to strengthen the page\'s distinct voice.',
        );
    }

    /**
     * Coefficient of variation (population stdev / mean) of a list of
     * integers. Returns 0.0 for an empty list or a zero mean.
     *
     * @param array<int, int> $values
     */
    private function coefficientOfVariation(array $values): float
    {
        $count = count($values);

        if ($count === 0) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;

        if ($mean <= 0.0) {
            return 0.0;
        }

        $variance = array_sum(array_map(
            static fn (int $v): float => ($v - $mean) ** 2,
            $values,
        )) / $count;

        return sqrt($variance) / $mean;
    }

    /**
     * Reports how much of the page's visible text its single most
     * frequent significant word accounts for.
     *
     * No target keyword is supplied to this analyzer, so this treats
     * the page's own most-frequent non-stopword as an implicit focus
     * keyword — the same proxy SEO tools fall back to for an at-a-glance
     * stuffing check when no target term is configured.
     */
    private function checkKeywordDensity(string $text): ContentCheckResult
    {
        if (trim($text) === '') {
            return new ContentCheckResult(
                metric: 'Keyword Density',
                value: 'no readable text found to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No readable body text was found to check — verify the page actually renders '
                    . 'visible content.',
            );
        }

        $words = array_filter(
            preg_split('/[^\p{L}\p{N}\']+/u', mb_strtolower($text)) ?: [],
            static fn (string $w): bool => $w !== '',
        );
        $totalWords = count($words);

        if ($totalWords === 0) {
            return new ContentCheckResult(
                metric: 'Keyword Density',
                value: 'no words found to analyze',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No readable words were found to check — verify the page actually renders '
                    . 'visible content.',
            );
        }

        $frequencies = [];

        foreach ($words as $word) {
            if (mb_strlen($word) < self::KEYWORD_MIN_WORD_LENGTH) {
                continue;
            }

            if (in_array($word, self::STOPWORDS, true)) {
                continue;
            }

            $frequencies[$word] = ($frequencies[$word] ?? 0) + 1;
        }

        if ($frequencies === []) {
            return new ContentCheckResult(
                metric: 'Keyword Density',
                value: 'no significant (non-stopword) terms found among ' . $totalWords . ' words',
                status: ContentCheckStatus::WARNING,
                recommendation: 'Add more substantive, topic-specific wording — the page is currently made up '
                    . 'almost entirely of short/common words.',
            );
        }

        arsort($frequencies);
        $keyword = (string) array_key_first($frequencies);
        $occurrences = $frequencies[$keyword];
        $density = round(($occurrences / $totalWords) * 100, 2);

        $value = "\"{$keyword}\" appears {$occurrences} times ({$density}% density) among {$totalWords} words";

        if ($density >= self::KEYWORD_DENSITY_CRITICAL_MIN) {
            return new ContentCheckResult(
                metric: 'Keyword Density',
                value: $value,
                status: ContentCheckStatus::CRITICAL,
                recommendation: "\"{$keyword}\" is repeated disproportionately often, which reads as keyword "
                    . 'stuffing to both readers and search engines. Reduce repetition and use natural synonyms '
                    . 'and related phrasing instead.',
            );
        }

        if ($density >= self::KEYWORD_DENSITY_WARNING_MIN) {
            return new ContentCheckResult(
                metric: 'Keyword Density',
                value: $value,
                status: ContentCheckStatus::WARNING,
                recommendation: "\"{$keyword}\" is used quite frequently. Consider varying the wording with "
                    . 'synonyms or related terms so the repetition reads naturally.',
            );
        }

        return new ContentCheckResult(
            metric: 'Keyword Density',
            value: $value,
            status: ContentCheckStatus::GOOD,
            recommendation: null,
        );
    }

    /**
     * Reports how recently the page's own on-page signals say it was
     * published or updated.
     *
     * This looks only at what a single fetched page can expose — it
     * cannot see a CMS's internal edit history or an editorial
     * calendar. Candidate signals, all optional and checked in the
     * same pass: JSON-LD structured data (dateModified/datePublished
     * on Article/BlogPosting/NewsArticle/WebPage types), date-related
     * meta tags (article:modified_time, article:published_time, a
     * bare "date" meta, common Dublin Core variants), the HTTP
     * Last-Modified response header, and visible `<time datetime>`
     * elements. The most recent valid date found across all signals is
     * used, since a page can legitimately carry an old publish date
     * alongside a newer update date.
     */
    private function checkContentFreshness(\DOMXPath $xpath, FetchResult $result): ContentCheckResult
    {
        $candidates = [
            ...$this->extractSchemaDates($result->schema),
            ...$this->extractMetaFreshnessDates($result->meta?->raw ?? []),
            ...$this->extractHeaderDate($result->headers),
            ...$this->extractTimeElementDates($xpath),
        ];

        if ($candidates === []) {
            return new ContentCheckResult(
                metric: 'Content Freshness',
                value: 'no last-updated/published date signal found on the page',
                status: ContentCheckStatus::WARNING,
                recommendation: 'No dateModified/datePublished structured data, date meta tag, Last-Modified '
                    . 'header, or visible <time datetime> element was found. Add one of these so readers and '
                    . 'search engines can tell how current the content is.',
            );
        }

        $mostRecent = $candidates[0];

        foreach ($candidates as $candidate) {
            if ($candidate > $mostRecent) {
                $mostRecent = $candidate;
            }
        }

        $ageDays = max(0, (new \DateTimeImmutable())->diff($mostRecent)->days);
        $value = 'most recent on-page date signal: ' . $mostRecent->format('Y-m-d') . " ({$ageDays} days ago)";

        if ($ageDays <= self::FRESHNESS_GOOD_MAX_DAYS) {
            return new ContentCheckResult(
                metric: 'Content Freshness',
                value: $value,
                status: ContentCheckStatus::GOOD,
                recommendation: null,
            );
        }

        if ($ageDays <= self::FRESHNESS_WARNING_MAX_DAYS) {
            return new ContentCheckResult(
                metric: 'Content Freshness',
                value: $value,
                status: ContentCheckStatus::WARNING,
                recommendation: 'The content is getting dated. Review it for accuracy and update the '
                    . 'dateModified/last-updated signal once you do.',
            );
        }

        return new ContentCheckResult(
            metric: 'Content Freshness',
            value: $value,
            status: ContentCheckStatus::CRITICAL,
            recommendation: 'The most recent on-page date signal is over a year old. Refresh the content and '
                . 'update its dateModified/last-updated signal — stale pages tend to lose both reader trust and '
                . 'search ranking.',
        );
    }

    /**
     * Extracts dateModified/datePublished values from JSON-LD blocks
     * whose "@type" matches DATED_SCHEMA_TYPES.
     *
     * @param array<int, SchemaBlock> $schema
     * @return array<int, \DateTimeImmutable>
     */
    private function extractSchemaDates(array $schema): array
    {
        $dates = [];

        foreach ($schema as $block) {
            $types = array_map('mb_strtolower', $block->types);

            if (array_intersect($types, self::DATED_SCHEMA_TYPES) === []) {
                continue;
            }

            $data = $block->data;

            if (! is_array($data)) {
                continue;
            }

            $raw = $data['dateModified'] ?? $data['datePublished'] ?? null;

            if (is_string($raw)) {
                $parsed = $this->parseDate($raw);

                if ($parsed !== null) {
                    $dates[] = $parsed;
                }
            }
        }

        return $dates;
    }

    /**
     * Extracts a freshness date from meta tags matching
     * FRESHNESS_META_KEYS.
     *
     * @param array<int, array{name: ?string, property: ?string, content: ?string}> $rawMeta
     * @return array<int, \DateTimeImmutable>
     */
    private function extractMetaFreshnessDates(array $rawMeta): array
    {
        $dates = [];

        foreach ($rawMeta as $tag) {
            $key = mb_strtolower((string) ($tag['name'] ?? $tag['property'] ?? ''));
            $content = $tag['content'] ?? null;

            if ($key === '' || $content === null || ! in_array($key, self::FRESHNESS_META_KEYS, true)) {
                continue;
            }

            $parsed = $this->parseDate($content);

            if ($parsed !== null) {
                $dates[] = $parsed;
            }
        }

        return $dates;
    }

    /**
     * Extracts a freshness date from the HTTP Last-Modified response
     * header, if present (header lookup is case-insensitive).
     *
     * @param array<string, string> $headers
     * @return array<int, \DateTimeImmutable>
     */
    private function extractHeaderDate(array $headers): array
    {
        foreach ($headers as $name => $value) {
            if (mb_strtolower($name) === 'last-modified') {
                $parsed = $this->parseDate($value);

                return $parsed !== null ? [$parsed] : [];
            }
        }

        return [];
    }

    /**
     * Extracts dates from every visible `<time datetime="...">`
     * element on the page.
     *
     * @return array<int, \DateTimeImmutable>
     */
    private function extractTimeElementDates(\DOMXPath $xpath): array
    {
        $dates = [];

        foreach ($xpath->query('//time[@datetime]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $parsed = $this->parseDate($node->getAttribute('datetime'));

            if ($parsed !== null) {
                $dates[] = $parsed;
            }
        }

        return $dates;
    }

    private function parseDate(string $raw): ?\DateTimeImmutable
    {
        $raw = trim($raw);

        if ($raw === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($raw);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Estimates how often this page publishes new posts, based purely
     * on distinct post dates the page itself exposes.
     *
     * There is no access here to a full blog archive or any other
     * page — only this one fetched page. On a blog index/listing page
     * that renders several post teasers, enough dated signals
     * (JSON-LD Article/BlogPosting datePublished, or visible `<time
     * datetime>` elements) are usually present to estimate a cadence.
     * On a single-post or non-blog page, there normally isn't — and
     * this check says so rather than guessing from a single data
     * point.
     */
    private function checkBlogFrequency(\DOMXPath $xpath, FetchResult $result): ContentCheckResult
    {
        $dates = array_values(array_unique([
            ...$this->extractSchemaDates($result->schema),
            ...$this->extractTimeElementDates($xpath),
        ], SORT_REGULAR));

        if (count($dates) < self::BLOG_FREQUENCY_MIN_DATES) {
            return new ContentCheckResult(
                metric: 'Blog Frequency',
                value: 'not enough on-page post dates found to estimate a posting cadence (found '
                    . count($dates) . ')',
                status: ContentCheckStatus::WARNING,
                recommendation: 'This check can only estimate posting frequency from post dates exposed on this '
                    . 'same page (e.g. a blog index/listing page). Run it against a blog listing page, or add '
                    . 'datePublished structured data / visible <time datetime> elements to post teasers.',
            );
        }

        usort($dates, static fn (\DateTimeImmutable $a, \DateTimeImmutable $b): int => $b <=> $a);

        $gaps = [];

        for ($i = 0; $i < count($dates) - 1; $i++) {
            $gaps[] = $dates[$i]->diff($dates[$i + 1])->days;
        }

        $avgGapDays = (int) round(array_sum($gaps) / count($gaps));
        $value = "~{$avgGapDays} day average gap across " . count($dates) . ' on-page post dates (most recent: '
            . $dates[0]->format('Y-m-d') . ', oldest: ' . $dates[count($dates) - 1]->format('Y-m-d') . ')';

        if ($avgGapDays <= self::BLOG_FREQUENCY_GOOD_MAX_AVG_DAYS) {
            return new ContentCheckResult(
                metric: 'Blog Frequency',
                value: $value,
                status: ContentCheckStatus::GOOD,
                recommendation: null,
            );
        }

        if ($avgGapDays <= self::BLOG_FREQUENCY_WARNING_MAX_AVG_DAYS) {
            return new ContentCheckResult(
                metric: 'Blog Frequency',
                value: $value,
                status: ContentCheckStatus::WARNING,
                recommendation: 'Publishing cadence looks moderate. Posting more consistently tends to improve '
                    . 'both returning-reader engagement and search visibility.',
            );
        }

        return new ContentCheckResult(
            metric: 'Blog Frequency',
            value: $value,
            status: ContentCheckStatus::CRITICAL,
            recommendation: 'Posts on this page are spaced far apart. A more consistent publishing schedule '
                . 'generally helps both audience retention and search performance.',
        );
    }

    /**
     * Averages points across every check (good/warning/critical all map
     * to a point value), then rounds to the nearest whole score. Mirrors
     * UiUxAnalyzer::score()'s (and, in turn, AccessibilityAnalyzer's and
     * SecurityAnalyzer's) points-averaging approach.
     *
     * @param array<string, ContentCheckResult> $checks
     */
    private function score(array $checks): int
    {
        $points = array_map(
            fn (ContentCheckResult $check): int => $this->pointsFor($check->status),
            $checks,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(ContentCheckStatus $status): int
    {
        return match ($status) {
            ContentCheckStatus::GOOD => $this->pointsGood,
            ContentCheckStatus::WARNING => $this->pointsWarning,
            ContentCheckStatus::CRITICAL => $this->pointsCritical,
        };
    }

    private function grade(int $score): string
    {
        return match (true) {
            $score >= $this->gradeAThreshold => 'A',
            $score >= $this->gradeBThreshold => 'B',
            $score >= $this->gradeCThreshold => 'C',
            $score >= $this->gradeDThreshold => 'D',
            default => 'F',
        };
    }

    /**
     * @param array<string, ContentCheckResult> $checks
     */
    private function summary(array $checks, int $score, string $grade): string
    {
        $counts = ['good' => 0, 'warning' => 0, 'critical' => 0];

        foreach ($checks as $check) {
            $counts[$check->status->value]++;
        }

        return sprintf(
            'Content score %d/100 (grade %s), based on %d check(s): %d good, %d warning(s), %d critical.',
            $score,
            $grade,
            count($checks),
            $counts['good'],
            $counts['warning'],
            $counts['critical'],
        );
    }
}
