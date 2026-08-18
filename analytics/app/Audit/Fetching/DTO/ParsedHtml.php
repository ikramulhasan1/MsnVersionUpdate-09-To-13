<?php

declare(strict_types=1);

namespace App\Audit\Fetching\DTO;

/**
 * Pure parsing output — no network calls happened to produce this. The
 * fetcher service turns these hints (manifestUrl, feedUrls) into verified
 * DiscoveredResource probes.
 */
final readonly class ParsedHtml
{
    /**
     * @param array<int, CssLink> $cssLinks
     * @param array<int, ScriptLink> $jsLinks
     * @param array<int, ImageAsset> $images
     * @param array<int, FontAsset> $fonts
     * @param array<int, string> $feedUrls
     * @param array<int, AnchorLink> $anchors
     * @param array<int, Heading> $headings every H1-H6 on the page, in document order
     * @param array<int, SchemaBlock> $schema every application/ld+json block on the page
     * @param array<int, string> $mailtoLinks raw addresses from every mailto: link on the
     *        page (deduplicated, no "mailto:" prefix or ?subject= query string)
     * @param array<int, string> $telLinks raw numbers from every tel: link on the page
     *        (deduplicated, no "tel:" prefix, in whatever format the link used)
     */
    public function __construct(
        public MetaData $meta,
        public array $cssLinks,
        public array $jsLinks,
        public array $images,
        public array $fonts,
        public ?string $manifestUrl,
        public array $feedUrls,
        public array $anchors,
        public array $headings,
        public array $schema,
        public int $wordCount,
        public array $mailtoLinks = [],
        public array $telLinks = [],
        /**
         * Phase M5 — see HtmlParser::parsePlainTextEmails()'s own
         * docblock for the real gap this closes: a real, published
         * business email with no mailto: link around it (plain text
         * only) was invisible to App\Audit\Contacts\ContactInfoExtractor
         * until now. Lowercased, deduplicated, in document order — the
         * same shape $mailtoLinks already uses.
         *
         * @var array<int, string>
         */
        public array $plainTextEmails = [],
    ) {
    }

    public static function empty(): self
    {
        return new self(
            meta: MetaData::empty(),
            cssLinks: [],
            jsLinks: [],
            images: [],
            fonts: [],
            manifestUrl: null,
            feedUrls: [],
            anchors: [],
            headings: [],
            schema: [],
            wordCount: 0,
            mailtoLinks: [],
            telLinks: [],
            plainTextEmails: [],
        );
    }
}