<?php

declare(strict_types=1);

namespace App\Audit\Fetching\Contracts;

use App\Audit\Fetching\DTO\ParsedHtml;

interface HtmlParserInterface
{
    /**
     * Extract meta, CSS/JS links, images, fonts, manifest and feed hints
     * from raw HTML. $baseUrl is used to resolve relative URLs and must be
     * the final (post-redirect) URL the HTML was served from.
     */
    public function parse(string $html, string $baseUrl): ParsedHtml;
}
