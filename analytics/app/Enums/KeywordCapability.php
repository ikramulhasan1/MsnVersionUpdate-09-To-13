<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase O1 (API Provider Management System) — App\Models\ApiProvider's
 * own $capabilities column holds an array of these cases' own values.
 * This is the SAME vocabulary Phase O2's own KeywordDataService uses
 * when deciding "which active provider row can answer a request for
 * X" — a capability name here must exactly match how that service
 * asks for it, so the two phases can never drift into two different
 * naming schemes for the same concept.
 */
enum KeywordCapability: string
{
    case VOLUME = 'volume';
    case CPC = 'cpc';
    case DIFFICULTY = 'difficulty';
    case RELATED_KEYWORDS = 'related_keywords';
    case SEARCH_INTENT = 'search_intent';
    case SERP_DATA = 'serp_data';
    // Phase O3 (Keyword Research page) — added after Phase O2 shipped,
    // once that page's own real requirements (a 12-month trend graph,
    // a Competitive Density metric) turned out to need two more
    // distinct pieces of data than Phase O2 originally scoped. Both
    // come from the SAME underlying DataForSEO Keywords Data response
    // App\KeywordData\Adapters\DataForSeoKeywordsAdapter::searchVolumeData()
    // already fetches for plain search volume — see that method's own
    // updated docblock.
    case VOLUME_TREND = 'volume_trend';
    case COMPETITIVE_DENSITY = 'competitive_density';

    public function label(): string
    {
        return match ($this) {
            self::VOLUME => 'Search Volume',
            self::CPC => 'CPC',
            self::DIFFICULTY => 'Keyword Difficulty',
            self::RELATED_KEYWORDS => 'Related Keywords',
            self::SEARCH_INTENT => 'Search Intent',
            self::SERP_DATA => 'SERP Data',
            self::VOLUME_TREND => 'Search Volume Trend',
            self::COMPETITIVE_DENSITY => 'Competitive Density',
        };
    }
}