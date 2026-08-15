<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Advanced Filters panel's "Opportunity" checkbox group
 * (resources/views/discovery/partials/search-panel.blade.php) — each
 * case is a specific, named reason a discovered site would be a good
 * lead for a particular kind of service work.
 *
 * UI-only for now, matching this module's established convention for
 * every other Advanced Filters group added so far: checking a box here
 * submits and round-trips through the URL (see
 * App\Http\Controllers\DiscoveryController::search()), but nothing yet
 * applies it to a query. self::criterion() documents the exact rule a
 * future App\Discovery\Filters\OpportunityFilterService is expected to
 * apply for each case, so that service's eventual implementation has a
 * single, already-agreed place to start from rather than needing to
 * reinvent what "SEO Opportunity" means — but that service does not
 * exist yet, deliberately: this phase only needs the vocabulary and
 * the filter UI, not the query logic.
 */
enum OpportunityFilter: string
{
    case SEO = 'seo';
    case PERFORMANCE = 'performance';
    case MOBILE = 'mobile';
    case SECURITY = 'security';
    case TECHNOLOGY = 'technology';
    case DESIGN = 'design';

    public function label(): string
    {
        return match ($this) {
            self::SEO => 'SEO Opportunity',
            self::PERFORMANCE => 'Performance Opportunity',
            self::MOBILE => 'Mobile Opportunity',
            self::SECURITY => 'Security Opportunity',
            self::TECHNOLOGY => 'Technology Opportunity',
            self::DESIGN => 'Design Opportunity',
        };
    }

    /**
     * The specific, already-agreed rule a future
     * App\Discovery\Filters\OpportunityFilterService is expected to
     * apply for this case — see this enum's own class docblock. Not
     * read by anything yet beyond display (e.g. as a checkbox's title/
     * tooltip); it exists now so the eventual filtering implementation
     * doesn't need to redefine what each opportunity type means.
     */
    public function criterion(): string
    {
        return match ($this) {
            self::SEO => 'SEO score below 50',
            self::PERFORMANCE => 'Performance score below 40',
            self::MOBILE => 'Poor mobile experience (e.g. missing/incorrect viewport, low mobile score)',
            self::SECURITY => 'Not served over HTTPS',
            self::TECHNOLOGY => 'Running an outdated CMS/framework version',
            self::DESIGN => 'Outdated or poorly structured design/UX signals',
        };
    }
}