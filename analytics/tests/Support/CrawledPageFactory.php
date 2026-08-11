<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Crawler\DTO\CrawledPage;
use App\Audit\Fetching\DTO\AnchorLink;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\SchemaBlock;

/**
 * Builds a minimal, empty-by-default CrawledPage — every field a Lead
 * Intelligence analyzer (BusinessSignalsDetector, ContactInfoExtractor,
 * ReviewPresenceScanner) reads is opt-in via a constructor argument, so
 * each test only supplies the one or two signals it's actually
 * exercising rather than a whole fetched page. Mirrors the shape of
 * FetchResultFactory::make() and SeoAnalyzerServiceTest::page(), the
 * two existing CrawledPage/FetchResult builders in this test suite.
 */
final class CrawledPageFactory
{
    /**
     * @param  array<int, AnchorLink>  $anchors
     * @param  array<int, Heading>  $headings
     * @param  array<int, SchemaBlock>  $schema
     * @param  array<int, string>  $mailtoLinks
     * @param  array<int, string>  $telLinks
     */
    public static function make(
        string $url = 'https://example.com/',
        ?string $title = null,
        array $anchors = [],
        array $headings = [],
        array $schema = [],
        array $mailtoLinks = [],
        array $telLinks = [],
        int $depth = 0,
    ): CrawledPage {
        return new CrawledPage(
            url: $url,
            depth: $depth,
            success: true,
            finalUrl: $url,
            statusCode: 200,
            redirectChain: [],
            meta: null,
            title: $title,
            canonical: null,
            noIndex: false,
            noFollow: false,
            anchors: $anchors,
            internalLinkUrls: [],
            externalLinkUrls: [],
            images: [],
            cssAssets: [],
            jsAssets: [],
            fontAssets: [],
            headings: $headings,
            schema: $schema,
            wordCount: 0,
            responseTimeMs: 100,
            errors: [],
            mailtoLinks: $mailtoLinks,
            telLinks: $telLinks,
        );
    }

    public static function anchor(string $url, ?string $text = null): AnchorLink
    {
        return new AnchorLink(url: $url, text: $text, rel: null, nofollow: false);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function schemaBlock(array $types, array $data, bool $valid = true): SchemaBlock
    {
        return new SchemaBlock(types: $types, data: $data, valid: $valid);
    }
}
