<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DiscoveredWebsite;
use App\Models\DiscoveryWatchlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Website Discovery — Phase A3 (routes + navbar) / A4 (page shell).
 *
 * Wires up the module's five routes (see routes/web.php) with real,
 * working controller actions and view/route-model-binding plumbing,
 * matching AuditController's own conventions (final class, uuid route-
 * model binding via DiscoveredWebsite::getRouteKeyName()). The actual
 * Industry/Niche search and advanced-filter logic (Location,
 * Technology, SEO/Performance/Security/Opportunity score, ...) is
 * intentionally NOT implemented here — that's a later phase's work.
 *
 * index() already fetches an unfiltered $websites list and passes
 * $filters through, ready for discovery/index.blade.php's "Results"
 * and "Search panel" placeholder sections (Phase A4) to start using as
 * soon as a later prompt fills them in — no controller change needed
 * when that happens. search() is a real, submittable, round-tripping
 * endpoint (a filter submitted via the search form comes back as query
 * parameters on the index page, exactly like a real search would)
 * even before the form/filtering UI itself exists yet.
 */
final class DiscoveryController extends Controller
{
    public function index(Request $request): View
    {
        return view('discovery.index', [
            'websites' => DiscoveredWebsite::query()->latest('discovered_at')->limit(50)->get(),
            'filters' => $request->query(),
        ]);
    }

    /**
     * Round-trips whatever filter fields were submitted back onto the
     * index page as query parameters, so the search form behaves like
     * a real search bar (a bookmarkable/shareable URL, browser back/
     * forward works, the submitted values repopulate the form) even
     * before index() actually applies them to a query — see this
     * class's own docblock for why that part is deferred to a later
     * phase.
     */
    public function search(Request $request): RedirectResponse
    {
        return redirect()->route('discovery.index', $request->except('_token'));
    }

    public function show(DiscoveredWebsite $website): View
    {
        return view('discovery.show', [
            'website' => $website,
            'isWatched' => $website->watchlistItem()->exists(),
        ]);
    }

    /**
     * updateOrCreate() rather than create(): discovery_watchlist's own
     * discovered_website_id column is unique (see database/migrations/
     * 2026_08_14_000002_create_discovery_watchlist_table.php's
     * docblock — a site can only be on the watchlist once), so
     * "watch" on an already-watched site is a harmless no-op update
     * rather than a duplicate-key error.
     */
    public function watch(DiscoveredWebsite $website): RedirectResponse
    {
        DiscoveryWatchlistItem::query()->updateOrCreate(
            ['discovered_website_id' => $website->id],
        );

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Added to your watchlist.');
    }

    public function unwatch(DiscoveredWebsite $website): RedirectResponse
    {
        $website->watchlistItem()->delete();

        return redirect()
            ->route('discovery.show', $website)
            ->with('status', 'Removed from your watchlist.');
    }
}