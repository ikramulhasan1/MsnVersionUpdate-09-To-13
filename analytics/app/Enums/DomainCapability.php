<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase Q1 (Domain Data Service Layer) — the domain-level counterpart
 * to App\Enums\KeywordCapability. Kept as its OWN separate enum rather
 * than merging into KeywordCapability because the two represent
 * genuinely different REQUEST SHAPES throughout this app:
 * KeywordCapability values are always requested for one or more
 * KEYWORD strings; DomainCapability values are always requested for
 * one DOMAIN — App\DomainData\DomainDataService's own methods each
 * take a domain, never a keyword, and
 * App\DomainData\DomainDataCacheRepository caches by domain, not
 * keyword. Merging the two vocabularies would blur that distinction
 * throughout the caching/provider-matching logic for no real benefit.
 */
enum DomainCapability: string
{
    case DOMAIN_OVERVIEW = 'domain_overview';
    case ORGANIC_COMPETITORS = 'organic_competitors';
    case RANKING_KEYWORDS = 'ranking_keywords';
    case TOP_PAGES = 'top_pages';
    case BACKLINKS_SUMMARY = 'backlinks_summary';
    case BACKLINKS_LIST = 'backlinks_list';
    case REFERRING_DOMAINS = 'referring_domains';
    case ANCHOR_TEXT_DISTRIBUTION = 'anchor_text_distribution';

    public function label(): string
    {
        return match ($this) {
            self::DOMAIN_OVERVIEW => 'Domain Overview',
            self::ORGANIC_COMPETITORS => 'Organic Competitors',
            self::RANKING_KEYWORDS => 'Ranking Keywords',
            self::TOP_PAGES => 'Top Pages',
            self::BACKLINKS_SUMMARY => 'Backlinks Summary',
            self::BACKLINKS_LIST => 'Backlinks List',
            self::REFERRING_DOMAINS => 'Referring Domains',
            self::ANCHOR_TEXT_DISTRIBUTION => 'Anchor Text Distribution',
        };
    }
}