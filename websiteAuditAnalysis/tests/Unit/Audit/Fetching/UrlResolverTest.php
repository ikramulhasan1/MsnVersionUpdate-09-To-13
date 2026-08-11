<?php

declare(strict_types=1);

namespace Tests\Unit\Audit\Fetching;

use App\Audit\Fetching\UrlResolver;
use PHPUnit\Framework\TestCase;

final class UrlResolverTest extends TestCase
{
    public function test_leaves_absolute_urls_unchanged(): void
    {
        $this->assertSame(
            'https://cdn.example.com/lib.css',
            UrlResolver::resolve('https://example.com/page', 'https://cdn.example.com/lib.css'),
        );
    }

    public function test_resolves_root_relative_paths(): void
    {
        $this->assertSame(
            'https://example.com/css/app.css',
            UrlResolver::resolve('https://example.com/blog/post-1', '/css/app.css'),
        );
    }

    public function test_resolves_document_relative_paths_against_directory(): void
    {
        $this->assertSame(
            'https://example.com/blog/images/logo.png',
            UrlResolver::resolve('https://example.com/blog/post-1.html', 'images/logo.png'),
        );
    }

    public function test_resolves_parent_directory_traversal(): void
    {
        $this->assertSame(
            'https://example.com/assets/logo.png',
            UrlResolver::resolve('https://example.com/blog/2026/post.html', '../../assets/logo.png'),
        );
    }

    public function test_resolves_protocol_relative_urls(): void
    {
        $this->assertSame(
            'https://cdn.example.com/lib.js',
            UrlResolver::resolve('https://example.com/page', '//cdn.example.com/lib.js'),
        );
    }

    public function test_returns_null_for_fragments_and_non_web_schemes(): void
    {
        $this->assertNull(UrlResolver::resolve('https://example.com', '#top'));
        $this->assertNull(UrlResolver::resolve('https://example.com', 'mailto:hi@example.com'));
        $this->assertNull(UrlResolver::resolve('https://example.com', 'javascript:void(0)'));
        $this->assertNull(UrlResolver::resolve('https://example.com', 'data:image/png;base64,abc'));
        $this->assertNull(UrlResolver::resolve('https://example.com', ''));
    }

    public function test_origin_of_extracts_scheme_and_host(): void
    {
        $this->assertSame('https://example.com', UrlResolver::originOf('https://example.com/a/b?c=1'));
        $this->assertSame('http://example.com:8080', UrlResolver::originOf('http://example.com:8080/x'));
        $this->assertNull(UrlResolver::originOf('not a url'));
    }
}
