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

    public function label(): string
    {
        return match ($this) {
            self::VOLUME => 'Search Volume',
            self::CPC => 'CPC',
            self::DIFFICULTY => 'Keyword Difficulty',
            self::RELATED_KEYWORDS => 'Related Keywords',
            self::SEARCH_INTENT => 'Search Intent',
            self::SERP_DATA => 'SERP Data',
        };
    }
}