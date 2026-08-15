<?php

declare(strict_types=1);

namespace App\Audit\Technology;

use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\ScriptLink;
use App\Audit\Technology\DTO\TechnologyDetectionResult;
use App\Audit\Technology\DTO\TechnologyResult;

/**
 * Fingerprints a single fetched page for a fixed set of CMS/backend
 * technologies: Laravel, WordPress, WooCommerce, and Shopify.
 *
 * Each technology is scored independently by combining several
 * independent signals (response headers, cookies, the meta "generator"
 * tag, asset path patterns, and inline HTML/JS markers). Every signal
 * carries its own confidence weight; a technology counts as "detected"
 * once its combined weight clears self::DETECTION_THRESHOLD. This
 * mirrors the weighted-checklist approach the other Audit analyzers use
 * for scoring, adapted here to a single 0-100 confidence value per
 * technology rather than a Pass/Warning/Fail status, since "confidence
 * a technology is present" is inherently a spectrum rather than a
 * checklist item that can outright pass or fail.
 *
 * Each signal a detect*() method finds is additionally, wherever
 * possible, tagged with its own evidence — either the specific asset
 * URL that matched ('url'), or a short raw snippet of the matched HTML/
 * cookie/header text ('snippet') — alongside its existing weight/label.
 * buildResult() rolls a technology's signals up into a single
 * evidenceUrl/evidenceSnippet pair on the returned
 * TechnologyDetectionResult (see primaryEvidence()): whichever signal
 * carried the *highest weight* among those with evidence, so the
 * reported evidence is always the strongest reason the technology was
 * flagged, not just whichever signal happened to be checked first.
 *
 * Takes a FetchResult (not a CrawledPage) for the same reason
 * SecurityAnalyzer does: fingerprinting needs the raw response headers
 * (Set-Cookie, X-Shopify-Stage, etc.) and raw HTML (inline JS globals,
 * body classes), neither of which CrawledPage carries.
 *
 * Part 2 extends this detector with five JavaScript framework
 * fingerprints — React, Vue, Angular, Next.js, and Nuxt — reusing the
 * exact same weighted-signal / DETECTION_THRESHOLD approach as Part 1.
 * The Part 1 methods (detectLaravel/detectWordPress/detectWooCommerce/
 * detectShopify) and their private helpers are unchanged; only new
 * private detect*() methods, one new version-extraction helper for
 * CDN-hosted package URLs, and five new entries in detect()'s
 * $detections array were added.
 *
 * Deliberately scoped to only these technologies — no other technology
 * is detected, and no overall "Technology Stack" summary is produced
 * here.
 */
final class TechnologyDetector
{
    /**
     * Combined signal weight at or above which a technology is
     * considered detected. Below this, matched signals are still
     * reported (confidence score > 0) but detected is false, since a
     * single weak/ambiguous signal alone (e.g. one generic cookie name)
     * is not reliable enough to claim a positive match.
     */
    private const int DETECTION_THRESHOLD = 30;

    private const int MAX_CONFIDENCE = 100;

    /**
     * Maps each technology slug produced by detect()'s $detections array
     * to the category it belongs to under the "Complete Technology
     * Stack" / "Technology Summary" output (see TechnologyResult::$technologyStack
     * and $technologySummary, and buildTechnologyStack()/buildTechnologySummary()
     * below, which already read this).
     *
     * Public (not private) so App\Discovery\Taxonomy\TechnologyFilterOptions
     * can group this same vocabulary into the Website Discovery
     * module's Technology filter checkboxes (CMS/Framework/Ecommerce
     * Platform/CDN) — the audit engine and the discovery filter share
     * one list rather than keeping two in sync by hand.
     *
     * @var array<string, string>
     */
    public const array CATEGORY_MAP = [
        'laravel' => 'Backend Framework',
        'wordpress' => 'CMS',
        'woocommerce' => 'Ecommerce',
        'shopify' => 'Ecommerce',
        'react' => 'JavaScript Framework',
        'vue' => 'JavaScript Framework',
        'angular' => 'JavaScript Framework',
        'nextjs' => 'JavaScript Framework',
        'nuxt' => 'JavaScript Framework',
        'tailwind' => 'CSS Framework',
        'bootstrap' => 'CSS Framework',
        'jquery' => 'JavaScript Library',
        'google_analytics' => 'Analytics',
        'google_tag_manager' => 'Analytics',
        'facebook_pixel' => 'Marketing',
        'microsoft_clarity' => 'Analytics',
        'google_ads' => 'Advertising',
        'cloudflare' => 'Infrastructure',
    ];

    /**
     * The human-readable display name for each technology slug — the
     * exact same string each detect*() method below already passes as
     * its TechnologyDetectionResult's own $technology parameter (e.g.
     * detectWordPress()'s `new TechnologyDetectionResult(technology: 'WordPress', ...)`).
     * Kept here too, publicly, purely so a caller that only has a slug
     * (e.g. TechnologyFilterOptions, building filter checkboxes from
     * CATEGORY_MAP's keys) can resolve a display name without running
     * detection at all — this does not replace or feed into the
     * per-technology detect*() methods themselves, which remain the
     * source of truth for a real detection result's own $technology
     * value.
     *
     * @var array<string, string>
     */
    public const array TECHNOLOGY_NAMES = [
        'laravel' => 'Laravel',
        'wordpress' => 'WordPress',
        'woocommerce' => 'WooCommerce',
        'shopify' => 'Shopify',
        'react' => 'React',
        'vue' => 'Vue',
        'angular' => 'Angular',
        'nextjs' => 'Next.js',
        'nuxt' => 'Nuxt',
        'tailwind' => 'Tailwind CSS',
        'bootstrap' => 'Bootstrap',
        'jquery' => 'jQuery',
        'google_analytics' => 'Google Analytics',
        'google_tag_manager' => 'Google Tag Manager',
        'facebook_pixel' => 'Facebook Pixel',
        'microsoft_clarity' => 'Microsoft Clarity',
        'google_ads' => 'Google Ads',
        'cloudflare' => 'Cloudflare',
    ];
    public function __construct(
        private readonly int $detectionThreshold = self::DETECTION_THRESHOLD,
    ) {}

    public function detect(FetchResult $result): TechnologyResult
    {
        $detections = [
            'laravel' => $this->detectLaravel($result),
            'wordpress' => $this->detectWordPress($result),
            'woocommerce' => $this->detectWooCommerce($result),
            'shopify' => $this->detectShopify($result),
            'react' => $this->detectReact($result),
            'vue' => $this->detectVue($result),
            'angular' => $this->detectAngular($result),
            'nextjs' => $this->detectNextJs($result),
            'nuxt' => $this->detectNuxt($result),
            'tailwind' => $this->detectTailwind($result),
            'bootstrap' => $this->detectBootstrap($result),
            'jquery' => $this->detectJquery($result),
            'google_analytics' => $this->detectGoogleAnalytics($result),
            'google_tag_manager' => $this->detectGoogleTagManager($result),
            'facebook_pixel' => $this->detectFacebookPixel($result),
            'microsoft_clarity' => $this->detectMicrosoftClarity($result),
            'google_ads' => $this->detectGoogleAds($result),
            'cloudflare' => $this->detectCloudflare($result),
        ];

        $technologyStack = $this->buildTechnologyStack($detections);
        $technologySummary = $this->buildTechnologySummary($detections);

        return new TechnologyResult(
            url: $result->url,
            detections: $detections,
            technologyStack: $technologyStack,
            technologySummary: $technologySummary,
            overallDetectionConfidence: $this->computeOverallConfidence($detections),
            serverHeader: $this->serverInfo($result),
            analyzedAt: (new \DateTimeImmutable)->format(DATE_ATOM),
        );
    }

    /**
     * Averages confidenceScore across every detected technology, rounded
     * to the nearest int, for TechnologyResult::$overallDetectionConfidence.
     * 0 when nothing was detected, rather than dividing by zero.
     *
     * @param  array<string, TechnologyDetectionResult>  $detections  keyed by technology slug
     */
    private function computeOverallConfidence(array $detections): int
    {
        $detected = array_filter(
            $detections,
            static fn (TechnologyDetectionResult $detection): bool => $detection->detected,
        );

        if ($detected === []) {
            return 0;
        }

        $scores = array_map(
            static fn (TechnologyDetectionResult $detection): int => $detection->confidenceScore,
            $detected,
        );

        return (int) round(array_sum($scores) / count($scores));
    }

    /**
     * Builds the "Technology Summary" counts: how many technologies were
     * detected out of how many were checked, broken down by category,
     * for TechnologyResult::$technologySummary (see its docblock).
     *
     * @param  array<string, TechnologyDetectionResult>  $detections  keyed by technology slug
     * @return array{total_detected: int, total_checked: int, by_category: array<string, int>}
     */
    private function buildTechnologySummary(array $detections): array
    {
        $totalDetected = 0;
        $byCategory = [];

        foreach ($detections as $slug => $detection) {
            if (! $detection->detected) {
                continue;
            }

            $totalDetected++;

            $category = self::CATEGORY_MAP[$slug];
            $byCategory[$category] = ($byCategory[$category] ?? 0) + 1;
        }

        return [
            'total_detected' => $totalDetected,
            'total_checked' => count($detections),
            'by_category' => $byCategory,
        ];
    }

    /**
     * Builds the "Complete Technology Stack" entry list: every slug from
     * $detections where detected === true, each shaped for
     * TechnologyResult::$technologyStack (see its docblock).
     *
     * @param  array<string, TechnologyDetectionResult>  $detections  keyed by technology slug
     * @return array<int, array{slug: string, technology: string, category: string, version: ?string}>
     */
    private function buildTechnologyStack(array $detections): array
    {
        $stack = [];

        foreach ($detections as $slug => $detection) {
            if (! $detection->detected) {
                continue;
            }

            $stack[] = [
                'slug' => $slug,
                'technology' => $detection->technology,
                'category' => self::CATEGORY_MAP[$slug],
                'version' => $detection->version,
            ];
        }

        return $stack;
    }

    /**
     * Laravel has no public, reliable version fingerprint (unlike
     * WordPress/WooCommerce, it does not print its version into HTML or
     * headers on a production site), so version is always null here
     * rather than guessed.
     */
    private function detectLaravel(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $laravelCookie = $this->cookieSnippet($result, 'laravel_session');

        if ($laravelCookie !== null) {
            $signals[] = ['weight' => 45, 'label' => 'laravel_session cookie present', 'snippet' => "Set-Cookie: {$laravelCookie}"];
        }

        $xsrfCookie = $this->cookieSnippet($result, 'XSRF-TOKEN');

        if ($xsrfCookie !== null) {
            $signals[] = ['weight' => 25, 'label' => 'XSRF-TOKEN cookie present', 'snippet' => "Set-Cookie: {$xsrfCookie}"];
        }

        $csrfMetaSnippet = $this->htmlMetaCsrfSnippet($html);

        if ($csrfMetaSnippet !== null) {
            $signals[] = ['weight' => 20, 'label' => 'csrf-token meta tag present', 'snippet' => $csrfMetaSnippet];
        }

        $wireNeedle = $this->firstMatchingNeedle($html, ['wire:id=', 'wire:model=', 'wire:click=']);

        if ($wireNeedle !== null) {
            $signals[] = [
                'weight' => 20,
                'label' => 'Livewire wire: attributes present (Laravel package)',
                'snippet' => $this->htmlSnippet($html, $wireNeedle),
            ];
        }

        $livewireLink = $this->firstMatchingLinkUrl($result->jsLinks, ['/livewire/livewire.js', '/vendor/livewire/']);

        if ($livewireLink !== null) {
            $signals[] = ['weight' => 15, 'label' => 'Livewire asset path present (Laravel package)', 'url' => $livewireLink];
        }

        return $this->buildResult('Laravel', version: null, signals: $signals);
    }

    private function detectWordPress(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];
        $version = null;

        $generator = $this->metaGeneratorContent($result);

        if ($generator !== null && stripos($generator, 'WordPress') !== false) {
            $signals[] = [
                'weight' => 55,
                'label' => "meta generator tag: \"{$generator}\"",
                'snippet' => $this->metaGeneratorSnippet($generator),
            ];
            $version = $this->extractVersion($generator, 'WordPress');
        }

        $apiOrgNeedle = $this->firstMatchingNeedle($html, ['api.w.org']);

        if ($apiOrgNeedle !== null) {
            $signals[] = [
                'weight' => 25,
                'label' => 'WordPress REST API discovery link (api.w.org) present',
                'snippet' => $this->htmlSnippet($html, $apiOrgNeedle),
            ];
        }

        $wpAssetLink = $this->firstMatchingLinkUrl(
            [...$result->cssLinks, ...$result->jsLinks],
            ['/wp-content/', '/wp-includes/'],
        );

        if ($wpAssetLink !== null) {
            $signals[] = ['weight' => 40, 'label' => '/wp-content/ or /wp-includes/ asset paths present', 'url' => $wpAssetLink];
        }

        $wpCookie = $this->cookieSnippet($result, 'wordpress_', 'wp-settings-');

        if ($wpCookie !== null) {
            $signals[] = [
                'weight' => 25,
                'label' => 'WordPress cookie (wordpress_* / wp-settings-*) present',
                'snippet' => "Set-Cookie: {$wpCookie}",
            ];
        }

        return $this->buildResult('WordPress', $version, $signals);
    }

    private function detectWooCommerce(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];
        $version = null;

        $generator = $this->metaGeneratorContent($result);

        if ($generator !== null && stripos($generator, 'WooCommerce') !== false) {
            $signals[] = [
                'weight' => 55,
                'label' => "meta generator tag: \"{$generator}\"",
                'snippet' => $this->metaGeneratorSnippet($generator),
            ];
            $version = $this->extractVersion($generator, 'WooCommerce');
        }

        $wcAssetLink = $this->firstMatchingLinkUrl(
            [...$result->cssLinks, ...$result->jsLinks],
            ['/plugins/woocommerce/'],
        );

        if ($wcAssetLink !== null) {
            $signals[] = ['weight' => 35, 'label' => '/plugins/woocommerce/ asset path present', 'url' => $wcAssetLink];
        }

        $wcJsNeedle = $this->firstMatchingNeedle(
            $html,
            ['woocommerce_params', 'wc_add_to_cart_params', 'wc_cart_fragments_params'],
        );

        if ($wcJsNeedle !== null) {
            $signals[] = [
                'weight' => 30,
                'label' => 'WooCommerce inline JS configuration object present',
                'snippet' => $this->htmlSnippet($html, $wcJsNeedle),
            ];
        }

        $bodyClassSnippet = $this->htmlBodyClassSnippet($html, 'woocommerce');

        if ($bodyClassSnippet !== null) {
            $signals[] = [
                'weight' => 20,
                'label' => '<body> element carries a "woocommerce" class',
                'snippet' => $bodyClassSnippet,
            ];
        }

        $wcCookie = $this->cookieSnippet($result, 'woocommerce_', 'wp_woocommerce_session_');

        if ($wcCookie !== null) {
            $signals[] = [
                'weight' => 25,
                'label' => 'WooCommerce cookie (woocommerce_* / wp_woocommerce_session_*) present',
                'snippet' => "Set-Cookie: {$wcCookie}",
            ];
        }

        return $this->buildResult('WooCommerce', $version, $signals);
    }

    /**
     * Shopify is SaaS-hosted, so — like Laravel — there is no public
     * "platform version" it exposes; version is always null here.
     */
    private function detectShopify(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $generator = $this->metaGeneratorContent($result);

        if ($generator !== null && stripos($generator, 'Shopify') !== false) {
            $signals[] = [
                'weight' => 55,
                'label' => "meta generator tag: \"{$generator}\"",
                'snippet' => $this->metaGeneratorSnippet($generator),
            ];
        }

        $shopifyAssetLink = $this->firstMatchingLinkUrl(
            [...$result->cssLinks, ...$result->jsLinks],
            ['cdn.shopify.com', '.myshopify.com'],
        );

        if ($shopifyAssetLink !== null) {
            $signals[] = ['weight' => 40, 'label' => 'cdn.shopify.com / *.myshopify.com asset path present', 'url' => $shopifyAssetLink];
        }

        $shopifyJsNeedle = $this->firstMatchingNeedle($html, ['Shopify.shop', 'window.Shopify', 'Shopify.theme']);

        if ($shopifyJsNeedle !== null) {
            $signals[] = [
                'weight' => 30,
                'label' => 'Shopify inline JS global (window.Shopify) present',
                'snippet' => $this->htmlSnippet($html, $shopifyJsNeedle),
            ];
        }

        $shopifyHeader = $this->headerSnippet($result, 'X-Shopify-Stage', 'X-ShardId', 'X-Sorting-Hat-PodId');

        if ($shopifyHeader !== null) {
            $signals[] = ['weight' => 40, 'label' => 'Shopify infrastructure response header present', 'snippet' => $shopifyHeader];
        }

        $shopifyCookie = $this->cookieSnippet($result, '_shopify_s', '_shopify_y');

        if ($shopifyCookie !== null) {
            $signals[] = [
                'weight' => 20,
                'label' => '_shopify_s / _shopify_y cookie present',
                'snippet' => "Set-Cookie: {$shopifyCookie}",
            ];
        }

        return $this->buildResult('Shopify', version: null, signals: $signals);
    }

    /**
     * React ships no server-visible "version" outside of whatever CDN
     * URL a site happens to load it from, so version is only ever
     * populated when a react/react-dom script is loaded from a
     * versioned CDN path (e.g. unpkg.com/react@18.2.0); framework
     * bundles built with a build tool (Vite/CRA/webpack) give no
     * public version fingerprint.
     */
    private function detectReact(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $reactRootNeedle = $this->firstMatchingNeedle($html, ['data-reactroot', 'data-reactid']);

        if ($reactRootNeedle !== null) {
            $signals[] = [
                'weight' => 35,
                'label' => 'data-reactroot / data-reactid attribute present',
                'snippet' => $this->htmlSnippet($html, $reactRootNeedle),
            ];
        }

        $reactJsNeedle = $this->firstMatchingNeedle(
            $html,
            ['__REACT_DEVTOOLS_GLOBAL_HOOK__', 'ReactDOM.render', 'ReactDOM.hydrate', 'ReactDOM.createRoot'],
        );

        if ($reactJsNeedle !== null) {
            $signals[] = ['weight' => 30, 'label' => 'React inline JS marker present', 'snippet' => $this->htmlSnippet($html, $reactJsNeedle)];
        }

        $reactLink = $this->firstMatchingLinkUrl($result->jsLinks, ['react-dom', 'react.production.min.js', 'react.development.js']);

        if ($reactLink !== null) {
            $signals[] = ['weight' => 35, 'label' => 'react / react-dom asset path present', 'url' => $reactLink];
        }

        $version = $this->extractVersionFromLinks($result->jsLinks, 'react');

        return $this->buildResult('React', $version, $signals);
    }

    /**
     * Like React, Vue's version is only publicly visible when loaded
     * from a versioned CDN URL (e.g. unpkg.com/vue@3.4.0); a bundled
     * build gives no public version fingerprint.
     */
    private function detectVue(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $vueMarkerNeedle = $this->firstMatchingNeedle($html, ['data-v-', '__VUE__', 'window.__VUE_DEVTOOLS_GLOBAL_HOOK__']);

        if ($vueMarkerNeedle !== null) {
            $signals[] = [
                'weight' => 30,
                'label' => 'data-v-* scoped-style attribute or Vue global present',
                'snippet' => $this->htmlSnippet($html, $vueMarkerNeedle),
            ];
        }

        $vueJsNeedle = $this->firstMatchingNeedle($html, ['Vue.createApp', 'new Vue(', 'createApp(']);

        if ($vueJsNeedle !== null) {
            $signals[] = ['weight' => 30, 'label' => 'Vue inline JS marker present', 'snippet' => $this->htmlSnippet($html, $vueJsNeedle)];
        }

        $vueLink = $this->firstMatchingLinkUrl(
            $result->jsLinks,
            ['vue.global.js', 'vue.runtime.esm', 'vue.esm-browser', 'vue.min.js'],
        );

        if ($vueLink !== null) {
            $signals[] = ['weight' => 35, 'label' => 'vue asset path present', 'url' => $vueLink];
        }

        $version = $this->extractVersionFromLinks($result->jsLinks, 'vue');

        return $this->buildResult('Vue', $version, $signals);
    }

    /**
     * Modern Angular (2+) uniquely stamps its root component with an
     * ng-version="X.Y.Z" attribute at runtime, which is by far the
     * most reliable signal here and the only one version is read from.
     */
    private function detectAngular(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];
        $version = null;

        if (preg_match('/ng-version=["\']([0-9][0-9.]*)["\']/i', $html, $matches) === 1) {
            $signals[] = ['weight' => 55, 'label' => "ng-version attribute present: \"{$matches[1]}\"", 'snippet' => $matches[0]];
            $version = $matches[1];
        }

        $appRootNeedle = $this->firstMatchingNeedle($html, ['<app-root']);

        if ($appRootNeedle !== null) {
            $signals[] = [
                'weight' => 20,
                'label' => '<app-root> host element present',
                'snippet' => $this->htmlSnippet($html, $appRootNeedle),
            ];
        }

        $angularLink = $this->firstMatchingLinkUrl($result->jsLinks, ['zone.js', 'runtime.js', 'polyfills.js']);

        if ($angularLink !== null) {
            $signals[] = ['weight' => 20, 'label' => 'zone.js / Angular CLI runtime bundle asset path present', 'url' => $angularLink];
        }

        return $this->buildResult('Angular', $version, $signals);
    }

    /**
     * Next.js embeds its page props/build metadata in a
     * <script id="__NEXT_DATA__"> tag on every server-rendered page,
     * which combined with the /_next/static/ asset path is a highly
     * reliable pair of signals. Some Next.js sites also emit a
     * generator meta tag naming the framework version; when present,
     * that is the only source used for version.
     */
    private function detectNextJs(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];
        $version = null;

        $generator = $this->metaGeneratorContent($result);

        if ($generator !== null && stripos($generator, 'Next.js') !== false) {
            $signals[] = [
                'weight' => 40,
                'label' => "meta generator tag: \"{$generator}\"",
                'snippet' => $this->metaGeneratorSnippet($generator),
            ];
            $version = $this->extractVersion($generator, 'Next.js');
        }

        $nextDataNeedle = $this->firstMatchingNeedle($html, ['id="__NEXT_DATA__', "id='__NEXT_DATA__"]);

        if ($nextDataNeedle !== null) {
            $signals[] = [
                'weight' => 45,
                'label' => '__NEXT_DATA__ script tag present',
                'snippet' => $this->htmlSnippet($html, $nextDataNeedle),
            ];
        }

        $nextAssetLink = $this->firstMatchingLinkUrl([...$result->jsLinks, ...$result->cssLinks], ['/_next/static/']);

        if ($nextAssetLink !== null) {
            $signals[] = ['weight' => 35, 'label' => '/_next/static/ asset path present', 'url' => $nextAssetLink];
        }

        $nextHeadNeedle = $this->firstMatchingNeedle($html, ['name="next-head-count"', "name='next-head-count'"]);

        if ($nextHeadNeedle !== null) {
            $signals[] = [
                'weight' => 20,
                'label' => 'next-head-count meta tag present',
                'snippet' => $this->htmlSnippet($html, $nextHeadNeedle),
            ];
        }

        return $this->buildResult('Next.js', $version, $signals);
    }

    /**
     * Nuxt mirrors Next.js: a __NUXT__ inline JS payload and /_nuxt/
     * asset paths are the strongest signals. A generator meta tag
     * naming Nuxt (with or without a version) is the only source used
     * for version.
     */
    private function detectNuxt(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];
        $version = null;

        $generator = $this->metaGeneratorContent($result);

        if ($generator !== null && stripos($generator, 'Nuxt') !== false) {
            $signals[] = [
                'weight' => 40,
                'label' => "meta generator tag: \"{$generator}\"",
                'snippet' => $this->metaGeneratorSnippet($generator),
            ];
            $version = $this->extractVersion($generator, 'Nuxt');
        }

        $nuxtPayloadNeedle = $this->firstMatchingNeedle($html, ['window.__NUXT__', '__NUXT__=']);

        if ($nuxtPayloadNeedle !== null) {
            $signals[] = [
                'weight' => 45,
                'label' => '__NUXT__ inline JS payload present',
                'snippet' => $this->htmlSnippet($html, $nuxtPayloadNeedle),
            ];
        }

        $nuxtRootNeedle = $this->firstMatchingNeedle($html, ['id="__nuxt"', "id='__nuxt'"]);

        if ($nuxtRootNeedle !== null) {
            $signals[] = [
                'weight' => 25,
                'label' => '#__nuxt root element present',
                'snippet' => $this->htmlSnippet($html, $nuxtRootNeedle),
            ];
        }

        $nuxtAssetLink = $this->firstMatchingLinkUrl([...$result->jsLinks, ...$result->cssLinks], ['/_nuxt/']);

        if ($nuxtAssetLink !== null) {
            $signals[] = ['weight' => 35, 'label' => '/_nuxt/ asset path present', 'url' => $nuxtAssetLink];
        }

        return $this->buildResult('Nuxt', $version, $signals);
    }

    /**
     * Tailwind ships no runtime version marker of its own (it's a
     * build-time CSS generator, not a loaded library) except when
     * pulled in via the Play CDN script, so version is only ever
     * populated from that CDN URL — a project-local compiled
     * stylesheet gives no public version fingerprint.
     */
    private function detectTailwind(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $tailwindCdnLink = $this->firstMatchingLinkUrl($result->jsLinks, ['cdn.tailwindcss.com']);

        if ($tailwindCdnLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'cdn.tailwindcss.com Play CDN script present', 'url' => $tailwindCdnLink];
        }

        $tailwindConfigNeedle = $this->firstMatchingNeedle($html, ['tailwind.config']);

        if ($tailwindConfigNeedle !== null) {
            $signals[] = [
                'weight' => 35,
                'label' => 'inline tailwind.config reference present',
                'snippet' => $this->htmlSnippet($html, $tailwindConfigNeedle),
            ];
        }

        $utilitySnippet = $this->htmlDenseUtilityClassSnippet($html);

        if ($utilitySnippet !== null) {
            $signals[] = [
                'weight' => 30,
                'label' => 'class attributes are dominated by Tailwind-style utility classes',
                'snippet' => $utilitySnippet,
            ];
        }

        $version = $this->extractVersionFromLinks($result->jsLinks, 'tailwindcss');

        return $this->buildResult('Tailwind CSS', $version, $signals);
    }

    private function detectBootstrap(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $bootstrapLink = $this->firstMatchingLinkUrl(
            [...$result->cssLinks, ...$result->jsLinks],
            ['bootstrap.min.css', 'bootstrap.min.js', 'bootstrap.bundle.min.js'],
        );

        if ($bootstrapLink !== null) {
            $signals[] = [
                'weight' => 45,
                'label' => 'bootstrap.min.css / bootstrap(.bundle).min.js asset path present',
                'url' => $bootstrapLink,
            ];
        }

        $bsAttrNeedle = $this->firstMatchingNeedle(
            $html,
            ['data-bs-toggle=', 'data-bs-target=', 'data-bs-dismiss=', 'data-bs-ride='],
        );

        if ($bsAttrNeedle !== null) {
            $signals[] = ['weight' => 35, 'label' => 'data-bs-* attribute present', 'snippet' => $this->htmlSnippet($html, $bsAttrNeedle)];
        }

        $version = $this->extractVersionFromLinks([...$result->cssLinks, ...$result->jsLinks], 'bootstrap');

        return $this->buildResult('Bootstrap', $version, $signals);
    }

    private function detectJquery(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $jqueryLink = $this->firstMatchingLinkUrl($result->jsLinks, ['jquery.min.js', 'jquery-']);

        if ($jqueryLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'jquery.min.js / jquery-*.js asset path present', 'url' => $jqueryLink];
        }

        $jqueryNeedle = $this->firstMatchingNeedle($html, ['$(document).ready(', 'jQuery(', '$(function(']);

        if ($jqueryNeedle !== null) {
            $signals[] = ['weight' => 30, 'label' => 'jQuery inline JS marker present', 'snippet' => $this->htmlSnippet($html, $jqueryNeedle)];
        }

        $version = $this->extractJqueryVersionFromLinks($result->jsLinks);

        return $this->buildResult('jQuery', $version, $signals);
    }

    private function detectGoogleAnalytics(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $gaLink = $this->firstMatchingLinkUrl($result->jsLinks, ['googletagmanager.com/gtag/js']);

        if ($gaLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'googletagmanager.com/gtag/js asset path present', 'url' => $gaLink];
        }

        $gaCreateNeedle = $this->firstMatchingNeedle($html, ["ga('create'", 'ga("create"']);

        if ($gaCreateNeedle !== null) {
            $signals[] = [
                'weight' => 40,
                'label' => "inline ga('create' ...) Universal Analytics marker present",
                'snippet' => $this->htmlSnippet($html, $gaCreateNeedle),
            ];
        }

        if (preg_match('/\bG-[A-Z0-9]{6,}\b|\bUA-[0-9]{4,}-[0-9]+\b/', $html, $matches) === 1) {
            $signals[] = ['weight' => 40, 'label' => 'G- or UA- prefixed measurement/tracking ID present', 'snippet' => $matches[0]];
        }

        $gtagNeedle = $this->firstMatchingNeedle($html, ['gtag(']);

        if ($gtagNeedle !== null) {
            $signals[] = [
                'weight' => 15,
                'label' => 'gtag( call present (shared with GTM/Google Ads, so weighted weakly alone)',
                'snippet' => $this->htmlSnippet($html, $gtagNeedle),
            ];
        }

        return $this->buildResult('Google Analytics', version: null, signals: $signals);
    }

    private function detectGoogleTagManager(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $gtmLink = $this->firstMatchingLinkUrl($result->jsLinks, ['googletagmanager.com/gtm.js']);

        if ($gtmLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'googletagmanager.com/gtm.js asset path present', 'url' => $gtmLink];
        }

        if (preg_match('/\bGTM-[A-Z0-9]{4,}\b/', $html, $matches) === 1) {
            $signals[] = ['weight' => 40, 'label' => 'GTM- prefixed container ID present', 'snippet' => $matches[0]];
        }

        return $this->buildResult('Google Tag Manager', version: null, signals: $signals);
    }

    private function detectFacebookPixel(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $fbLink = $this->firstMatchingLinkUrl($result->jsLinks, ['connect.facebook.net', 'fbevents.js']);

        if ($fbLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'connect.facebook.net/.../fbevents.js asset path present', 'url' => $fbLink];
        }

        $fbqNeedle = $this->firstMatchingNeedle($html, ["fbq('init'", 'fbq("init"']);

        if ($fbqNeedle !== null) {
            $signals[] = [
                'weight' => 40,
                'label' => "inline fbq('init' ...) marker present",
                'snippet' => $this->htmlSnippet($html, $fbqNeedle),
            ];
        }

        return $this->buildResult('Facebook Pixel', version: null, signals: $signals);
    }

    private function detectMicrosoftClarity(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $clarityLink = $this->firstMatchingLinkUrl($result->jsLinks, ['clarity.ms/tag']);

        if ($clarityLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'clarity.ms/tag asset path present', 'url' => $clarityLink];
        }

        $clarityNeedle = $this->firstMatchingNeedle($html, ["clarity('init'", 'clarity("init"']);

        if ($clarityNeedle !== null) {
            $signals[] = [
                'weight' => 35,
                'label' => "inline clarity('init' ...) marker present",
                'snippet' => $this->htmlSnippet($html, $clarityNeedle),
            ];
        }

        return $this->buildResult('Microsoft Clarity', version: null, signals: $signals);
    }

    private function detectGoogleAds(FetchResult $result): TechnologyDetectionResult
    {
        $html = (string) $result->html;
        $signals = [];

        $adsLink = $this->firstMatchingLinkUrl($result->jsLinks, ['googleads.g.doubleclick.net']);

        if ($adsLink !== null) {
            $signals[] = ['weight' => 45, 'label' => 'googleads.g.doubleclick.net asset path present', 'url' => $adsLink];
        }

        $awConfigNeedle = $this->firstMatchingNeedle($html, [
            "gtag('config', 'AW-", 'gtag("config", "AW-',
            "gtag('config','AW-", 'gtag("config","AW-',
        ]);

        if ($awConfigNeedle !== null) {
            $signals[] = [
                'weight' => 40,
                'label' => "inline gtag('config', 'AW-...') marker present",
                'snippet' => $this->htmlSnippet($html, $awConfigNeedle),
            ];
        }

        if (preg_match('/\bAW-[0-9]{6,}\b/', $html, $matches) === 1) {
            $signals[] = ['weight' => 35, 'label' => 'AW- prefixed conversion ID present', 'snippet' => $matches[0]];
        }

        return $this->buildResult('Google Ads', version: null, signals: $signals);
    }

    private function detectCloudflare(FetchResult $result): TechnologyDetectionResult
    {
        $signals = [];

        $cfHeader = $this->headerSnippet($result, 'CF-Ray', 'CF-Cache-Status');

        if ($cfHeader !== null) {
            $signals[] = ['weight' => 50, 'label' => 'CF-Ray / CF-Cache-Status response header present', 'snippet' => $cfHeader];
        }

        $server = $this->headerValue($result, 'Server');

        if ($server !== null && stripos($server, 'cloudflare') !== false) {
            $signals[] = [
                'weight' => 45,
                'label' => "Server response header: \"{$server}\"",
                'snippet' => "Server: {$server}",
            ];
        }

        return $this->buildResult('Cloudflare', version: null, signals: $signals);
    }

    /**
     * NOT a weighted detection like the rest of this class — Server/CDN/
     * Hosting are informational values ("nginx", "Apache", "cloudflare"),
     * not a yes/no confidence judgement, so this simply surfaces the raw
     * Server response header rather than producing a
     * TechnologyDetectionResult. Wired directly onto
     * TechnologyResult::$serverHeader (see that DTO's docblock) rather
     * than into $technologyStack, since it has no detected/confidence
     * concept behind it.
     */
    public function serverInfo(FetchResult $result): ?string
    {
        return $this->headerValue($result, 'Server');
    }

    /**
     * @param  array<int, array{weight: int, label: string, url?: string, snippet?: string}>  $signals
     */
    private function buildResult(string $technology, ?string $version, array $signals): TechnologyDetectionResult
    {
        if ($signals === []) {
            return new TechnologyDetectionResult(
                technology: $technology,
                detected: false,
                version: null,
                confidenceScore: 0,
                detectionMethod: null,
            );
        }

        $confidence = min(self::MAX_CONFIDENCE, array_sum(array_column($signals, 'weight')));
        $method = implode('; ', array_column($signals, 'label'));
        $detected = $confidence >= $this->detectionThreshold;
        $evidence = $detected ? $this->primaryEvidence($signals) : ['url' => null, 'snippet' => null];

        return new TechnologyDetectionResult(
            technology: $technology,
            detected: $detected,
            version: $detected ? $version : null,
            confidenceScore: $confidence,
            detectionMethod: $method,
            evidenceUrl: $evidence['url'],
            evidenceSnippet: $evidence['snippet'],
        );
    }

    /**
     * Picks the strongest evidence behind a technology's detection: the
     * highest-weight signal among those that carry a 'url' or 'snippet'
     * — not simply the first one checked in code — so the reported
     * evidence is always the single most convincing reason the
     * technology was flagged. Signals with no evidence attached (e.g. a
     * boolean-only check that was never instrumented) are skipped
     * entirely rather than winning by default.
     *
     * @param  array<int, array{weight: int, label: string, url?: string, snippet?: string}>  $signals
     * @return array{url: ?string, snippet: ?string}
     */
    private function primaryEvidence(array $signals): array
    {
        $best = null;

        foreach ($signals as $signal) {
            if (! isset($signal['url']) && ! isset($signal['snippet'])) {
                continue;
            }

            if ($best === null || $signal['weight'] > $best['weight']) {
                $best = $signal;
            }
        }

        if ($best === null) {
            return ['url' => null, 'snippet' => null];
        }

        return ['url' => $best['url'] ?? null, 'snippet' => $best['snippet'] ?? null];
    }

    private function extractVersion(string $generatorContent, string $prefix): ?string
    {
        $pattern = '/'.preg_quote($prefix, '/').'\s+([0-9][0-9.]*)/i';

        return preg_match($pattern, $generatorContent, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * Reads a version out of a CDN-hosted script/style URL for the
     * given package name, matching either the npm-CDN "@version" form
     * (unpkg.com/react@18.2.0) or the path-segment form used by
     * cdnjs/jsdelivr (cdnjs.cloudflare.com/ajax/libs/vue/3.4.0/vue.js).
     * Used only for libraries (React, Vue) that have no other public
     * version signal; build-tool bundles are not versioned this way.
     *
     * @param  array<int, CssLink|ScriptLink>  $links
     */
    private function extractVersionFromLinks(array $links, string $packageName): ?string
    {
        $pattern = '/'.preg_quote($packageName, '/').'[@\/]([0-9]+\.[0-9]+(?:\.[0-9]+)?)/i';

        foreach ($links as $link) {
            if (preg_match($pattern, $link->url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * jQuery's CDN filename convention (jquery-3.7.1.min.js) doesn't
     * fit extractVersionFromLinks()'s "package@version" / "package/version"
     * pattern — it's "package-version" — so this is a small dedicated
     * matcher rather than a third case bolted onto that helper.
     *
     * @param  array<int, ScriptLink>  $links
     */
    private function extractJqueryVersionFromLinks(array $links): ?string
    {
        foreach ($links as $link) {
            if (preg_match('/jquery-([0-9]+\.[0-9]+(?:\.[0-9]+)?)(?:\.min)?\.js/i', $link->url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Returns the first needle (case-insensitive) found in $haystack, or
     * null if none match — used both to decide whether a signal fires
     * and, via htmlSnippet(), to build that signal's evidence snippet
     * from the specific needle that actually matched.
     *
     * @param  array<int, string>  $needles
     */
    private function firstMatchingNeedle(string $haystack, array $needles): ?string
    {
        foreach ($needles as $needle) {
            if (stripos($haystack, $needle) !== false) {
                return $needle;
            }
        }

        return null;
    }

    /**
     * Extracts a short, whitespace-collapsed raw excerpt of $html
     * centered on the first occurrence of $needle, for use as a
     * TechnologyDetectionResult's evidenceSnippet.
     */
    private function htmlSnippet(string $html, string $needle, int $context = 30): ?string
    {
        $pos = stripos($html, $needle);

        if ($pos === false) {
            return null;
        }

        $start = max(0, $pos - $context);
        $length = mb_strlen($needle) + $context * 2;
        $snippet = mb_substr($html, $start, $length);
        $snippet = trim((string) preg_replace('/\s+/', ' ', $snippet));

        return $snippet !== '' ? $snippet : null;
    }

    /**
     * Returns the URL of the first link (CSS or JS) whose URL contains
     * any of $needles (case-insensitive), or null if none match —
     * doubles as both the detection check and the signal's evidenceUrl,
     * since the matching URL *is* the evidence.
     *
     * @param  array<int, CssLink|ScriptLink>  $links
     * @param  array<int, string>  $needles
     */
    private function firstMatchingLinkUrl(array $links, array $needles): ?string
    {
        foreach ($links as $link) {
            foreach ($needles as $needle) {
                if (stripos($link->url, $needle) !== false) {
                    return $link->url;
                }
            }
        }

        return null;
    }

    /**
     * Reconstructs a representative <meta name="generator" ...> tag
     * snippet from its already-parsed content attribute, for use as a
     * signal's evidenceSnippet. Reconstructed rather than scraped
     * verbatim from raw HTML, since MetaData only retains the parsed
     * content value, not the tag's original raw markup — this still
     * accurately represents what was matched.
     */
    private function metaGeneratorSnippet(string $generatorContent): string
    {
        return '<meta name="generator" content="'.$generatorContent.'">';
    }

    /**
     * Checks the Set-Cookie response header for a cookie whose name
     * starts with any of the given exact names/prefixes, returning the
     * matched "name=value" segment as evidence (or null if none
     * matched). A single implementation covers both an exact cookie
     * name (e.g. "laravel_session") and a prefix (e.g. "wordpress_")
     * since the trailing [A-Za-z0-9_-]* in the pattern matches zero
     * additional characters just as readily as several, and matches on
     * a word boundary followed by "=" rather than splitting the header
     * on commas, since Set-Cookie's Expires attribute itself contains a
     * comma (e.g. "Expires=Wed, 21 Oct 2026 ...") and would otherwise
     * break a naive split.
     */
    private function cookieSnippet(FetchResult $result, string ...$namesOrPrefixes): ?string
    {
        $cookieHeader = $this->headerValue($result, 'Set-Cookie');

        if ($cookieHeader === null) {
            return null;
        }

        foreach ($namesOrPrefixes as $prefix) {
            $pattern = '/(?:^|[;,\s])('.preg_quote($prefix, '/').'[A-Za-z0-9_-]*=[^;,]*)/i';

            if (preg_match($pattern, $cookieHeader, $matches) === 1) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Returns the first of the given response header names that is
     * present, formatted as "Name: value" evidence — or null if none
     * of them are present.
     */
    private function headerSnippet(FetchResult $result, string ...$names): ?string
    {
        foreach ($names as $name) {
            $value = $this->headerValue($result, $name);

            if ($value !== null) {
                return "{$name}: {$value}";
            }
        }

        return null;
    }

    private function metaGeneratorContent(FetchResult $result): ?string
    {
        foreach ($result->meta?->raw ?? [] as $tag) {
            if (isset($tag['name']) && strcasecmp((string) $tag['name'], 'generator') === 0) {
                $content = trim((string) ($tag['content'] ?? ''));

                return $content !== '' ? $content : null;
            }
        }

        return null;
    }

    private function htmlMetaCsrfSnippet(string $html): ?string
    {
        return preg_match('/<meta\s[^>]*name=["\']csrf-token["\'][^>]*>/i', $html, $matches) === 1 ? $matches[0] : null;
    }

    /**
     * Returns the raw <body ...> opening-tag snippet when its class
     * attribute includes $class, or null otherwise.
     */
    private function htmlBodyClassSnippet(string $html, string $class): ?string
    {
        if (preg_match('/<body\b[^>]*class=["\']([^"\']*)["\'][^>]*>/i', $html, $matches) !== 1) {
            return null;
        }

        $classes = preg_split('/\s+/', trim($matches[1])) ?: [];

        return in_array($class, $classes, true) ? $matches[0] : null;
    }

    /**
     * A rough, deliberately simple density check: Tailwind utility
     * classes are highly repetitive short tokens (bg-, text-, flex,
     * grid-, px-, py-, w-, h-, rounded-, shadow-, etc.) — this counts
     * how many distinct class="..." attributes contain at least one
     * such token and treats five or more as "dominated by" utility
     * classes, since a handful of coincidental matches (e.g. a single
     * custom ".flex" class) shouldn't alone suggest Tailwind is in use.
     * Returns the first matching class="..." attribute as evidence when
     * the density threshold is met, or null otherwise.
     */
    private function htmlDenseUtilityClassSnippet(string $html): ?string
    {
        if (preg_match_all('/class=["\']([^"\']*)["\']/i', $html, $matches) === false) {
            return null;
        }

        $utilityPattern = '/\b(?:bg|text|flex|grid|px|py|mx|my|w|h|rounded|shadow|border|justify|items)-[a-z0-9]+\b|\bflex\b|\bgrid\b/i';

        $matchingAttributeCount = 0;
        $firstMatch = null;

        foreach ($matches[1] as $classAttribute) {
            if (preg_match($utilityPattern, $classAttribute) === 1) {
                $matchingAttributeCount++;
                $firstMatch ??= 'class="'.$classAttribute.'"';
            }
        }

        return $matchingAttributeCount >= 5 ? $firstMatch : null;
    }

    private function headerValue(FetchResult $result, string $name): ?string
    {
        foreach ($result->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }
}
