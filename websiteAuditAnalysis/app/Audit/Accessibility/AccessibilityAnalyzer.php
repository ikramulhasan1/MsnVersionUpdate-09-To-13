<?php

declare(strict_types=1);

namespace App\Audit\Accessibility;

use App\Audit\Accessibility\DTO\AccessibilityCheckResult;
use App\Audit\Accessibility\DTO\AccessibilityResult;
use App\Audit\Enums\AccessibilityCheckStatus;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\ImageAsset;

/**
 * Runs a fixed set of basic HTML accessibility checks against a single
 * fetched page: ARIA usage, image alt text, form control labeling, button
 * accessible names, inline-style color contrast, inline/legacy font size,
 * keyboard reachability of click handlers, tabindex order, heading level
 * order, and baseline WCAG document-structure compliance (page language,
 * page title, unique ids) — then rolls the results up into an overall
 * score, letter grade, and summary. Mirrors SecurityAnalyzer's
 * score()/grade()/summary() points-averaging approach.
 *
 * Deliberately scoped to only these ten checks.
 *
 * Takes a FetchResult rather than a CrawledPage because most of these
 * checks require walking the raw DOM (roles, aria-* attributes, inline
 * style attributes, tabindex, onclick handlers, <label>/<input>/<button>
 * relationships) that no existing parsed DTO captures — only Alt is
 * covered by CrawledPage's tracked ImageAsset data, the same reasoning
 * SecurityAnalyzer and PerformanceAnalyzer document for their own
 * not-yet-available data.
 *
 * Contrast and Font Size are both inherently limited here: real-world
 * color and font-size mostly live in external stylesheets, which a static
 * HTML fetch never sees. Both checks only evaluate inline `style="..."`
 * attributes (plus legacy `<font>` tags for size) and report a Warning —
 * rather than a false Pass — when nothing inline was found to check.
 */
final class AccessibilityAnalyzer
{
    /**
     * The WAI-ARIA 1.2 role vocabulary. Used to flag a `role="..."`
     * attribute whose value isn't a real ARIA role (a common copy-paste
     * or typo mistake that silently breaks assistive-technology support).
     *
     * @var array<int, string>
     */
    private const array VALID_ARIA_ROLES = [
        'alert', 'alertdialog', 'application', 'article', 'banner', 'button', 'cell', 'checkbox',
        'columnheader', 'combobox', 'complementary', 'contentinfo', 'definition', 'dialog', 'directory',
        'document', 'feed', 'figure', 'form', 'grid', 'gridcell', 'group', 'heading', 'img', 'link',
        'list', 'listbox', 'listitem', 'log', 'main', 'marquee', 'math', 'menu', 'menubar', 'menuitem',
        'menuitemcheckbox', 'menuitemradio', 'navigation', 'none', 'note', 'option', 'presentation',
        'progressbar', 'radio', 'radiogroup', 'region', 'row', 'rowgroup', 'rowheader', 'scrollbar',
        'search', 'searchbox', 'separator', 'slider', 'spinbutton', 'status', 'switch', 'tab', 'table',
        'tablist', 'tabpanel', 'term', 'textbox', 'timer', 'toolbar', 'tooltip', 'tree', 'treegrid',
        'treeitem',
    ];

    /**
     * <input type="..."> values excluded from the Label check — each of
     * these already renders its own accessible text (a submit/reset/
     * button input's "value" is its label; a hidden input is never
     * exposed to assistive technology at all).
     *
     * @var array<int, string>
     */
    private const array UNLABELABLE_INPUT_TYPES = ['hidden', 'submit', 'button', 'reset', 'image'];

    /**
     * WCAG 2.1 Success Criterion 1.4.3 (AA) minimum contrast ratio for
     * normal-size text. (Large text's lower 3:1 threshold is not applied
     * here since font-size cannot reliably be correlated with the same
     * element's color in every markup pattern.)
     */
    private const float MIN_CONTRAST_RATIO = 4.5;

    /**
     * Below this, body text is generally considered too small to read
     * comfortably, independent of any specific WCAG success criterion.
     */
    private const float MIN_FONT_SIZE_PX = 12.0;

    /**
     * Points-averaging and letter-grade thresholds for the overall score.
     * Constructor-injected so scoring can be tuned without editing check
     * logic. Mirrors SecurityAnalyzer's constructor defaults exactly.
     */
    public function __construct(
        private readonly int $pointsPass = 100,
        private readonly int $pointsWarning = 60,
        private readonly int $pointsFail = 0,
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function analyze(FetchResult $result): AccessibilityResult
    {
        $html = (string) $result->html;

        if (trim($html) === '') {
            return $this->emptyResult($result);
        }

        $xpath = new \DOMXPath($this->loadDocument($html));

        $checks = [
            'aria' => $this->checkAria($xpath),
            'alt' => $this->checkAlt($result),
            'label' => $this->checkLabel($xpath),
            'button' => $this->checkButton($xpath),
            'contrast' => $this->checkContrast($xpath),
            'font_size' => $this->checkFontSize($xpath),
            'keyboard_navigation' => $this->checkKeyboardNavigation($xpath),
            'tab_index' => $this->checkTabIndex($xpath),
            'heading_order' => $this->checkHeadingOrder($xpath),
            'wcag_compliance' => $this->checkWcagCompliance($xpath),
        ];

        $score = $this->score($checks);
        $grade = $this->grade($score);

        return new AccessibilityResult(
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

    private function emptyResult(FetchResult $result): AccessibilityResult
    {
        $noHtml = new AccessibilityCheckResult(
            check: 'ARIA',
            value: 'no HTML content to analyze',
            status: AccessibilityCheckStatus::WARNING,
            recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
        );

        $checks = [
            'aria' => $noHtml,
            'alt' => new AccessibilityCheckResult(
                check: 'Alt',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'label' => new AccessibilityCheckResult(
                check: 'Label',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'button' => new AccessibilityCheckResult(
                check: 'Button',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'contrast' => new AccessibilityCheckResult(
                check: 'Contrast',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'font_size' => new AccessibilityCheckResult(
                check: 'Font Size',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'keyboard_navigation' => new AccessibilityCheckResult(
                check: 'Keyboard Navigation',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'tab_index' => new AccessibilityCheckResult(
                check: 'Tab Index',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'heading_order' => new AccessibilityCheckResult(
                check: 'Heading Order',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
            'wcag_compliance' => new AccessibilityCheckResult(
                check: 'WCAG Compliance',
                value: 'no HTML content to analyze',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'The page returned no HTML to inspect — re-fetch the page and re-run this analysis.',
            ),
        ];

        $score = $this->score($checks);
        $grade = $this->grade($score);

        return new AccessibilityResult(
            url: $result->url,
            checks: $checks,
            score: $score,
            grade: $grade,
            summary: $this->summary($checks, $score, $grade),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    private function checkAria(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $invalidRoles = $this->findInvalidAriaRoles($xpath);
        $hiddenFocusable = $this->findAriaHiddenFocusableElements($xpath);

        if ($invalidRoles === [] && $hiddenFocusable === []) {
            return new AccessibilityCheckResult(
                check: 'ARIA',
                value: 'no ARIA issues found',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $issues = [];

        if ($invalidRoles !== []) {
            $unique = array_values(array_unique($invalidRoles));
            $issues[] = count($invalidRoles) === 1
                ? '1 element uses an invalid ARIA role: ' . implode(', ', $unique)
                : count($invalidRoles) . ' element(s) use invalid ARIA roles: ' . implode(', ', $unique);
        }

        if ($hiddenFocusable !== []) {
            $issues[] = count($hiddenFocusable) === 1
                ? '1 focusable element is hidden from assistive technology with aria-hidden="true"'
                : count($hiddenFocusable) . ' focusable elements are hidden from assistive technology with aria-hidden="true"';
        }

        return new AccessibilityCheckResult(
            check: 'ARIA',
            value: implode('; ', $issues),
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Use only valid WAI-ARIA role values, and never set aria-hidden="true" on an element '
                . 'that can still receive keyboard focus — either remove it from the tab order (tabindex="-1") '
                . 'or remove aria-hidden entirely.',
        );
    }

    /**
     * @return array<int, string> the invalid role value(s) found, one entry per offending element
     */
    private function findInvalidAriaRoles(\DOMXPath $xpath): array
    {
        $invalid = [];

        foreach ($xpath->query('//*[@role]') ?: [] as $node) {
            /** @var \DOMElement $node */
            // A role attribute may legally list multiple space-separated
            // fallback values; any one of them being invalid is a finding.
            $roles = preg_split('/\s+/', trim($node->getAttribute('role'))) ?: [];

            foreach ($roles as $role) {
                if ($role !== '' && ! in_array(strtolower($role), self::VALID_ARIA_ROLES, true)) {
                    $invalid[] = $role;
                }
            }
        }

        return $invalid;
    }

    /**
     * @return array<int, string> tag names of matching elements
     */
    private function findAriaHiddenFocusableElements(\DOMXPath $xpath): array
    {
        $query = '//*[@aria-hidden="true"]'
            . '[self::a[@href] or self::button or self::input or self::select or self::textarea'
            . ' or (@tabindex and @tabindex!="-1")]';

        $nodes = $xpath->query($query) ?: [];

        return array_map(static fn (\DOMElement $node): string => $node->nodeName, iterator_to_array($nodes));
    }

    private function checkAlt(FetchResult $result): AccessibilityCheckResult
    {
        $total = count($result->images);

        if ($total === 0) {
            return new AccessibilityCheckResult(
                check: 'Alt',
                value: 'no images found',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $missing = count(array_filter(
            $result->images,
            static fn (ImageAsset $image): bool => $image->alt === null || trim($image->alt) === '',
        ));

        if ($missing === 0) {
            return new AccessibilityCheckResult(
                check: 'Alt',
                value: 'all images have alt text',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        return new AccessibilityCheckResult(
            check: 'Alt',
            value: "{$missing} of {$total} image(s) missing alt text",
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Add descriptive alt text to every meaningful image; use alt="" only for purely '
                . 'decorative images so screen readers skip them.',
        );
    }

    private function checkLabel(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $controls = $this->collectLabelableControls($xpath);

        if ($controls === []) {
            return new AccessibilityCheckResult(
                check: 'Label',
                value: 'no form controls found',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $labelForIds = $this->collectLabelForIds($xpath);
        $unlabeled = [];

        foreach ($controls as $control) {
            if (! $this->hasAccessibleLabel($control, $xpath, $labelForIds)) {
                $unlabeled[] = $this->describeElement($control);
            }
        }

        if ($unlabeled === []) {
            return new AccessibilityCheckResult(
                check: 'Label',
                value: 'all form controls are labeled',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $count = count($unlabeled);
        $sample = implode(', ', array_slice($unlabeled, 0, 3));

        return new AccessibilityCheckResult(
            check: 'Label',
            value: $count === 1
                ? "1 form control has no accessible label: {$sample}"
                : "{$count} form controls have no accessible label, including: {$sample}",
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Give every form control an accessible name: wrap it in a <label>, add a '
                . '<label for="..."> pointing at its id, or set aria-label/aria-labelledby.',
        );
    }

    /**
     * @return array<int, \DOMElement>
     */
    private function collectLabelableControls(\DOMXPath $xpath): array
    {
        $controls = [];

        foreach ($xpath->query('//input | //select | //textarea') ?: [] as $node) {
            /** @var \DOMElement $node */
            $type = strtolower($node->getAttribute('type') ?: 'text');

            if ($node->nodeName === 'input' && in_array($type, self::UNLABELABLE_INPUT_TYPES, true)) {
                continue;
            }

            $controls[] = $node;
        }

        return $controls;
    }

    /**
     * @return array<int, string> every id referenced by a <label for="...">
     */
    private function collectLabelForIds(\DOMXPath $xpath): array
    {
        $ids = [];

        foreach ($xpath->query('//label[@for]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $for = trim($node->getAttribute('for'));

            if ($for !== '') {
                $ids[] = $for;
            }
        }

        return $ids;
    }

    /**
     * @param array<int, string> $labelForIds
     */
    private function hasAccessibleLabel(\DOMElement $control, \DOMXPath $xpath, array $labelForIds): bool
    {
        if (trim($control->getAttribute('aria-label')) !== '') {
            return true;
        }

        if ($control->hasAttribute('aria-labelledby') && trim($control->getAttribute('aria-labelledby')) !== '') {
            return true;
        }

        $id = trim($control->getAttribute('id'));

        if ($id !== '' && in_array($id, $labelForIds, true)) {
            return true;
        }

        // Implicit labeling: the control is nested inside a <label> that
        // has no "for" attribute (e.g. <label>Name <input></label>).
        $ancestorLabels = $xpath->query('ancestor::label', $control);

        return $ancestorLabels !== false && $ancestorLabels->length > 0;
    }

    private function checkButton(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $buttons = iterator_to_array($xpath->query('//button | //*[@role="button"]') ?: []);

        if ($buttons === []) {
            return new AccessibilityCheckResult(
                check: 'Button',
                value: 'no buttons found',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $unlabeled = [];

        foreach ($buttons as $button) {
            /** @var \DOMElement $button */
            if (! $this->buttonHasAccessibleName($button)) {
                $unlabeled[] = $this->describeElement($button);
            }
        }

        if ($unlabeled === []) {
            return new AccessibilityCheckResult(
                check: 'Button',
                value: 'all buttons have accessible text',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $count = count($unlabeled);
        $sample = implode(', ', array_slice($unlabeled, 0, 3));

        return new AccessibilityCheckResult(
            check: 'Button',
            value: $count === 1
                ? "1 button has no accessible text: {$sample}"
                : "{$count} buttons have no accessible text, including: {$sample}",
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Give every button visible text, or an aria-label/aria-labelledby, so its purpose is '
                . 'announced to screen reader users — this is especially common on icon-only buttons.',
        );
    }

    private function buttonHasAccessibleName(\DOMElement $button): bool
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', $button->textContent));

        if ($text !== '') {
            return true;
        }

        if (trim($button->getAttribute('aria-label')) !== '') {
            return true;
        }

        return $button->hasAttribute('aria-labelledby') && trim($button->getAttribute('aria-labelledby')) !== '';
    }

    private function describeElement(\DOMElement $element): string
    {
        $identifier = $element->getAttribute('name') ?: $element->getAttribute('id');

        return $identifier !== '' ? "{$element->nodeName}[{$identifier}]" : $element->nodeName;
    }

    private function checkContrast(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $ratios = $this->collectInlineContrastRatios($xpath);

        if ($ratios === []) {
            return new AccessibilityCheckResult(
                check: 'Contrast',
                value: 'no inline text/background color pairs found to check',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'Color contrast mostly depends on external CSS, which static HTML analysis '
                    . 'cannot read. Run an automated tool (e.g. axe, Lighthouse) or verify manually against '
                    . 'WCAG AA (4.5:1 for normal text, 3:1 for large text).',
            );
        }

        $checked = count($ratios);
        $failing = count(array_filter(
            $ratios,
            static fn (float $ratio): bool => $ratio < self::MIN_CONTRAST_RATIO,
        ));

        if ($failing === 0) {
            return new AccessibilityCheckResult(
                check: 'Contrast',
                value: "{$checked} inline-styled element(s) checked, all meet the WCAG AA 4.5:1 contrast ratio",
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        return new AccessibilityCheckResult(
            check: 'Contrast',
            value: "{$failing} of {$checked} inline-styled element(s) fall below the WCAG AA 4.5:1 contrast ratio",
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Increase the contrast between text and background color to at least 4.5:1 (3:1 '
                . 'for large text) per WCAG AA. Note: only elements with inline color/background-color styles '
                . 'were checked — colors set via external CSS are not visible to static analysis.',
        );
    }

    /**
     * Computes a WCAG contrast ratio for every element whose inline style
     * sets both `color` and a background (`background-color` or the
     * shorthand `background`). Elements using named colors, gradients, or
     * other values this parser doesn't resolve are silently skipped
     * rather than guessed at.
     *
     * @return array<int, float>
     */
    private function collectInlineContrastRatios(\DOMXPath $xpath): array
    {
        $ratios = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));

            $color = $this->extractCssValue($style, 'color');
            $background = $this->extractCssValue($style, 'background-color')
                ?? $this->extractCssValue($style, 'background');

            if ($color === null || $background === null) {
                continue;
            }

            $ratio = $this->contrastRatio($color, $background);

            if ($ratio !== null) {
                $ratios[] = $ratio;
            }
        }

        return $ratios;
    }

    private function contrastRatio(string $colorValue, string $backgroundValue): ?float
    {
        $color = $this->parseColor($colorValue);
        $background = $this->parseColor($backgroundValue);

        if ($color === null || $background === null) {
            return null;
        }

        $l1 = $this->relativeLuminance($color) + 0.05;
        $l2 = $this->relativeLuminance($background) + 0.05;

        return $l1 > $l2 ? $l1 / $l2 : $l2 / $l1;
    }

    /**
     * Parses a #hex or rgb()/rgba() CSS color into an [r, g, b] triple.
     * Named colors (e.g. "red", "cornflowerblue") and other formats
     * (hsl, currentColor, CSS variables) return null rather than being
     * approximated, since a wrong guess is worse than skipping the check.
     *
     * @return array{0: int, 1: int, 2: int}|null
     */
    private function parseColor(string $value): ?array
    {
        $value = trim($value);

        if (preg_match('/^#([0-9a-f]{3})$/i', $value, $m) === 1) {
            [$r, $g, $b] = str_split($m[1]);

            return [hexdec($r . $r), hexdec($g . $g), hexdec($b . $b)];
        }

        if (preg_match('/^#([0-9a-f]{6})$/i', $value, $m) === 1) {
            return [
                hexdec(substr($m[1], 0, 2)),
                hexdec(substr($m[1], 2, 2)),
                hexdec(substr($m[1], 4, 2)),
            ];
        }

        if (preg_match('/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $value, $m) === 1) {
            return [(int) $m[1], (int) $m[2], (int) $m[3]];
        }

        return null;
    }

    /**
     * @param array{0: int, 1: int, 2: int} $rgb
     */
    private function relativeLuminance(array $rgb): float
    {
        $channels = array_map(static function (int $channel): float {
            $normalized = $channel / 255;

            return $normalized <= 0.03928
                ? $normalized / 12.92
                : (($normalized + 0.055) / 1.055) ** 2.4;
        }, $rgb);

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function checkFontSize(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $sizes = $this->collectInlineFontSizesPx($xpath);
        $legacyNodes = $xpath->query('//font[@size]');
        $legacyCount = $legacyNodes !== false ? $legacyNodes->length : 0;

        if ($sizes === [] && $legacyCount === 0) {
            return new AccessibilityCheckResult(
                check: 'Font Size',
                value: 'no inline font-size styles or legacy <font> tags found to check',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'Font size mostly depends on external CSS, which static HTML analysis cannot '
                    . 'read. Verify with an automated tool or manually that body text renders at least 16px '
                    . '(never below ' . (int) self::MIN_FONT_SIZE_PX . 'px).',
            );
        }

        $tooSmall = count(array_filter(
            $sizes,
            static fn (float $px): bool => $px < self::MIN_FONT_SIZE_PX,
        ));

        if ($tooSmall === 0 && $legacyCount === 0) {
            return new AccessibilityCheckResult(
                check: 'Font Size',
                value: count($sizes) . ' inline font-size declaration(s) checked, all at or above '
                    . (int) self::MIN_FONT_SIZE_PX . 'px',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $issues = [];

        if ($tooSmall > 0) {
            $issues[] = "{$tooSmall} inline font-size declaration(s) below " . (int) self::MIN_FONT_SIZE_PX . 'px';
        }

        if ($legacyCount > 0) {
            $issues[] = "{$legacyCount} legacy <font> tag(s) found";
        }

        return new AccessibilityCheckResult(
            check: 'Font Size',
            value: implode('; ', $issues),
            status: $tooSmall > 0 ? AccessibilityCheckStatus::FAIL : AccessibilityCheckStatus::WARNING,
            recommendation: 'Avoid font sizes below ' . (int) self::MIN_FONT_SIZE_PX . 'px for body text, and '
                . 'replace legacy <font> tags with CSS font-size on semantic elements.',
        );
    }

    /**
     * Collects inline `font-size` declarations normalized to px. Only px
     * and pt units are converted — unitless values, %, em, and rem are
     * relative to a base size static analysis has no way to resolve, so
     * they're skipped rather than misreported.
     *
     * @return array<int, float>
     */
    private function collectInlineFontSizesPx(\DOMXPath $xpath): array
    {
        $sizes = [];

        foreach ($xpath->query('//*[@style]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $style = strtolower($node->getAttribute('style'));
            $value = $this->extractCssValue($style, 'font-size');

            if ($value === null) {
                continue;
            }

            $px = $this->toPixels($value);

            if ($px !== null) {
                $sizes[] = $px;
            }
        }

        return $sizes;
    }

    private function toPixels(string $value): ?float
    {
        $value = trim($value);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $m) === 1) {
            return (float) $m[1];
        }

        if (preg_match('/^(\d+(?:\.\d+)?)pt$/', $value, $m) === 1) {
            return (float) $m[1] * 96 / 72;
        }

        return null;
    }

    /**
     * Extracts a single CSS property's value out of an inline style
     * string. The negative lookbehind on the property name keeps a
     * search for "color" from matching inside "background-color".
     */
    private function extractCssValue(string $style, string $property): ?string
    {
        $pattern = '/(?<![a-z-])' . preg_quote($property, '/') . '\s*:\s*([^;]+)/i';

        if (preg_match($pattern, $style, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function checkKeyboardNavigation(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $unreachable = $this->findClickableNonFocusableElements($xpath);

        if ($unreachable === []) {
            return new AccessibilityCheckResult(
                check: 'Keyboard Navigation',
                value: 'no click handlers found on non-focusable elements',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $count = count($unreachable);
        $sample = implode(', ', array_slice($unreachable, 0, 3));

        return new AccessibilityCheckResult(
            check: 'Keyboard Navigation',
            value: $count === 1
                ? "1 element has a click handler but isn't keyboard-focusable: {$sample}"
                : "{$count} elements have click handlers but aren't keyboard-focusable, including: {$sample}",
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Interactive elements must be operable by keyboard: use a native <button>/<a> '
                . 'instead, or add tabindex="0" plus a role (e.g. role="button") and a matching keydown handler '
                . 'for Enter/Space.',
        );
    }

    /**
     * Finds elements with an onclick handler that are neither natively
     * focusable (a[href], button, input, select, textarea) nor made
     * focusable via an explicit tabindex — i.e. a mouse-only interaction
     * a keyboard user cannot reach at all.
     *
     * @return array<int, string>
     */
    private function findClickableNonFocusableElements(\DOMXPath $xpath): array
    {
        $query = '//*[@onclick]'
            . '[not(self::a[@href]) and not(self::button) and not(self::input) and not(self::select)'
            . ' and not(self::textarea) and not(@tabindex)]';

        $nodes = $xpath->query($query) ?: [];

        return array_map(
            fn (\DOMElement $node): string => $this->describeElement($node),
            iterator_to_array($nodes),
        );
    }

    private function checkTabIndex(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $positive = $this->findPositiveTabIndexElements($xpath);

        if ($positive === []) {
            return new AccessibilityCheckResult(
                check: 'Tab Index',
                value: 'no positive tabindex values found',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        $count = count($positive);
        $sample = implode(', ', array_slice($positive, 0, 3));

        return new AccessibilityCheckResult(
            check: 'Tab Index',
            value: $count === 1
                ? "1 element uses a positive tabindex: {$sample}"
                : "{$count} elements use a positive tabindex, including: {$sample}",
            status: AccessibilityCheckStatus::WARNING,
            recommendation: 'Avoid positive tabindex values — they override the natural DOM tab order and '
                . 'create confusing, hard-to-maintain keyboard navigation. Use tabindex="0" (natural order) or '
                . 'restructure the DOM instead.',
        );
    }

    /**
     * @return array<int, string>
     */
    private function findPositiveTabIndexElements(\DOMXPath $xpath): array
    {
        $found = [];

        foreach ($xpath->query('//*[@tabindex]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $value = trim($node->getAttribute('tabindex'));

            if (preg_match('/^\+?\d+$/', $value) === 1 && (int) $value > 0) {
                $found[] = $this->describeElement($node);
            }
        }

        return $found;
    }

    private function checkHeadingOrder(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $headings = $this->collectHeadingLevels($xpath);

        if ($headings === []) {
            return new AccessibilityCheckResult(
                check: 'Heading Order',
                value: 'no heading elements (h1-h6) found',
                status: AccessibilityCheckStatus::WARNING,
                recommendation: 'Structure page content with heading elements (h1-h6) so screen reader users '
                    . 'can navigate the page by outline.',
            );
        }

        $issues = [];

        if ($headings[0]['level'] !== 1) {
            $issues[] = "page does not start with an <h1> (first heading is <h{$headings[0]['level']}>)";
        }

        $skips = $this->findSkippedHeadingLevels($headings);

        if ($skips !== []) {
            $count = count($skips);
            $sample = implode(', ', array_slice($skips, 0, 3));
            $issues[] = $count === 1
                ? "1 heading level is skipped: {$sample}"
                : "{$count} heading levels are skipped, including: {$sample}";
        }

        if ($issues === []) {
            return new AccessibilityCheckResult(
                check: 'Heading Order',
                value: count($headings) . ' heading(s) checked, order is sequential',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        return new AccessibilityCheckResult(
            check: 'Heading Order',
            value: implode('; ', $issues),
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Start the page with a single <h1> and increase heading levels one step at a time '
                . '(e.g. h2 then h3, never h2 then h4) so the document outline stays logical for assistive '
                . 'technology.',
        );
    }

    /**
     * Collects every h1-h6 element in document order. A DOMXPath union
     * query normalizes results into document order regardless of the
     * order the alternatives are listed in, so no extra sort is needed.
     *
     * @return array<int, array{level: int, label: string}>
     */
    private function collectHeadingLevels(\DOMXPath $xpath): array
    {
        $headings = [];

        foreach ($xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6') ?: [] as $node) {
            /** @var \DOMElement $node */
            $headings[] = [
                'level' => (int) substr($node->nodeName, 1),
                'label' => $this->describeElement($node),
            ];
        }

        return $headings;
    }

    /**
     * @param array<int, array{level: int, label: string}> $headings
     * @return array<int, string>
     */
    private function findSkippedHeadingLevels(array $headings): array
    {
        $skips = [];
        $previousLevel = null;

        foreach ($headings as $heading) {
            if ($previousLevel !== null && $heading['level'] > $previousLevel + 1) {
                $skips[] = "h{$previousLevel} to h{$heading['level']} ({$heading['label']})";
            }

            $previousLevel = $heading['level'];
        }

        return $skips;
    }

    private function checkWcagCompliance(\DOMXPath $xpath): AccessibilityCheckResult
    {
        $issues = [];

        if (! $this->hasPageLanguage($xpath)) {
            $issues[] = 'the <html> element has no (or an empty) lang attribute';
        }

        if (! $this->hasPageTitle($xpath)) {
            $issues[] = 'the page has no (or an empty) <title> element';
        }

        $duplicateIds = $this->findDuplicateIds($xpath);

        if ($duplicateIds !== []) {
            $count = count($duplicateIds);
            $sample = implode(', ', array_slice($duplicateIds, 0, 3));
            $issues[] = $count === 1
                ? "1 id attribute is duplicated: {$sample}"
                : "{$count} id attributes are duplicated, including: {$sample}";
        }

        if ($issues === []) {
            return new AccessibilityCheckResult(
                check: 'WCAG Compliance',
                value: 'page language, page title, and unique element ids are all present',
                status: AccessibilityCheckStatus::PASS,
                recommendation: null,
            );
        }

        return new AccessibilityCheckResult(
            check: 'WCAG Compliance',
            value: implode('; ', $issues),
            status: AccessibilityCheckStatus::FAIL,
            recommendation: 'Set a valid lang attribute on <html> (WCAG 3.1.1), give the page a descriptive '
                . '<title> (WCAG 2.4.2), and ensure every id attribute is unique (WCAG 4.1.1) so assistive '
                . 'technology can reliably parse and reference the document.',
        );
    }

    private function hasPageLanguage(\DOMXPath $xpath): bool
    {
        $nodes = $xpath->query('//html[@lang]');

        if ($nodes === false || $nodes->length === 0) {
            return false;
        }

        /** @var \DOMElement $html */
        $html = $nodes->item(0);

        return trim($html->getAttribute('lang')) !== '';
    }

    private function hasPageTitle(\DOMXPath $xpath): bool
    {
        $nodes = $xpath->query('//title');

        if ($nodes === false || $nodes->length === 0) {
            return false;
        }

        /** @var \DOMElement $title */
        $title = $nodes->item(0);

        return trim($title->textContent) !== '';
    }

    /**
     * @return array<int, string> id values that appear on more than one element
     */
    private function findDuplicateIds(\DOMXPath $xpath): array
    {
        $seen = [];
        $duplicates = [];

        foreach ($xpath->query('//*[@id]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $id = trim($node->getAttribute('id'));

            if ($id === '') {
                continue;
            }

            if (isset($seen[$id]) && ! in_array($id, $duplicates, true)) {
                $duplicates[] = $id;
            }

            $seen[$id] = true;
        }

        return $duplicates;
    }

    /**
     * Averages points across every check (pass/warning/fail all map to a
     * point value), then rounds to the nearest whole score. Mirrors
     * SecurityAnalyzer::score()'s points-averaging approach.
     *
     * @param array<string, AccessibilityCheckResult> $checks
     */
    private function score(array $checks): int
    {
        $points = array_map(
            fn (AccessibilityCheckResult $check): int => $this->pointsFor($check->status),
            $checks,
        );

        return (int) round(array_sum($points) / count($points));
    }

    private function pointsFor(AccessibilityCheckStatus $status): int
    {
        return match ($status) {
            AccessibilityCheckStatus::PASS => $this->pointsPass,
            AccessibilityCheckStatus::WARNING => $this->pointsWarning,
            AccessibilityCheckStatus::FAIL => $this->pointsFail,
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
     * @param array<string, AccessibilityCheckResult> $checks
     */
    private function summary(array $checks, int $score, string $grade): string
    {
        $counts = ['pass' => 0, 'warning' => 0, 'fail' => 0];

        foreach ($checks as $check) {
            $counts[$check->status->value]++;
        }

        return sprintf(
            'Accessibility score %d/100 (grade %s), based on %d check(s): %d passed, %d warning(s), %d failed.',
            $score,
            $grade,
            count($checks),
            $counts['pass'],
            $counts['warning'],
            $counts['fail'],
        );
    }
}
