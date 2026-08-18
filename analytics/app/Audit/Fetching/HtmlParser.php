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
            headings: $this->parseHeadings($xpath, $baseUrl),
            schema: $this->parseSchema($xpath, $baseUrl),
            wordCount: $this->countWords($xpath),
            mailtoLinks: $this->parseSchemeLinks($xpath, 'mailto:'),
            telLinks: $this->parseSchemeLinks($xpath, 'tel:'),
            // Phase M5 — see ParsedHtml::$plainTextEmails's own docblock
            // for the real gap this closes: a real, published business
            // email with no mailto: link was invisible to this whole
            // pipeline until now.
            plainTextEmails: $this->parsePlainTextEmails($xpath),
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
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
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
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
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
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
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

            $fonts[] = new FontAsset(
                url: $resolved,
                format: $format,
                source: 'preload',
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
            );
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
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
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
    private function parseHeadings(\DOMXPath $xpath, string $baseUrl): array
    {
        $headings = [];

        $query = '//h1 | //h2 | //h3 | //h4 | //h5 | //h6';

        foreach ($xpath->query($query) ?: [] as $node) {
            /** @var \DOMElement $node */
            $level = (int) substr($node->nodeName, 1);
            $text = $this->normalizeWhitespace($node->textContent);

            $headings[] = new Heading(
                level: $level,
                text: $text,
                pageUrl: $baseUrl,
                domPath: $this->buildDomPath($node),
            );
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
    private function parseSchema(\DOMXPath $xpath, string $baseUrl): array
    {
        $blocks = [];

        foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $node) {
            /** @var \DOMElement $node */
            $raw = trim($node->textContent);

            if ($raw === '') {
                continue;
            }

            $domPath = $this->buildDomPath($node);

            try {
                $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $blocks[] = new SchemaBlock(types: [], data: null, valid: false, pageUrl: $baseUrl, domPath: $domPath);

                continue;
            }

            if (! is_array($decoded)) {
                // Technically valid JSON (e.g. a bare string or number) but
                // not a usable JSON-LD payload.
                $blocks[] = new SchemaBlock(types: [], data: null, valid: false, pageUrl: $baseUrl, domPath: $domPath);

                continue;
            }

            $types = [];
            $this->collectSchemaTypes($decoded, $types);

            $blocks[] = new SchemaBlock(types: $types, data: $decoded, valid: true, pageUrl: $baseUrl, domPath: $domPath);
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

    /**
     * PRODUCTION INCIDENT (Phase M5) — read before removing this method
     * again: App\Audit\Contacts\ContactInfoExtractor used to read
     * emails ONLY from mailto: links (see that class's own now-updated
     * docblock for the reasoning that originally justified this — real
     * false positives from prose-scanning image filenames/example
     * numbers). In practice, a real, common pattern defeated that
     * assumption outright: a site whose ONLY mailto: link is a generic
     * personal address (a founder's own Gmail in a footer, say) while
     * the REAL business contact address is published as plain,
     * unlinked text elsewhere on the page (e.g. "Contact us:
     * info@example.com" with no <a href="mailto:..."> around it) — the
     * mailto:-only pipeline would find the Gmail address and MISS the
     * actual business email entirely, the exact opposite of what a
     * lead-generation tool needs.
     *
     * This closes that gap with a real (not prose-guessed) regex scan
     * of the page's own VISIBLE text — same clean-text extraction
     * countWords() above already uses (a detached body clone with
     * NON_CONTENT_TAGS removed, so <script>/<style>/<noscript>/<template>
     * text — the actual source of the false-positive risk the old
     * docblock warned about, e.g. a filename embedded in an inline
     * script — never reaches this regex at all). The false-positive
     * concern that originally ruled this out (image filenames, example
     * numbers) applies far less to EMAIL addresses specifically than it
     * would to phone numbers: a string matching a strict
     * local-part@domain.tld shape in genuinely visible body text is
     * overwhelmingly likely to be a real, intentionally-published email
     * address, not incidental noise — phone-shaped numbers remain
     * mailto:/tel:-link-only, unchanged, since THAT false-positive risk
     * (a price, a date, a product SKU) is real and unchanged by this
     * fix.
     *
     * @return array<int, string> lowercased, deduplicated, in document order
     */
    private function parsePlainTextEmails(\DOMXPath $xpath): array
    {
        $bodyNodes = $xpath->query('//body');

        if ($bodyNodes === false || $bodyNodes->length === 0) {
            return [];
        }

        $body = $bodyNodes->item(0);

        if (! $body instanceof \DOMElement) {
            return [];
        }

        $clone = $body->cloneNode(true);

        if (! $clone instanceof \DOMElement) {
            return [];
        }

        $nonContentQuery = implode(' | ', array_map(
            static fn (string $tag): string => ".//{$tag}",
            self::NON_CONTENT_TAGS,
        ));

        $nonContentNodes = $xpath->query($nonContentQuery, $clone) ?: [];

        foreach (iterator_to_array($nonContentNodes) as $node) {
            $node->parentNode?->removeChild($node);
        }

        $text = $clone->textContent;

        // A conservative, standard email shape — deliberately not
        // RFC 5322's full grammar (which accepts many rarely-real-world
        // forms that would only increase false-positive risk here);
        // this is the same practical pattern most real-world email
        // extraction settles on.
        preg_match_all('/[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+/', $text, $matches);

        $emails = [];
        $seen = [];

        foreach ($matches[0] as $match) {
            $normalized = strtolower($match);

            if (isset($seen[$normalized]) || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
                continue;
            }

            $seen[$normalized] = true;
            $emails[] = $normalized;
        }

        return $emails;
    }

    /**
     * Builds a readable CSS-selector-style path from the document root down
     * to the given element, e.g. "body > header > nav > a:nth-child(2)".
     * Each segment prefers a stable identifier over a positional one: an
     * `id` wins outright (assumed unique), then the element's first class,
     * and only when neither is present does it fall back to an
     * `:nth-child(n)` position among the element's siblings. This keeps
     * paths short and human-readable for well-structured markup while still
     * producing a deterministic, disambiguating locator for plain markup.
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