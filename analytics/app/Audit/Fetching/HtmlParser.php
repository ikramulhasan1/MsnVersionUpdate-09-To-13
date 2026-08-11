<?php

declare(strict_types=1);

namespace App\Audit\Fetching;

use App\Audit\Fetching\Contracts\HtmlParserInterface;
use App\Audit\Fetching\DTO\AnchorLink;
use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\FontAsset;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\MetaData;
use App\Audit\Fetching\DTO\ParsedHtml;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\Fetching\DTO\ScriptLink;

final class HtmlParser implements HtmlParserInterface
{
    /**
     * Tags whose text content is never visible page copy. Stripped out of
     * the body clone before counting words, and before extracting heading
     * text (a <script> block that happens to sit inside an <h2> for some
     * tracking-pixel reason shouldn't count as heading text either).
     */
    private const array NON_CONTENT_TAGS = ['script', 'style', 'noscript', 'template'];

    public function parse(string $html, string $baseUrl): ParsedHtml
    {
        if (trim($html) === '') {
            return ParsedHtml::empty();
        }

        $dom = $this->loadDocument($html);
        $xpath = new \DOMXPath($dom);

        return new ParsedHtml(
            meta: $this->parseMeta($xpath, $baseUrl),
            cssLinks: $this->parseCssLinks($xpath, $baseUrl),
            jsLinks: $this->parseScriptLinks($xpath, $baseUrl),
            images: $this->parseImages($xpath, $baseUrl),
            fonts: $this->parseFonts($xpath, $baseUrl),
            manifestUrl: $this->parseManifest($xpath, $baseUrl),
            feedUrls: $this->parseFeeds($xpath, $baseUrl),
            anchors: $this->parseAnchors($xpath, $baseUrl),
            headings: $this->parseHeadings($xpath),
            schema: $this->parseSchema($xpath),
            wordCount: $this->countWords($xpath),
            mailtoLinks: $this->parseSchemeLinks($xpath, 'mailto:'),
            telLinks: $this->parseSchemeLinks($xpath, 'tel:'),
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

    private function parseMeta(\DOMXPath $xpath, string $baseUrl): MetaData
    {
        $title = trim((string) $this->firstText($xpath, '//title'));
        $canonical = $this->firstAttr($xpath, '//link[@rel="canonical"]/@href');

        $description = null;
        $keywords = null;
        $robots = null;
        $viewport = null;
        $charset = null;
        $openGraph = [];
        $twitter = [];
        $raw = [];

        foreach ($xpath->query('//meta') ?: [] as $node) {
            /** @var \DOMElement $node */
            $name = $node->getAttribute('name') ?: null;
            $property = $node->getAttribute('property') ?: null;
            $content = $node->getAttribute('content') ?: null;

            if ($node->hasAttribute('charset')) {
                $charset = $node->getAttribute('charset') ?: $charset;
            }

            $raw[] = ['name' => $name, 'property' => $property, 'content' => $content];

            if ($name === null && $property === null) {
                continue;
            }

            $key = strtolower((string) ($name ?? $property));

            match (true) {
                $key === 'description' => $description = $content ?? $description,
                $key === 'keywords' => $keywords = $content ?? $keywords,
                $key === 'robots' => $robots = $content ?? $robots,
                $key === 'viewport' => $viewport = $content ?? $viewport,
                str_starts_with($key, 'og:') && $content !== null => $openGraph[$key] = $content,
                str_starts_with($key, 'twitter:') && $content !== null => $twitter[$key] = $content,
                default => null,
            };
        }

        return new MetaData(
            title: $title !== '' ? $title : null,
            description: $description,
            keywords: $keywords,
            canonical: $canonical !== null && $canonical !== '' ? UrlResolver::resolve($baseUrl, $canonical) : null,
            robots: $robots,
            viewport: $viewport,
            charset: $charset,
            openGraph: $openGraph,
            twitter: $twitter,
            raw: $raw,
        );
    }

    /**
     * @return array<int, CssLink>
     */
    private function parseCssLinks(\DOMXPath $xpath, string $baseUrl): array
    {
        $links = [];

        foreach ($xpath->query('//link[@rel="stylesheet"]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $href = $node->getAttribute('href');
            $resolved = $href !== '' ? UrlResolver::resolve($baseUrl, $href) : null;

            if ($resolved === null) {
                continue;
            }

            $links[] = new CssLink(
                url: $resolved,
                rel: $node->getAttribute('rel') ?: 'stylesheet',
                media: $node->getAttribute('media') ?: null,
            );
        }

        return $links;
    }

    /**
     * @return array<int, ScriptLink>
     */
    private function parseScriptLinks(\DOMXPath $xpath, string $baseUrl): array
    {
        $links = [];

        foreach ($xpath->query('//script[@src]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $src = $node->getAttribute('src');
            $resolved = $src !== '' ? UrlResolver::resolve($baseUrl, $src) : null;

            if ($resolved === null) {
                continue;
            }

            $links[] = new ScriptLink(
                url: $resolved,
                type: $node->getAttribute('type') ?: null,
                async: $node->hasAttribute('async'),
                defer: $node->hasAttribute('defer'),
            );
        }

        return $links;
    }

    /**
     * @return array<int, ImageAsset>
     */
    private function parseImages(\DOMXPath $xpath, string $baseUrl): array
    {
        $images = [];

        foreach ($xpath->query('//img[@src]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $src = $node->getAttribute('src');
            $resolved = $src !== '' ? UrlResolver::resolve($baseUrl, $src) : null;

            if ($resolved === null) {
                continue;
            }

            $width = $node->getAttribute('width');
            $height = $node->getAttribute('height');

            $images[] = new ImageAsset(
                url: $resolved,
                alt: $node->hasAttribute('alt') ? $node->getAttribute('alt') : null,
                width: ctype_digit($width) ? (int) $width : null,
                height: ctype_digit($height) ? (int) $height : null,
            );
        }

        return $images;
    }

    /**
     * @return array<int, FontAsset>
     */
    private function parseFonts(\DOMXPath $xpath, string $baseUrl): array
    {
        $fonts = [];

        foreach ($xpath->query('//link[@rel="preload"][@as="font"]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $href = $node->getAttribute('href');
            $resolved = $href !== '' ? UrlResolver::resolve($baseUrl, $href) : null;

            if ($resolved === null) {
                continue;
            }

            $type = $node->getAttribute('type');
            $format = $type !== '' ? (explode('/', $type)[1] ?? null) : $this->guessFormatFromUrl($resolved);

            $fonts[] = new FontAsset(url: $resolved, format: $format, source: 'preload');
        }

        return $fonts;
    }

    private function guessFormatFromUrl(string $url): ?string
    {
        $extension = strtolower((string) pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot'], true) ? $extension : null;
    }

    private function parseManifest(\DOMXPath $xpath, string $baseUrl): ?string
    {
        $href = $this->firstAttr($xpath, '//link[@rel="manifest"]/@href');

        return $href !== null && $href !== '' ? UrlResolver::resolve($baseUrl, $href) : null;
    }

    /**
     * @return array<int, string>
     */
    private function parseFeeds(\DOMXPath $xpath, string $baseUrl): array
    {
        $query = '//link[@rel="alternate"][@type="application/rss+xml" or @type="application/atom+xml"]';
        $urls = [];

        foreach ($xpath->query($query) ?: [] as $node) {
            /** @var \DOMElement $node */
            $href = $node->getAttribute('href');
            $resolved = $href !== '' ? UrlResolver::resolve($baseUrl, $href) : null;

            if ($resolved !== null && ! in_array($resolved, $urls, true)) {
                $urls[] = $resolved;
            }
        }

        return $urls;
    }

    /**
     * Extract every <a href> on the page, resolved to absolute URLs.
     * De-duplicated by resolved URL (first occurrence wins) so a page that
     * links the same target ten times in a nav menu doesn't get counted
     * ten times by the crawler. Anchors that aren't fetchable web resources
     * (#fragments, mailto:, javascript:, etc.) are filtered out already by
     * UrlResolver::resolve().
     *
     * @return array<int, AnchorLink>
     */
    private function parseAnchors(\DOMXPath $xpath, string $baseUrl): array
    {
        $anchors = [];
        $seen = [];

        foreach ($xpath->query('//a[@href]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $href = $node->getAttribute('href');
            $resolved = $href !== '' ? UrlResolver::resolve($baseUrl, $href) : null;

            if ($resolved === null || isset($seen[$resolved])) {
                continue;
            }

            $seen[$resolved] = true;

            $rel = $node->getAttribute('rel') ?: null;
            $text = trim($node->textContent);

            $anchors[] = new AnchorLink(
                url: $resolved,
                text: $text !== '' ? $text : null,
                rel: $rel,
                nofollow: $rel !== null && str_contains(strtolower($rel), 'nofollow'),
            );
        }

        return $anchors;
    }

    /**
     * Captures raw href values for a specific non-web URI scheme
     * (mailto:, tel:) that UrlResolver::resolve() deliberately discards
     * for parseAnchors() above, since they aren't fetchable web
     * resources for crawling purposes — but they're the single most
     * reliable structured source of a site's published contact email/
     * phone (far more reliable than scanning body prose for
     * email-shaped or phone-shaped text, which produces false
     * positives like image filenames or example numbers in copy).
     * Kept as its own pass rather than folded into parseAnchors() so
     * that method's contract (only fetchable web resources) stays
     * unchanged for every existing caller.
     *
     * Returns the raw href value after the scheme prefix, decoded and
     * trimmed of any query string (mailto: supports ?subject=... which
     * isn't part of the address itself), deduplicated, in document
     * order.
     *
     * @return array<int, string>
     */
    private function parseSchemeLinks(\DOMXPath $xpath, string $scheme): array
    {
        $values = [];
        $seen = [];

        foreach ($xpath->query('//a[@href]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $href = trim($node->getAttribute('href'));

            if (stripos($href, $scheme) !== 0) {
                continue;
            }

            $value = substr($href, strlen($scheme));
            $value = explode('?', $value, 2)[0];
            $value = trim(rawurldecode($value));

            if ($value === '' || isset($seen[$value])) {
                continue;
            }

            $seen[$value] = true;
            $values[] = $value;
        }

        return $values;
    }

    /**
     * Extract every H1-H6 on the page, in document order. Uses an XPath
     * union so the result set comes back already interleaved in the order
     * the headings actually appear, rather than all the H1s, then all the
     * H2s, etc.
     *
     * @return array<int, Heading>
     */
    private function parseHeadings(\DOMXPath $xpath): array
    {
        $headings = [];

        $query = '//h1 | //h2 | //h3 | //h4 | //h5 | //h6';

        foreach ($xpath->query($query) ?: [] as $node) {
            /** @var \DOMElement $node */
            $level = (int) substr($node->nodeName, 1);
            $text = $this->normalizeWhitespace($node->textContent);

            $headings[] = new Heading(level: $level, text: $text);
        }

        return $headings;
    }

    /**
     * Extract every application/ld+json block on the page. A block that
     * fails to decode is still reported (valid: false) rather than
     * silently dropped, so the SEO analyzer can flag broken structured
     * data instead of just seeing an empty schema list.
     *
     * @return array<int, SchemaBlock>
     */
    private function parseSchema(\DOMXPath $xpath): array
    {
        $blocks = [];

        foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $raw = trim($node->textContent);

            if ($raw === '') {
                continue;
            }

            try {
                $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $blocks[] = new SchemaBlock(types: [], data: null, valid: false);

                continue;
            }

            if (! is_array($decoded)) {
                // Technically valid JSON (e.g. a bare string or number) but
                // not a usable JSON-LD payload.
                $blocks[] = new SchemaBlock(types: [], data: null, valid: false);

                continue;
            }

            $types = [];
            $this->collectSchemaTypes($decoded, $types);

            $blocks[] = new SchemaBlock(types: $types, data: $decoded, valid: true);
        }

        return $blocks;
    }

    /**
     * Recursively collects every distinct "@type" in a decoded JSON-LD
     * payload. Handles three shapes that all show up in the wild: a single
     * object with "@type", a top-level array of such objects, and an
     * "@graph" array nesting either of those — including multiple levels
     * deep, since a @graph entry can itself contain another @graph.
     *
     * @param array<mixed> $node
     * @param array<int, string> $types
     */
    private function collectSchemaTypes(array $node, array &$types): void
    {
        if (array_is_list($node)) {
            foreach ($node as $entry) {
                if (is_array($entry)) {
                    $this->collectSchemaTypes($entry, $types);
                }
            }

            return;
        }

        if (isset($node['@type'])) {
            foreach ((array) $node['@type'] as $type) {
                if (is_string($type) && $type !== '' && ! in_array($type, $types, true)) {
                    $types[] = $type;
                }
            }
        }

        if (isset($node['@graph']) && is_array($node['@graph'])) {
            $this->collectSchemaTypes($node['@graph'], $types);
        }
    }

    /**
     * Count words in the page's visible body copy, for the thin-content
     * check. Operates on a detached clone of the <body> subtree (never the
     * <head>, so a <style> or <title> block can't leak in) with every
     * script/style/noscript/template node pruned out of the clone before
     * counting — so inline analytics snippets or CSS sitting inside the
     * body don't inflate the count either.
     */
    private function countWords(\DOMXPath $xpath): int
    {
        $bodyNodes = $xpath->query('//body');

        if ($bodyNodes === false || $bodyNodes->length === 0) {
            return 0;
        }

        $body = $bodyNodes->item(0);

        if (! $body instanceof \DOMElement) {
            return 0;
        }

        // Cloned within the *same* document (no importNode into a fresh
        // DOMDocument) so there's no cross-document reimport edge case to
        // worry about — the clone is simply detached from the tree.
        $clone = $body->cloneNode(true);

        if (! $clone instanceof \DOMElement) {
            return 0;
        }

        $nonContentQuery = implode(' | ', array_map(
            static fn (string $tag): string => ".//{$tag}",
            self::NON_CONTENT_TAGS,
        ));

        // Query against the detached clone via its context node — the
        // clone belongs to the same DOMDocument, so the existing $xpath
        // instance can query it directly, and removing matches here never
        // touches the live $dom the rest of the parser is still reading.
        $nonContentNodes = $xpath->query($nonContentQuery, $clone) ?: [];

        foreach (iterator_to_array($nonContentNodes) as $node) {
            $node->parentNode?->removeChild($node);
        }

        $text = $this->normalizeWhitespace($clone->textContent);

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text) ?: []);
    }

    private function normalizeWhitespace(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function firstText(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);

        return $nodes !== false && $nodes->length > 0 ? $nodes->item(0)?->textContent : null;
    }

    private function firstAttr(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);

        return $nodes !== false && $nodes->length > 0 ? $nodes->item(0)?->nodeValue : null;
    }
}
