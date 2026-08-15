<?php

declare(strict_types=1);

namespace App\Discovery\Search;

use App\Discovery\Enums\ContactAvailability;
use App\Models\DiscoveredWebsite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Builds and runs the Website Discovery search query from a
 * DiscoveryFilterCriteria — the beginning of this module's real search
 * backend (see that DTO's own docblock for exactly which filters are
 * wired in today vs. still UI-only, and why).
 *
 * query() is exposed separately from search() so a future phase can
 * compose additional clauses (pagination, sorting, eager-loading the
 * watchlist relationship for the results list, ...) onto the same
 * Builder without this class needing a combinatorial explosion of
 * search-with-this-option-or-that-option methods.
 */
final class WebsiteSearchService
{
    /**
     * @return Collection<int, DiscoveredWebsite>
     */
    public function search(DiscoveryFilterCriteria $criteria, int $limit = 50): Collection
    {
        return $this->query($criteria)->latest('discovered_at')->limit($limit)->get();
    }

    public function query(DiscoveryFilterCriteria $criteria): Builder
    {
        $query = DiscoveredWebsite::query();

        $this->applyContactAvailability($query, $criteria->contactAvailability);

        return $query;
    }

    /**
     * See ContactAvailability's own docblock for why CONTACT_FORM is
     * approximated as "contact_page_url is not null", and why this is
     * a radio-style single choice rather than several independent
     * checkbox clauses.
     */
    private function applyContactAvailability(Builder $query, ?ContactAvailability $availability): void
    {
        match ($availability) {
            null => null,
            ContactAvailability::EMAIL => $query->whereNotNull('email'),
            ContactAvailability::PHONE => $query->whereNotNull('phone'),
            ContactAvailability::CONTACT_FORM => $query->whereNotNull('contact_page_url'),
            ContactAvailability::NONE => $query
                ->whereNull('email')
                ->whereNull('phone')
                ->whereNull('contact_page_url'),
        };
    }
}