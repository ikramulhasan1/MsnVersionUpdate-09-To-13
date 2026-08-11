<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Fetching;

use App\Audit\Fetching\DTO\ParsedHtml;
use App\Audit\Fetching\HtmlParser;
use PHPUnit\Framework\TestCase;

final class HtmlParserSeoExtractionTest extends TestCase
{
    private function parse(string $html): ParsedHtml
    {
        return (new HtmlParser())->parse($html, 'https://example.com/page');
    }

    public function test_extracts_headings_in_document_order_with_level_and_text(): void
    {
        $html = <<<'HTML'
            <html><head><title>T</title></head>
            <body>
                <h1>Main Title</h1>
                <p>Intro</p>
                <h2>First Section</h2>
                <h3>Sub Section</h3>
                <h2>Second Section</h2>
            </body></html>
            HTML;

        $headings = $this->parse($html)->headings;

        $this->assertCount(4, $headings);
        $this->assertSame([1, 2, 3, 2], array_map(static fn ($h) => $h->level, $headings));
        $this->assertSame(
            ['Main Title', 'First Section', 'Sub Section', 'Second Section'],
            array_map(static fn ($h) => $h->text, $headings),
        );
    }

    public function test_normalizes_whitespace_in_heading_text(): void
    {
        $html = '<html><body><h1>  Hello
            World  </h1></body></html>';

        $headings = $this->parse($html)->headings;

        $this->assertSame('Hello World', $headings[0]->text);
    }

    public function test_parses_single_json_ld_block(): void
    {
        $html = <<<'HTML'
            <html><head>
                <script type="application/ld+json">
                {"@context": "https://schema.org", "@type": "Article", "headline": "Hi"}
                </script>
            </head><body></body></html>
            HTML;

        $schema = $this->parse($html)->schema;

        $this->assertCount(1, $schema);
        $this->assertTrue($schema[0]->valid);
        $this->assertSame(['Article'], $schema[0]->types);
    }

    public function test_parses_graph_nested_type_arrays(): void
    {
        $html = <<<'HTML'
            <html><head>
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@graph": [
                        {"@type": "Organization", "name": "Acme"},
                        {"@type": ["Product", "Offer"], "name": "Widget"},
                        {"@graph": [{"@type": "WebPage"}]}
                    ]
                }
                </script>
            </head><body></body></html>
            HTML;

        $schema = $this->parse($html)->schema;

        $this->assertCount(1, $schema);
        $this->assertTrue($schema[0]->valid);
        $this->assertEqualsCanonicalizing(
            ['Organization', 'Product', 'Offer', 'WebPage'],
            $schema[0]->types,
        );
    }

    public function test_flags_invalid_json_ld_without_dropping_it(): void
    {
        $html = <<<'HTML'
            <html><head>
                <script type="application/ld+json">{ not valid json </script>
            </head><body></body></html>
            HTML;

        $schema = $this->parse($html)->schema;

        $this->assertCount(1, $schema);
        $this->assertFalse($schema[0]->valid);
        $this->assertSame([], $schema[0]->types);
        $this->assertNull($schema[0]->data);
    }

    public function test_counts_words_in_body_only_ignoring_head_style_and_body_script(): void
    {
        // Five real words in the body. A <style> block sits in <head> (must
        // never be counted at all) and a <script> block sits in <body>
        // (must be stripped before counting, not counted as words).
        $html = <<<'HTML'
            <html>
            <head>
                <title>Test</title>
                <style>.a{color:red}</style>
            </head>
            <body>
                <p>One two three four five</p>
                <script>var shouldNotCount = "nope nope nope";</script>
            </body>
            </html>
            HTML;

        $this->assertSame(5, $this->parse($html)->wordCount);
    }

    public function test_word_count_is_zero_for_empty_body(): void
    {
        $html = '<html><head><title>Empty</title></head><body></body></html>';

        $this->assertSame(0, $this->parse($html)->wordCount);
    }

    public function test_word_count_ignores_noscript_and_template_content(): void
    {
        $html = <<<'HTML'
            <html><body>
                <p>Real content here</p>
                <noscript>Enable JavaScript to continue please</noscript>
                <template><p>Not rendered content either</p></template>
            </body></html>
            HTML;

        $this->assertSame(3, $this->parse($html)->wordCount);
    }
}
