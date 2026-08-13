<?php

declare(strict_types=1);

namespace App\Audit\UiUx;

use App\Audit\Enums\UiUxElementStatus;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\UiUx\DTO\UiUxAuditResult;
use App\Audit\UiUx\DTO\UiUxElementResult;
use App\Audit\UiUx\DTO\UiUxResult;

/**
 * Runs a fixed set of basic UI/UX checks against a single fetched page via
 * analyze(): presence and content of the primary navigation landmark, a
 * hero section near the top of the page, call-to-action buttons/links,
 * form usability, whitespace/spacing, color palette consistency,
 * typography consistency, button component quality, footer completeness,
 * trust signals, testimonials, reviews, and mobile-readiness — then rolls
 * the results up into an overall score, letter grade, summary, and a
 * prioritized list of improvement suggestions. Mirrors
 * AccessibilityAnalyzer's (and, in turn, SecurityAnalyzer's)
 * score()/grade()/summary() points-averaging approach. analyzeAll() runs
 * the same checklist across several fetched pages at once and wraps the
 * per-page results in a UiUxAuditResult, mirroring
 * SecurityAnalyzer::analyzeAll() and AccessibilityAnalyzer::analyzeAll().
 *
 * Deliberately scoped to only these thirteen elements.
 *
 * Static HTML analysis cannot see layout, visual hierarchy, or
 * "above the fold" positioning — those depend on external CSS and
 * viewport size — so every check here relies on markup-level proxies:
 * landmark elements, common naming conventions (hero/jumbotron/banner,
 * cta/btn/button class or id fragments), link/button text, form field
 * counts, and inline `style="..."` declarations (spacing, color, and
 * typography mostly live in external stylesheets a static fetch never
 * sees, so those three checks report a Warning — not a false Pass —
 * when nothing inline was found to check). Each check documents its own
 * proxy and limitation.
 *
 * The Part 3 checks (Button, Footer, Trust Signals, Testimonials,
 * Reviews) follow the same proxy-based approach: Button evaluates the
 * quality of buttons that already exist (missing `type` attribute, overly
 * long text, disabled state without an explanation) rather than whether
 * a call-to-action exists at all — that remains CTA's job. Trust
 * Signals, Testimonials, and Reviews are not mandatory on every page, so
 * their absence is reported as a Warning rather than a Fail, consistent
 * with how Forms treats "no forms present" as a Pass.
 *
 * The Part 4 check (Mobile Design) is limited the same way: it can only
 * see a `<meta name="viewport">` tag and inline `width` styles, not the
 * actual rendered layout at any viewport size, so it reports the
 * strongest signal it can see (a missing viewport tag entirely) as a
 * Fail and softer signals (a misconfigured viewport tag, or elements
 * with a large fixed inline pixel width that risk horizontal overflow
 * on narrow screens) as a Warning.
 *
 * Takes a FetchResult rather than a CrawledPage for the same reason
 * AccessibilityAnalyzer does: these checks need to walk the raw DOM
 * (landmark elements, class/id attributes, nested images and links,
 * inline style attributes) that no existing parsed DTO captures. The
 * Reviews check additionally reads FetchResult::$schema to recognize
 * Review/AggregateRating JSON-LD structured data alongside DOM markers.
 */
final class UiUxAnalyzer
{
    /**
     * Case-insensitive substrings checked against class/id attributes to
     * recognize a hero section when no other top-of-page candidate
     * (a <header>, then a first <h1>) is found first.
     *
     * @var array<int, string>
     */
    private const array HERO_MARKERS = ['hero', 'jumbotron', 'banner'];

    /**
     * Case-insensitive substrings checked against an <a> element's
     * class/id attributes to recognize it as a call-to-action. Every
     * <button> element is treated as a CTA candidate outright.
     *
     * @var array<int, string>
     */
    private const array CTA_MARKERS = ['cta', 'btn', 'button'];

    /**
     * Exact (whitespace-normalized, lowercased) link/button text
     * considered too vague to convey where it leads or what it does —
     * a common UX anti-pattern for scanning and screen-reader users
     * alike. An empty accessible name is treated the same way.
     *
     * @var array<int, string>
     */
    private const array GENERIC_CTA_TEXT = [
        'click here', 'here', 'link', 'read more', 'more', 'learn more', 'submit',
    ];

    /**
     * A form with more input/select/textarea fields than this is flagged
     * as long — long single-page forms are a well-known conversion-rate
     * risk, independent of any specific field's own validity.
     */
    private const int FORM_MAX_FIELDS = 10;

    /**
     * More distinct inline colors than this across the page is flagged
     * as a possibly inconsistent palette.
     */
    private const int COLOR_DISTINCT_THRESHOLD = 8;

    /**
     * More distinct inline font-family declarations than this is flagged
     * as a possibly inconsistent typographic system.
     */
    private const int FONT_FAMILY_DISTINCT_THRESHOLD = 3;

    /**
     * A button's accessible text longer than this many words is flagged
     * as hurting scannability — buttons are meant to be scanned quickly,
     * not read like a sentence.
     */
    private const int BUTTON_MAX_TEXT_WORDS = 6;

    /**
     * Case-insensitive substrings checked against link href/text to
     * recognize a trust-related legal link (privacy policy, terms of
     * service, money-back guarantee, and similar).
     *
     * @var array<int, string>
     */
    private const array TRUST_LINK_MARKERS = ['privacy', 'terms', 'refund', 'guarantee'];

    /**
     * Case-insensitive substrings checked against image alt text and
     * surrounding class/id attributes to recognize a security or
     * payment trust badge (SSL seals, "as seen in" logos, payment
     * provider marks, and similar).
     *
     * @var array<int, string>
     */
    private const array TRUST_BADGE_MARKERS = [
        'ssl', 'secure', 'trust', 'verified', 'badge', 'norton', 'mcafee', 'bbb', 'certified',
    ];

    /**
     * Case-insensitive substrings checked against class/id attributes to
     * recognize a testimonial container.
     *
     * @var array<int, string>
     */
    private const array TESTIMONIAL_MARKERS = ['testimonial', 'review-quote'];

    /**
     * Case-insensitive substrings checked against a descendant element's
     * class/id (or the presence of a  tag) to recognize an
     * attributed author for a testimonial.
     *
     * @var array<int, string>
     */
    private const array TESTIMONIAL_ATTRIBUTION_MARKERS = ['author', 'name', 'attribution', 'byline'];

    /**
     * Case-insensitive substrings checked against class/id attributes,
     * and itemprop values, to recognize customer review markup.
     *
     * @var array<int, string>
     */
    private const array REVIEW_MARKERS = ['review', 'rating'];

    /**
     * Case-insensitive substrings checked against class/id/itemprop to
     * recognize a visible numeric or star rating indicator.
     *
     * @var array<int, string>
     */
    private const array RATING_INDICATOR_MARKERS = ['star', 'rating'];

    /**
     * Case-insensitive schema.org "@type" values that indicate customer
     * review structured data.
     *
     * @var array<int, string>
     */
    private const array REVIEW_SCHEMA_TYPES = ['review', 'aggregaterating'];

    /**
     * Regex matched against a footer's text content to recognize a
     * copyright notice or reference to legal terms.
     */
    private const string FOOTER_LEGAL_PATTERN = '/copyright|©|all rights reserved|privacy policy|terms of/i';

    /**
     * An inline pixel width above this on any element is flagged as a
     * horizontal-overflow risk on narrow (mobile) viewports.
     */
    private const int MOBILE_FIXED_WIDTH_THRESHOLD_PX = 600;

    /**
     * Points-averaging and letter-grade thresholds for the overall
     * score. Constructor-injected so scoring can be tuned without
     * editing check logic. Mirrors AccessibilityAnalyzer's (and, in
     * turn, SecurityAnalyzer's) constructor defaults exactly.
     */
    public function __construct(
        private readonly int $pointsPass = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsFail = 0,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {}

    public function analyze(FetchResult $result): UiUxResult
    {
        $html = (string) $result->html;
        $pageUrl = $this->targetUrl($result);

        if (trim($html) === '') {
            return $this->emptyResult($result, $pageUrl);
        }

        $xpath = new \DOMXPath($this->loadDocument($html));

        $elements = [
            'navigation' => $this->checkNavigation($xpath, $pageUrl),
            'hero_section' => $this->checkHeroSection($xpath, $pageUrl),
            'cta' => $this->checkCta($xpath, $pageUrl),
            'forms' => $this->checkForms($xpath, $pageUrl),
            'spacing' => $this->checkSpacing($xpath, $pageUrl),
            'color' => $this->checkColor($xpath, $pageUrl),
            'typography' => $this->checkTypography($xpath, $pageUrl),
            'button' => $this->checkButton($xpath, $pageUrl),
            'footer' => $this->checkFooter($xpath, $pageUrl),
            'trust_signals' => $this->checkTrustSignals($xpath, $pageUrl),
            'testimonials' => $this->checkTestimonials($xpath, $pageUrl),
            'reviews' => $this->checkReviews($xpath, $result->schema, $pageUrl),
            'mobile_design' => $this->checkMobileDesign($xpath, $pageUrl),
        ];

        $score = $this->score($elements);
        $grade = $this->grade($score);

        return new UiUxResult(
            url: $result->url,
            elements: $elements,
            score: $score,
            grade: $grade,
            summary: $this->summary($elements, $score, $grade),
            prioritizedSuggestions: $this->prioritizedSuggestions($elements),
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    /**
     * Runs analyze() over several already-fetched pages at once (see
     * AnalyzeChunkJob, which shares the fetched page set with
     * SecurityAnalyzer::analyzeAll(), AccessibilityAnalyzer::analyzeAll(),
     * and ContentAnalyzer::analyzeAll() via a common helper) and wraps
     * the per-page results in a UiUxAuditResult. Pages whose fetch itself
     * failed are reported in failedPageUrls rather than analyzed, since
     * there's no response to check.
     *
     * @param  array<string, FetchResult>  $fetchResults  keyed by page URL
     */
    public function analyzeAll(array $fetchResults, string $startUrl): UiUxAuditResult
    {
        $pageResults = [];
        $failedPageUrls = [];

        foreach ($fetchResults as $url => $fetchResult) {
            if (! $fetchResult->success) {
                $failedPageUrls[] = $url;

                continue;
            }

            $pageResults[$url] = $this->analyze($fetchResult);
        }

        $averageScore = $pageResults !== []
            ? (int) round(
                array_sum(array_map(static fn (UiUxResult $r): int => $r->score, $pageResults))
                    / count($pageResults)
            )
            : 0;

        return new UiUxAuditResult(
            startUrl: $startUrl,
            pages: $pageResults,
            failedPageUrls: $failedPageUrls,
            pagesAnalyzed: count($pageResults),
            pagesFailed: count($failedPageUrls),
            averageScore: $averageScore,
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    private function targetUrl(FetchResult $result): string
    {
        return $result->finalUrl ?? $result->url;
    }

    private function loadDocument(string $html): \DOMDocument
    {
        $previous = libxml_use_internal_errors(true);

        $dom = new \DOMDocument;
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom;
    }

    private function emptyResult(FetchResult $result, string $pageUrl): UiUxResult
    {
        $labels = [
            'navigation' => 'Navigation',
            'hero_section' => 'Hero Section',
            'cta' => 'CTA',
            'forms' => 'Forms',
            'spacing' => 'Spacing',
            'color' => 'Color',
            'typography' => 'Typography',
            'button' => 'Button',
            'footer' => 'Footer',
            'trust_signals' => 'Trust Signals',
            'testimonials' => 'Testimonials',
            'reviews' => 'Reviews',
            'mobile_design' => 'Mobile Design',
        ];

        $elements = [];

        foreach ($labels as $key => $label) {
            $elements[$key] = new UiUxElementResult(
                element: $label,
                status: UiUxElementStatus::WARNING,
                issues: ['no HTML content to analyze'],
                suggestions: ['The page returned no HTML to inspect — re-fetch the page and re-run this analysis.'],
                pageUrl: $pageUrl,
            );
        }

        $score = $this->score($elements);
        $grade = $this->grade($score);

        return new UiUxResult(
            url: $result->url,
            elements: $elements,
            score: $score,
            grade: $grade,
            summary: $this->summary($elements, $score, $grade),
            prioritizedSuggestions: $this->prioritizedSuggestions($elements),
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    private function checkNavigation(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $navs = iterator_to_array($xpath->query('//nav | //*[@role="navigation"]') ?: []);

        if ($navs === []) {
            return new UiUxElementResult(
                element: 'Navigation',
                status: UiUxElementStatus::FAIL,
                issues: ['no navigation landmark found (<nav> element or role="navigation")'],
                suggestions: [
                    'Add a <nav> element (or role="navigation") wrapping the site\'s primary links so users '
                        .'and assistive technology can find it quickly.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [
                    $this->element(null, 'No navigation landmark found (<nav> element or role="navigation")'),
                ],
            );
        }

        $totalLinks = 0;

        foreach ($navs as $nav) {
            /** @var \DOMElement $nav */
            $totalLinks += $xpath->query('.//a[@href]', $nav)?->length ?? 0;
        }

        /** @var \DOMElement $firstNav */
        $firstNav = $navs[0];
        $firstNavDomPath = $this->buildDomPath($firstNav);

        if ($totalLinks === 0) {
            return new UiUxElementResult(
                element: 'Navigation',
                status: UiUxElementStatus::FAIL,
                issues: ['navigation landmark found but contains no links'],
                suggestions: ['Add links to the site\'s primary pages inside the navigation landmark.'],
                pageUrl: $pageUrl,
                affectedElements: [$this->element($firstNavDomPath, 'Navigation landmark contains no links')],
            );
        }

        if ($totalLinks === 1) {
            return new UiUxElementResult(
                element: 'Navigation',
                status: UiUxElementStatus::WARNING,
                issues: ['navigation contains only 1 link'],
                suggestions: [
                    'Add links to the site\'s other primary sections so users can reach the whole site from here.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [$this->element($firstNavDomPath, 'Navigation contains only 1 link')],
            );
        }

        return new UiUxElementResult(
            element: 'Navigation',
            status: UiUxElementStatus::PASS,
            issues: [],
            suggestions: [],
            pageUrl: $pageUrl,
        );
    }

    private function checkHeroSection(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $hero = $this->findHeroCandidate($xpath);

        if ($hero === null) {
            return new UiUxElementResult(
                element: 'Hero Section',
                status: UiUxElementStatus::FAIL,
                issues: ['no hero section, page <header>, or top-of-page <h1> found'],
                suggestions: [
                    'Add an introductory hero section near the top of the page with a clear heading and a '
                        .'call to action.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [
                    $this->element(null, 'No hero section, page <header>, or top-of-page <h1> found'),
                ],
            );
        }

        $hasHeading = ($xpath->query('.//h1 | .//h2 | .//h3', $hero)?->length ?? 0) > 0;
        $hasImage = ($xpath->query('.//img', $hero)?->length ?? 0) > 0;
        $hasCta = $this->findCtaElements($xpath, $hero) !== [];

        $issues = [];
        $suggestions = [];
        $affectedElements = [];
        $heroDomPath = $this->buildDomPath($hero);

        if (! $hasHeading) {
            $issues[] = 'hero section has no heading';
            $suggestions[] = 'Add a clear heading (h1-h3) summarizing the page\'s value proposition.';
            $affectedElements[] = $this->element($heroDomPath, 'Hero section has no heading');
        }

        if (! $hasImage && ! $hasCta) {
            $issues[] = 'hero section has no supporting image and no call to action';
            $suggestions[] = 'Add a supporting image and/or a call-to-action button so the hero section '
                .'prompts a next step.';
            $affectedElements[] = $this->element(
                $heroDomPath,
                'Hero section has no supporting image and no call to action',
            );
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Hero Section',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Hero Section',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    /**
     * Finds a hero-like container using, in order: an element whose
     * class or id contains a hero marker (hero/jumbotron/banner), then
     * the page's first <header>, then the page's first <h1> — each a
     * common top-of-page introductory pattern static analysis can
     * detect without seeing layout or "above the fold" positioning.
     */
    private function findHeroCandidate(\DOMXPath $xpath): ?\DOMElement
    {
        foreach ($xpath->query('//*[@class or @id]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower($node->getAttribute('class').' '.$node->getAttribute('id'));

            foreach (self::HERO_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return $node;
                }
            }
        }

        $header = $xpath->query('//header')?->item(0);

        if ($header instanceof \DOMElement) {
            return $header;
        }

        $firstHeading = $xpath->query('//h1')?->item(0);

        return $firstHeading instanceof \DOMElement ? $firstHeading : null;
    }

    private function checkCta(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $ctas = $this->findCtaElements($xpath);

        if ($ctas === []) {
            return new UiUxElementResult(
                element: 'CTA',
                status: UiUxElementStatus::FAIL,
                issues: ['no call-to-action buttons or links found'],
                suggestions: [
                    'Add a prominent call-to-action button or link with action-oriented text (e.g. '
                        .'"Get Started", "Sign Up", "Contact Us").',
                ],
                pageUrl: $pageUrl,
                affectedElements: [$this->element(null, 'No call-to-action buttons or links found')],
            );
        }

        $generic = array_values(array_filter(
            $ctas,
            fn (\DOMElement $cta): bool => $this->hasGenericCtaText($cta),
        ));

        if ($generic === []) {
            return new UiUxElementResult(
                element: 'CTA',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        $count = count($generic);

        return new UiUxElementResult(
            element: 'CTA',
            status: UiUxElementStatus::WARNING,
            issues: [
                $count === 1
                    ? '1 call-to-action uses generic, non-descriptive text'
                    : "{$count} calls-to-action use generic, non-descriptive text",
            ],
            suggestions: [
                'Replace generic text like "click here" or "read more" with specific, action-oriented text '
                    .'that describes what happens next.',
            ],
            pageUrl: $pageUrl,
            affectedElements: array_map(
                fn (\DOMElement $cta): array => $this->element(
                    $this->buildDomPath($cta),
                    'Generic, non-descriptive call-to-action text',
                ),
                $generic,
            ),
        );
    }

    /**
     * Finds elements that behave like a call to action: every <button>
     * element, plus <a href> elements whose class or id contains a CTA
     * marker (cta/btn/button). Optionally scoped to a context node (e.g.
     * within the hero section) rather than the whole document.
     *
     * @return array<int, \DOMElement>
     */
    private function findCtaElements(\DOMXPath $xpath, ?\DOMElement $context = null): array
    {
        $prefix = $context !== null ? '.' : '';
        $query = "{$prefix}//button | {$prefix}//a[@href]";

        $found = [];

        foreach ($xpath->query($query, $context) ?: [] as $node) {
            /** @var \DOMElement $node */
            if ($node->nodeName === 'button' || $this->looksLikeCta($node)) {
                $found[] = $node;
            }
        }

        return $found;
    }

    private function looksLikeCta(\DOMElement $node): bool
    {
        $haystack = strtolower($node->getAttribute('class').' '.$node->getAttribute('id'));

        foreach (self::CTA_MARKERS as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    private function hasGenericCtaText(\DOMElement $cta): bool
    {
        $text = strtolower(trim((string) preg_replace('/\s+/u', ' ', $cta->textContent)));

        if ($text === '') {
            return true;
        }

        return in_array($text, self::GENERIC_CTA_TEXT, true);
    }

    private function checkForms(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $forms = iterator_to_array($xpath->query('//form') ?: []);

        if ($forms === []) {
            return new UiUxElementResult(
                element: 'Forms',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        $missingSubmit = [];
        $longForms = [];

        foreach ($forms as $form) {
            /** @var \DOMElement $form */
            $submitCount = $xpath->query(
                './/button[not(@type) or @type="submit"] | .//input[@type="submit"]',
                $form,
            )?->length ?? 0;

            if ($submitCount === 0) {
                $missingSubmit[] = $form;
            }

            $fieldCount = $xpath->query(
                './/input[not(@type="hidden") and not(@type="submit") and not(@type="button")'
                    .' and not(@type="reset")] | .//select | .//textarea',
                $form,
            )?->length ?? 0;

            if ($fieldCount > self::FORM_MAX_FIELDS) {
                $longForms[] = $form;
            }
        }

        $issues = [];
        $suggestions = [];
        $affectedElements = [];

        if ($missingSubmit !== []) {
            $count = count($missingSubmit);
            $issues[] = $count === 1
                ? '1 form has no visible submit control'
                : "{$count} forms have no visible submit control";
            $suggestions[] = 'Give every form a visible submit control (a <button> or '
                .'<input type="submit">) so users can tell how to complete it.';

            foreach ($missingSubmit as $form) {
                $affectedElements[] = $this->element($this->buildDomPath($form), 'Form has no visible submit control');
            }
        }

        if ($longForms !== []) {
            $count = count($longForms);
            $max = self::FORM_MAX_FIELDS;
            $issues[] = $count === 1
                ? "1 form has more than {$max} fields"
                : "{$count} forms have more than {$max} fields";
            $suggestions[] = 'Break long forms into shorter steps or sections — long single-page forms tend '
                .'to hurt completion rates.';

            foreach ($longForms as $form) {
                $affectedElements[] = $this->element($this->buildDomPath($form), "Form has more than {$max} fields");
            }
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Forms',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Forms',
            status: $missingSubmit !== [] ? UiUxElementStatus::FAIL : UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    private function checkSpacing(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $declared = $this->countInlineSpacingDeclarations($xpath);

        if ($declared === 0) {
            return new UiUxElementResult(
                element: 'Spacing',
                status: UiUxElementStatus::WARNING,
                issues: ['no inline margin/padding styles found to check'],
                suggestions: [
                    'Spacing mostly depends on external CSS, which static HTML analysis cannot read. Verify '
                        .'with an automated tool or manually that content has adequate breathing room.',
                ],
                pageUrl: $pageUrl,
            );
        }

        $cramped = $this->collectZeroSpacingElements($xpath);

        if ($cramped === []) {
            return new UiUxElementResult(
                element: 'Spacing',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        $count = count($cramped);
        $sample = implode(', ', array_slice(
            array_map(fn (\DOMElement $e): string => $this->describeElement($e), $cramped),
            0,
            3,
        ));

        return new UiUxElementResult(
            element: 'Spacing',
            status: UiUxElementStatus::WARNING,
            issues: [
                $count === 1
                    ? "1 inline-styled element has both margin and padding set to zero: {$sample}"
                    : "{$count} inline-styled elements have both margin and padding set to zero, "
                        ."including: {$sample}",
            ],
            suggestions: [
                'Add adequate margin/padding around content blocks so the layout has visual breathing room '
                    .'instead of feeling cramped. Note: only elements with inline margin/padding styles were '
                    .'checked — spacing set via external CSS is not visible to static analysis.',
            ],
            pageUrl: $pageUrl,
            affectedElements: array_map(
                fn (\DOMElement $e): array => $this->element(
                    $this->buildDomPath($e),
                    'Margin and padding both set to zero',
                ),
                $cramped,
            ),
        );
    }

    /**
     * Counts elements whose inline style declares margin and/or padding
     * at all, regardless of value — the denominator for deciding whether
     * this check has anything to evaluate.
     */
    private function countInlineSpacingDeclarations(\DOMXPath $xpath): int
    {
        $count = 0;

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));

            if ($this->extractCssValue($style, 'margin') !== null
                || $this->extractCssValue($style, 'padding') !== null) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array<int, \DOMElement>
     */
    private function collectZeroSpacingElements(\DOMXPath $xpath): array
    {
        $found = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));
            $margin = $this->extractCssValue($style, 'margin');
            $padding = $this->extractCssValue($style, 'padding');

            if ($margin !== null && $padding !== null
                && $this->isAllZeroCssValue($margin) && $this->isAllZeroCssValue($padding)) {
                $found[] = $node;
            }
        }

        return $found;
    }

    /**
     * Whether every whitespace-separated token in a CSS shorthand value
     * (e.g. "0 0 0 0", "0px") is zero.
     */
    private function isAllZeroCssValue(string $value): bool
    {
        $tokens = preg_split('/\s+/', trim($value)) ?: [];

        if ($tokens === []) {
            return false;
        }

        foreach ($tokens as $token) {
            if (preg_match('/^0(px|em|rem|%)?$/', $token) !== 1) {
                return false;
            }
        }

        return true;
    }

    private function checkColor(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $colors = $this->collectDistinctInlineColors($xpath);

        if ($colors === []) {
            return new UiUxElementResult(
                element: 'Color',
                status: UiUxElementStatus::WARNING,
                issues: ['no inline color/background-color styles found to check'],
                suggestions: [
                    'Color palette mostly depends on external CSS, which static HTML analysis cannot read. '
                        .'Verify with an automated tool or manually that the page uses a small, deliberate '
                        .'color palette.',
                ],
                pageUrl: $pageUrl,
            );
        }

        $count = count($colors);

        if ($count > self::COLOR_DISTINCT_THRESHOLD) {
            $affectedElements = [];

            foreach ($colors as $color => $element) {
                $affectedElements[] = $this->element($this->buildDomPath($element), "Uses color \"{$color}\"");
            }

            return new UiUxElementResult(
                element: 'Color',
                status: UiUxElementStatus::WARNING,
                issues: ["{$count} distinct inline colors found, which may signal an inconsistent color palette"],
                suggestions: [
                    'Standardize on a small, deliberate color palette (e.g. via CSS custom properties or a '
                        .'design system) instead of one-off inline colors. Note: only inline color/'
                        .'background-color styles were checked — colors set via external CSS are not visible '
                        .'to static analysis.',
                ],
                pageUrl: $pageUrl,
                affectedElements: $affectedElements,
            );
        }

        return new UiUxElementResult(
            element: 'Color',
            status: UiUxElementStatus::PASS,
            issues: [],
            suggestions: [],
            pageUrl: $pageUrl,
        );
    }

    /**
     * Collects every distinct inline `color` and background
     * (`background-color` or the shorthand `background`) value used on
     * the page, together with the first element found using each value
     * (so the Color check can point at a concrete example when the
     * distinct-color count is flagged).
     *
     * @return array<string, \DOMElement> color/background value => first element using it
     */
    private function collectDistinctInlineColors(\DOMXPath $xpath): array
    {
        $colors = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));
            $color = $this->extractCssValue($style, 'color');
            $background = $this->extractCssValue($style, 'background-color')
                ?? $this->extractCssValue($style, 'background');

            if ($color !== null && ! isset($colors[$color])) {
                $colors[$color] = $node;
            }

            if ($background !== null && ! isset($colors[$background])) {
                $colors[$background] = $node;
            }
        }

        return $colors;
    }

    private function checkTypography(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $fontFamilies = $this->collectDistinctInlineFontFamilies($xpath);
        $legacyNodes = iterator_to_array($xpath->query('//font[@face]') ?: []);
        $legacyCount = count($legacyNodes);

        if ($fontFamilies === [] && $legacyCount === 0) {
            return new UiUxElementResult(
                element: 'Typography',
                status: UiUxElementStatus::WARNING,
                issues: ['no inline font-family styles or legacy <font face> tags found to check'],
                suggestions: [
                    'Typography mostly depends on external CSS, which static HTML analysis cannot read. '
                        .'Verify with an automated tool or manually that the page uses a small, consistent '
                        .'set of font families.',
                ],
                pageUrl: $pageUrl,
            );
        }

        $issues = [];
        $suggestions = [];
        $affectedElements = [];
        $count = count($fontFamilies);

        if ($count > self::FONT_FAMILY_DISTINCT_THRESHOLD) {
            $issues[] = "{$count} distinct inline font-family declarations found, which may create visual "
                .'inconsistency';
            $suggestions[] = 'Limit the page to 1-2 font families for a consistent, professional look.';

            foreach ($fontFamilies as $font => $element) {
                $affectedElements[] = $this->element(
                    $this->buildDomPath($element),
                    "Uses font-family \"{$font}\"",
                );
            }
        }

        if ($legacyCount > 0) {
            $issues[] = "{$legacyCount} legacy <font> tag(s) found";
            $suggestions[] = 'Replace legacy <font> tags with CSS font-family declarations on semantic elements.';

            foreach ($legacyNodes as $node) {
                /** @var \DOMElement $node */
                $affectedElements[] = $this->element($this->buildDomPath($node), 'Legacy <font> tag');
            }
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Typography',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Typography',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    /**
     * @return array<string, \DOMElement> font-family value => first element using it
     */
    private function collectDistinctInlineFontFamilies(\DOMXPath $xpath): array
    {
        $fonts = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));
            $value = $this->extractCssValue($style, 'font-family');

            if ($value !== null && ! isset($fonts[$value])) {
                $fonts[$value] = $node;
            }
        }

        return $fonts;
    }

    private function checkButton(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $buttons = $this->findButtonElements($xpath);

        if ($buttons === []) {
            return new UiUxElementResult(
                element: 'Button',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        $missingType = [];
        $longText = [];
        $unexplainedDisabled = [];

        foreach ($buttons as $button) {
            /** @var \DOMElement $button */
            if ($button->nodeName === 'button' && ! $button->hasAttribute('type')) {
                $missingType[] = $button;
            }

            $text = $this->buttonAccessibleText($button);
            $wordCount = $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []);

            if ($wordCount > self::BUTTON_MAX_TEXT_WORDS) {
                $longText[] = $button;
            }

            if ($this->isDisabledWithoutExplanation($button)) {
                $unexplainedDisabled[] = $button;
            }
        }

        $issues = [];
        $suggestions = [];
        $affectedElements = [];

        if ($missingType !== []) {
            $count = count($missingType);
            $issues[] = $count === 1
                ? '1 <button> element has no type attribute'
                : "{$count} <button> elements have no type attribute";
            $suggestions[] = 'Add an explicit type="button" or type="submit" to every <button> — inside a '
                .'form, an untyped button defaults to submitting it, which is a common source of accidental '
                .'form submission.';

            foreach ($missingType as $button) {
                $affectedElements[] = $this->element($this->buildDomPath($button), 'No type attribute');
            }
        }

        if ($longText !== []) {
            $count = count($longText);
            $max = self::BUTTON_MAX_TEXT_WORDS;
            $issues[] = $count === 1
                ? "1 button's text is longer than {$max} words"
                : "{$count} buttons have text longer than {$max} words";
            $suggestions[] = 'Keep button text short and action-oriented so it can be scanned at a glance.';

            foreach ($longText as $button) {
                $affectedElements[] = $this->element($this->buildDomPath($button), "Text longer than {$max} words");
            }
        }

        if ($unexplainedDisabled !== []) {
            $count = count($unexplainedDisabled);
            $issues[] = $count === 1
                ? '1 disabled button has no title or aria-label explaining why it is disabled'
                : "{$count} disabled buttons have no title or aria-label explaining why they are disabled";
            $suggestions[] = 'Add a title or aria-label to disabled buttons so users understand what to do '
                .'to enable them, instead of leaving them to guess.';

            foreach ($unexplainedDisabled as $button) {
                $affectedElements[] = $this->element(
                    $this->buildDomPath($button),
                    'Disabled with no title/aria-label explaining why',
                );
            }
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Button',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Button',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    /**
     * Finds every button-like control on the page: native <button>
     * elements and <input> elements typed as submit/button/reset. This
     * check evaluates the quality of buttons that already exist —
     * whether any call-to-action exists at all is CTA's responsibility.
     *
     * @return array<int, \DOMElement>
     */
    private function findButtonElements(\DOMXPath $xpath): array
    {
        $query = '//button | //input[@type="submit" or @type="button" or @type="reset"]';

        return iterator_to_array($xpath->query($query) ?: []);
    }

    /**
     * The accessible text of a button-like element: its text content for
     * a <button>, or its value/aria-label attribute for an <input>.
     */
    private function buttonAccessibleText(\DOMElement $button): string
    {
        if ($button->nodeName === 'button') {
            $text = trim((string) preg_replace('/\s+/u', ' ', $button->textContent));

            return $text !== '' ? $text : trim($button->getAttribute('aria-label'));
        }

        $value = trim($button->getAttribute('value'));

        return $value !== '' ? $value : trim($button->getAttribute('aria-label'));
    }

    private function isDisabledWithoutExplanation(\DOMElement $button): bool
    {
        if (! $button->hasAttribute('disabled')) {
            return false;
        }

        return trim($button->getAttribute('title')) === ''
            && trim($button->getAttribute('aria-label')) === ''
            && trim($button->getAttribute('aria-describedby')) === '';
    }

    private function checkFooter(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $footer = $xpath->query('//footer | //*[@role="contentinfo"]')?->item(0);

        if (! $footer instanceof \DOMElement) {
            return new UiUxElementResult(
                element: 'Footer',
                status: UiUxElementStatus::FAIL,
                issues: ['no footer landmark found (<footer> element or role="contentinfo")'],
                suggestions: [
                    'Add a <footer> element (or role="contentinfo") containing site links and copyright/legal '
                        .'information.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [
                    $this->element(null, 'No footer landmark found (<footer> element or role="contentinfo")'),
                ],
            );
        }

        $footerDomPath = $this->buildDomPath($footer);
        $linkCount = $xpath->query('.//a[@href]', $footer)?->length ?? 0;
        $hasLegalText = preg_match(self::FOOTER_LEGAL_PATTERN, $footer->textContent) === 1;

        $issues = [];
        $suggestions = [];
        $affectedElements = [];

        if ($linkCount === 0) {
            $issues[] = 'footer contains no links';
            $suggestions[] = 'Add links to key pages (e.g. About, Privacy Policy, Terms, Contact) inside the '
                .'footer.';
            $affectedElements[] = $this->element($footerDomPath, 'Footer contains no links');
        }

        if (! $hasLegalText) {
            $issues[] = 'footer has no copyright notice or reference to legal terms';
            $suggestions[] = 'Add a copyright notice (e.g. "© 2026 Company Name") and/or a link to legal '
                .'terms in the footer.';
            $affectedElements[] = $this->element(
                $footerDomPath,
                'Footer has no copyright notice or reference to legal terms',
            );
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Footer',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Footer',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    private function checkTrustSignals(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $hasLegalLink = $this->hasTrustLegalLink($xpath);
        $hasBadge = $this->hasTrustBadge($xpath);

        if ($hasLegalLink && $hasBadge) {
            return new UiUxElementResult(
                element: 'Trust Signals',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        if (! $hasLegalLink && ! $hasBadge) {
            return new UiUxElementResult(
                element: 'Trust Signals',
                status: UiUxElementStatus::WARNING,
                issues: ['no trust signals found (privacy/terms/guarantee links or security/payment badges)'],
                suggestions: [
                    'Add a link to a privacy policy or terms of service, and consider security or payment '
                        .'trust badges, to reassure visitors before they convert.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [
                    $this->element(
                        null,
                        'No trust signals found (privacy/terms/guarantee links or security/payment badges)',
                    ),
                ],
            );
        }

        $issues = [];
        $suggestions = [];
        $affectedElements = [];

        if (! $hasLegalLink) {
            $issues[] = 'no privacy policy, terms, refund, or guarantee link found';
            $suggestions[] = 'Add a visible link to a privacy policy or terms of service.';
            $affectedElements[] = $this->element(null, 'No privacy policy, terms, refund, or guarantee link found');
        }

        if (! $hasBadge) {
            $issues[] = 'no security or payment trust badge found';
            $suggestions[] = 'Consider adding a security seal or payment provider badge near checkout or '
                .'sign-up to build confidence.';
            $affectedElements[] = $this->element(null, 'No security or payment trust badge found');
        }

        return new UiUxElementResult(
            element: 'Trust Signals',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    private function hasTrustLegalLink(\DOMXPath $xpath): bool
    {
        foreach ($xpath->query('//a[@href]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower($node->getAttribute('href').' '.$node->textContent);

            foreach (self::TRUST_LINK_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasTrustBadge(\DOMXPath $xpath): bool
    {
        foreach ($xpath->query('//img') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower(
                $node->getAttribute('alt').' '.$node->getAttribute('class').' '.$node->getAttribute('id'),
            );

            foreach (self::TRUST_BADGE_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function checkTestimonials(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $testimonials = $this->findTestimonialElements($xpath);

        if ($testimonials === []) {
            return new UiUxElementResult(
                element: 'Testimonials',
                status: UiUxElementStatus::WARNING,
                issues: ['no testimonials found'],
                suggestions: [
                    'Add customer testimonials with a name or photo to build social proof — not every page '
                        .'needs them, but they tend to help on pages that ask for a conversion.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [$this->element(null, 'No testimonials found')],
            );
        }

        $unattributed = array_values(array_filter(
            $testimonials,
            fn (\DOMElement $testimonial): bool => ! $this->hasTestimonialAttribution($xpath, $testimonial),
        ));

        if ($unattributed === []) {
            return new UiUxElementResult(
                element: 'Testimonials',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        $count = count($unattributed);

        return new UiUxElementResult(
            element: 'Testimonials',
            status: UiUxElementStatus::WARNING,
            issues: [
                $count === 1
                    ? '1 testimonial has no visible author attribution'
                    : "{$count} testimonials have no visible author attribution",
            ],
            suggestions: [
                'Attribute each testimonial to a named person (and ideally a photo or company) — unattributed '
                    .'quotes are less persuasive and can look fabricated.',
            ],
            pageUrl: $pageUrl,
            affectedElements: array_map(
                fn (\DOMElement $t): array => $this->element(
                    $this->buildDomPath($t),
                    'No visible author attribution',
                ),
                $unattributed,
            ),
        );
    }

    /**
     * @return array<int, \DOMElement>
     */
    private function findTestimonialElements(\DOMXPath $xpath): array
    {
        $found = [];

        foreach ($xpath->query('//*[@class or @id]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower($node->getAttribute('class').' '.$node->getAttribute('id'));

            foreach (self::TESTIMONIAL_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    $found[] = $node;

                    continue 2;
                }
            }
        }

        foreach ($xpath->query('//blockquote[@cite]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $found[] = $node;
        }

        return $found;
    }

    private function hasTestimonialAttribution(\DOMXPath $xpath, \DOMElement $testimonial): bool
    {
        if ($testimonial->nodeName === 'blockquote' && $testimonial->hasAttribute('cite')) {
            return true;
        }

        if (($xpath->query('.//cite', $testimonial)?->length ?? 0) > 0) {
            return true;
        }

        foreach ($xpath->query('.//*[@class or @id]', $testimonial) ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower($node->getAttribute('class').' '.$node->getAttribute('id'));

            foreach (self::TESTIMONIAL_ATTRIBUTION_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<int, SchemaBlock>  $schema
     */
    private function checkReviews(\DOMXPath $xpath, array $schema, string $pageUrl): UiUxElementResult
    {
        $schemaTypes = $this->collectLowercaseSchemaTypes($schema);
        $hasReviewSchema = $this->arrayContainsAny($schemaTypes, self::REVIEW_SCHEMA_TYPES);
        $hasReviewMarkup = $this->hasReviewMarkup($xpath);

        if (! $hasReviewSchema && ! $hasReviewMarkup) {
            return new UiUxElementResult(
                element: 'Reviews',
                status: UiUxElementStatus::WARNING,
                issues: ['no customer reviews found'],
                suggestions: [
                    'Add customer reviews or ratings — not every page needs them, but they tend to build '
                        .'trust and improve conversion on product and pricing pages. Consider marking them '
                        .'up with Review/AggregateRating schema.org structured data for rich search results.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [$this->element(null, 'No customer reviews found')],
            );
        }

        $hasRatingIndicator = $this->arrayContainsAny($schemaTypes, ['aggregaterating'])
            || $this->hasRatingIndicatorMarkup($xpath);

        if (! $hasRatingIndicator) {
            return new UiUxElementResult(
                element: 'Reviews',
                status: UiUxElementStatus::WARNING,
                issues: ['reviews found but no visible star or numeric rating indicator'],
                suggestions: [
                    'Show a visible aggregate rating (e.g. star icons or "4.8 out of 5") alongside the '
                        .'reviews so visitors can gauge sentiment at a glance.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [
                    $this->element(null, 'Reviews found but no visible star or numeric rating indicator'),
                ],
            );
        }

        return new UiUxElementResult(
            element: 'Reviews',
            status: UiUxElementStatus::PASS,
            issues: [],
            suggestions: [],
            pageUrl: $pageUrl,
        );
    }

    /**
     * @param  array<int, SchemaBlock>  $schema
     * @return array<int, string>
     */
    private function collectLowercaseSchemaTypes(array $schema): array
    {
        $types = [];

        foreach ($schema as $block) {
            foreach ($block->types as $type) {
                $types[] = strtolower($type);
            }
        }

        return $types;
    }

    /**
     * @param  array<int, string>  $haystack
     * @param  array<int, string>  $needles
     */
    private function arrayContainsAny(array $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (in_array($needle, $haystack, true)) {
                return true;
            }
        }

        return false;
    }

    private function hasReviewMarkup(\DOMXPath $xpath): bool
    {
        foreach ($xpath->query('//*[@class or @id or @itemprop]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower(
                $node->getAttribute('class').' '.$node->getAttribute('id').' '.$node->getAttribute('itemprop'),
            );

            foreach (self::REVIEW_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasRatingIndicatorMarkup(\DOMXPath $xpath): bool
    {
        foreach ($xpath->query('//*[@class or @id or @itemprop]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $haystack = strtolower(
                $node->getAttribute('class').' '.$node->getAttribute('id').' '.$node->getAttribute('itemprop'),
            );

            foreach (self::RATING_INDICATOR_MARKERS as $marker) {
                if (str_contains($haystack, $marker)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function describeElement(\DOMElement $element): string
    {
        $identifier = $element->getAttribute('name') ?: $element->getAttribute('id');

        return $identifier !== '' ? "{$element->nodeName}[{$identifier}]" : $element->nodeName;
    }

    /**
     * Builds a readable CSS-selector-style path from the document root
     * down to the given element, identical in approach to
     * App\Audit\Fetching\HtmlParser::buildDomPath(),
     * AccessibilityAnalyzer::buildDomPath(), and
     * ContentAnalyzer::buildDomPath() — duplicated here for the same
     * reason those document: this class parses its own fresh
     * \DOMDocument from raw HTML, so there's no existing DOMElement
     * from HtmlParser's own parse to reuse.
     */
    private function buildDomPath(\DOMElement $element): string
    {
        $segments = [];
        $node = $element;

        while ($node instanceof \DOMElement) {
            $segments[] = $this->domPathSegment($node);
            $parent = $node->parentNode;
            $node = $parent instanceof \DOMElement ? $parent : null;
        }

        return implode(' > ', array_reverse($segments));
    }

    private function domPathSegment(\DOMElement $node): string
    {
        $tag = strtolower($node->nodeName);

        $id = trim($node->getAttribute('id'));

        if ($id !== '') {
            return sprintf('%s#%s', $tag, $id);
        }

        $class = trim($node->getAttribute('class'));

        if ($class !== '') {
            $firstClass = preg_split('/\s+/', $class, -1, PREG_SPLIT_NO_EMPTY)[0] ?? '';

            if ($firstClass !== '') {
                return sprintf('%s.%s', $tag, $firstClass);
            }
        }

        $position = 1;
        $sibling = $node->previousSibling;

        while ($sibling !== null) {
            if ($sibling instanceof \DOMElement) {
                $position++;
            }

            $sibling = $sibling->previousSibling;
        }

        return sprintf('%s:nth-child(%d)', $tag, $position);
    }

    /**
     * Builds one entry of a UiUxElementResult's affectedElements list.
     * Unlike SecurityAnalyzer::element()/AccessibilityAnalyzer::element()/
     * ContentAnalyzer::element(), there's no 'url' field here — UI/UX
     * findings are never tied to a resource URL, only a DOM location (or,
     * for page-level absences, no location at all).
     *
     * @return array{domPath: ?string, detail: ?string}
     */
    private function element(?string $domPath, ?string $detail): array
    {
        return ['domPath' => $domPath, 'detail' => $detail];
    }

    /**
     * Extracts a single CSS property's value out of an inline style
     * string. The negative lookbehind on the property name keeps a
     * search for "color" from matching inside "background-color".
     */
    private function extractCssValue(string $style, string $property): ?string
    {
        $pattern = '/(?<![a-z-])'.preg_quote($property, '/').'\s*:\s*([^;]+)/i';

        if (preg_match($pattern, $style, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function checkMobileDesign(\DOMXPath $xpath, string $pageUrl): UiUxElementResult
    {
        $viewport = $xpath->query('//meta[@name="viewport"]')?->item(0);

        if (! $viewport instanceof \DOMElement) {
            return new UiUxElementResult(
                element: 'Mobile Design',
                status: UiUxElementStatus::FAIL,
                issues: ['no <meta name="viewport"> tag found'],
                suggestions: [
                    'Add <meta name="viewport" content="width=device-width, initial-scale=1"> to <head> so '
                        .'the page scales correctly on mobile devices instead of rendering at desktop width.',
                ],
                pageUrl: $pageUrl,
                affectedElements: [$this->element(null, 'No <meta name="viewport"> tag found')],
            );
        }

        $issues = [];
        $suggestions = [];
        $affectedElements = [];
        $viewportDomPath = $this->buildDomPath($viewport);

        $content = strtolower($viewport->getAttribute('content'));

        if (! str_contains($content, 'width=device-width')) {
            $issues[] = 'viewport meta tag is present but missing "width=device-width"';
            $suggestions[] = 'Set the viewport meta tag\'s content to include "width=device-width, '
                .'initial-scale=1" so the page matches the device\'s screen width.';
            $affectedElements[] = $this->element(
                $viewportDomPath,
                'Viewport meta tag missing "width=device-width"',
            );
        }

        $wideElements = $this->collectFixedWidthOverflowElements($xpath);

        if ($wideElements !== []) {
            $count = count($wideElements);
            $max = self::MOBILE_FIXED_WIDTH_THRESHOLD_PX;
            $sample = implode(', ', array_slice(
                array_map(fn (\DOMElement $e): string => $this->describeElement($e), $wideElements),
                0,
                3,
            ));
            $issues[] = $count === 1
                ? "1 element has an inline fixed width over {$max}px, including: {$sample}"
                : "{$count} elements have an inline fixed width over {$max}px, including: {$sample}";
            $suggestions[] = 'Replace large fixed pixel widths with responsive units (%, max-width, rem) so '
                .'content does not force horizontal scrolling on narrow screens.';

            foreach ($wideElements as $element) {
                $affectedElements[] = $this->element(
                    $this->buildDomPath($element),
                    "Inline fixed width over {$max}px",
                );
            }
        }

        if ($issues === []) {
            return new UiUxElementResult(
                element: 'Mobile Design',
                status: UiUxElementStatus::PASS,
                issues: [],
                suggestions: [],
                pageUrl: $pageUrl,
            );
        }

        return new UiUxElementResult(
            element: 'Mobile Design',
            status: UiUxElementStatus::WARNING,
            issues: $issues,
            suggestions: $suggestions,
            pageUrl: $pageUrl,
            affectedElements: $affectedElements,
        );
    }

    /**
     * Elements whose inline `width` style is a fixed pixel value above
     * the overflow threshold — a common source of horizontal scrolling
     * on narrow (mobile) viewports. Percentage, viewport-unit, and
     * unit-less values are not flagged since only a fixed px width is a
     * reliable overflow signal from static markup alone.
     *
     * @return array<int, \DOMElement>
     */
    private function collectFixedWidthOverflowElements(\DOMXPath $xpath): array
    {
        $found = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));
            $width = $this->extractCssValue($style, 'width');

            if ($width === null) {
                continue;
            }

            if (preg_match('/^(\d+(?:\.\d+)?)px$/', trim($width), $matches) !== 1) {
                continue;
            }

            if ((float) $matches[1] > self::MOBILE_FIXED_WIDTH_THRESHOLD_PX) {
                $found[] = $node;
            }
        }

        return $found;
    }

    /**
     * Averages points across every element (pass/warning/fail all map to
     * a point value), then rounds to the nearest whole score. Mirrors
     * AccessibilityAnalyzer::score()'s (and, in turn, SecurityAnalyzer's)
     * points-averaging approach.
     *
     * @param  array<string, UiUxElementResult>  $elements
     */
    private function score(array $elements): int
    {
        $points = array_map(
            fn (UiUxElementResult $element): int => $this->pointsFor($element->status),
            $elements,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(UiUxElementStatus $status): int
    {
        return match ($status) {
            UiUxElementStatus::PASS => $this->pointsPass,
            UiUxElementStatus::WARNING => $this->pointsWarning,
            UiUxElementStatus::FAIL => $this->pointsFail,
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
     * @param  array<string, UiUxElementResult>  $elements
     */
    private function summary(array $elements, int $score, string $grade): string
    {
        $counts = ['pass' => 0, 'warning' => 0, 'fail' => 0];

        foreach ($elements as $element) {
            $counts[$element->status->value]++;
        }

        return sprintf(
            'UI/UX score %d/100 (grade %s), based on %d element(s): %d passed, %d warning(s), %d failed.',
            $score,
            $grade,
            count($elements),
            $counts['pass'],
            $counts['warning'],
            $counts['fail'],
        );
    }

    /**
     * Builds a deduplicated, priority-ordered list of improvement
     * suggestions: every suggestion from a Fail-status element first (in
     * element-array order), then every suggestion from a Warning-status
     * element, so the most impactful fixes surface first. Pass-status
     * elements contribute nothing, since they have no suggestions.
     *
     * @param  array<string, UiUxElementResult>  $elements
     * @return array<int, string>
     */
    private function prioritizedSuggestions(array $elements): array
    {
        $failSuggestions = [];
        $warningSuggestions = [];

        foreach ($elements as $element) {
            if ($element->status === UiUxElementStatus::FAIL) {
                array_push($failSuggestions, ...$element->suggestions);
            } elseif ($element->status === UiUxElementStatus::WARNING) {
                array_push($warningSuggestions, ...$element->suggestions);
            }
        }

        return array_values(array_unique([...$failSuggestions, ...$warningSuggestions]));
    }
}
