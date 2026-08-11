<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Fetching\DTO\CssLink;
use App\Audit\Fetching\DTO\DiscoveredResource;
use App\Audit\Fetching\DTO\FetchResult;
use App\Audit\Fetching\DTO\Heading;
use App\Audit\Fetching\DTO\ImageAsset;
use App\Audit\Fetching\DTO\MetaData;
use App\Audit\Fetching\DTO\SchemaBlock;
use App\Audit\Fetching\DTO\ScriptLink;

/**
 * Builds a FetchResult that passes every analyzer's checks by default —
 * HTTPS, full security headers, a complete <html> document with a
 * <nav>, a hero heading, a form with labels, alt text on every image,
 * and healthy meta tags — so each analyzer test only needs to override
 * the one or two fields it's actually exercising, the same pattern
 * SeoAnalyzerServiceTest::page() already uses for CrawledPage.
 */
final class FetchResultFactory
{
    /**
     * @param  array<string, string>  $headers  merged over the passing defaults
     * @param  array<int, string>  $errors
     */
    public static function make(
        string $url = 'https://example.com/',
        bool $success = true,
        ?int $statusCode = 200,
        array $headers = [],
        ?string $html = null,
        ?MetaData $meta = null,
        int $wordCount = 600,
        array $errors = [],
        bool $includeDefaultSecurityHeaders = true,
        ?array $cssLinks = null,
        ?array $jsLinks = null,
        ?array $images = null,
    ): FetchResult {
        $defaultHeaders = $includeDefaultSecurityHeaders ? [
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'",
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-XSS-Protection' => '1; mode=block',
            'Set-Cookie' => 'session=abc123; Secure; HttpOnly; SameSite=Strict',
        ] : [];

        return new FetchResult(
            url: $url,
            success: $success,
            finalUrl: $success ? $url : null,
            statusCode: $success ? $statusCode : null,
            headers: [...$defaultHeaders, ...$headers],
            html: $html ?? self::passingHtml(),
            meta: $meta ?? self::passingMeta(),
            cssLinks: $cssLinks ?? [new CssLink(url: 'https://example.com/app.css')],
            jsLinks: $jsLinks ?? [new ScriptLink(url: 'https://example.com/app.js', defer: true)],
            images: $images ?? [new ImageAsset(url: 'https://example.com/hero.png', alt: 'A descriptive hero image', width: 800, height: 400)],
            fonts: [],
            anchors: [],
            headings: [new Heading(level: 1, text: 'Welcome to Example')],
            schema: [new SchemaBlock(types: ['Organization'], data: ['@type' => 'Organization'], valid: true)],
            wordCount: $wordCount,
            robotsTxt: DiscoveredResource::notFound(),
            sitemap: DiscoveredResource::notFound(),
            rssFeeds: [],
            manifest: DiscoveredResource::notFound(),
            redirectChain: [],
            responseTimeMs: 250,
            errors: $errors,
            fetchedAt: '2026-01-01T00:00:00+00:00',
        );
    }

    private static function passingMeta(): MetaData
    {
        return new MetaData(
            title: 'A Well Optimized Example Page Title',
            description: 'This is a meta description that sits comfortably within the recommended length range for search results.',
            keywords: 'example, widgets',
            canonical: 'https://example.com/',
            robots: 'index, follow',
            viewport: 'width=device-width, initial-scale=1',
            charset: 'UTF-8',
            openGraph: ['og:title' => 'Example', 'og:description' => 'Example', 'og:image' => 'https://example.com/og.png'],
            twitter: ['twitter:card' => 'summary_large_image'],
            raw: [],
        );
    }

    private static function passingHtml(): string
    {
        return <<<'HTML'
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>A Well Optimized Example Page Title</title>
            </head>
            <body>
                <nav aria-label="Primary">
                    <a href="/">Home</a>
                    <a href="/about">About</a>
                    <a href="/contact">Contact</a>
                </nav>
                <header>
                    <h1>Welcome to Example</h1>
                    <p>A clear, compelling hero subheading that explains the value proposition.</p>
                    <a href="/signup" class="btn btn-primary">Get Started</a>
                </header>
                <main>
                    <img src="https://example.com/hero.png" alt="A descriptive hero image" width="800" height="400">
                    <form action="/newsletter" method="post">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                    <p>Plenty of substantive, original body copy describing what this page is about, well beyond
                    any thin-content threshold, written in clear and grammatically correct sentences. Visitors can
                    browse curated widgets, compare pricing plans, and read customer stories from small businesses
                    that switched to a faster, more reliable workflow. Our support team publishes weekly guides
                    covering setup, integrations, billing, and troubleshooting, so newcomers can get comfortable
                    without waiting on a reply. Every order ships within two business days, and returns are
                    accepted for a full thirty days, no questions asked. Longtime customers often mention how the
                    onboarding checklist saved them hours during their first week, and how responsive the billing
                    department has been whenever a plan needed adjusting.</p>
                </main>
                <footer>
                    <p>&copy; 2026 Example Inc.</p>
                </footer>
            </body>
            </html>
            HTML;
    }
}
