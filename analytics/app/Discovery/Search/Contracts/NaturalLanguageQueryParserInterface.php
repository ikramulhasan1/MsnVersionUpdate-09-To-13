<?php

declare(strict_types=1);

namespace App\Discovery\Search\Contracts;

use App\Discovery\Search\DTO\DiscoveryFilterCriteria;

/**
 * Turns a free-text "smart search" query (e.g. "Restaurant businesses
 * in Chicago with SEO below 50") directly into a DiscoveryFilterCriteria
 * — the Website Discovery module's natural-language search entry point
 * (Phase F2).
 *
 * A contract rather than calling
 * App\Discovery\Search\NaturalLanguageQueryParser directly anywhere:
 * that class is a rule-based (regex/keyword) implementation — see its
 * own docblock for exactly what it can and can't recognize — and every
 * caller is expected to depend on this interface instead, so a future
 * phase can bind an LLM-backed implementation (calling out to an
 * actual language model for genuinely flexible natural-language
 * understanding) without any caller changing. See
 * App\Providers\DiscoveryServiceProvider for where that binding lives
 * today (bound to the rule-based parser) and where it would be
 * repointed later — the same "swap the implementation behind an
 * interface" pattern GeoLookupServiceInterface already established for
 * this module.
 */
interface NaturalLanguageQueryParserInterface
{
    public function parse(string $query): DiscoveryFilterCriteria;
}