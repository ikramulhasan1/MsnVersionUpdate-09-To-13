<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KeywordList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Phase O5 (Keyword List/Project Management) — PRODUCTION-CRITICAL
 * OWNERSHIP PATTERN, read before touching any method here: this exact
 * app has, over several real production incidents earlier in this
 * project (Website Discovery's own audit-sourced leak, then a second
 * round narrowing an Admin's own "sees everything" bypass down to
 * "sees only their own"), learned the hard way that per-user data
 * needs an EXPLICIT ownership check on every single action that
 * touches a specific row — never just a route-model-binding that
 * resolves ANY id, and never an Admin-wide bypass either. Every
 * method below that receives a KeywordList via route model binding
 * (show/destroy/removeItem/export) immediately verifies
 * $keywordList->user_id === $request->user()->id and aborts 403
 * otherwise — including for an Admin. A KeywordList has no "legacy,
 * unowned row" case the way DiscoveredWebsite does (see that table's
 * own user_id column, nullable, vs this one's own NOT nullable — every
 * KeywordList is created by a real person taking an explicit action),
 * so there's no orphaned-data exception to carve out here at all: this
 * is simply, unconditionally "only the owner, full stop".
 */
final class KeywordListController extends Controller
{
    public function index(Request $request): View
    {
        return view('keyword-lists.index', [
            'lists' => $request->user()->keywordLists()->withCount('items')->latest()->get(),
        ]);
    }

    /**
     * Phase O5 — backs resources/views/keyword-lists/_add-to-list-modal.blade.php's
     * own existing-lists dropdown, populated via fetch() when that
     * modal opens (public/js/keyword-lists.js). Deliberately its own
     * lightweight JSON endpoint rather than reusing index() with
     * content negotiation — this response is a minimal {id, name}
     * pair per list, not the item-count-annotated shape index()'s own
     * view needs.
     */
    public function listJson(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->keywordLists()->orderBy('name')->get(['id', 'name']),
        );
    }

    public function show(Request $request, KeywordList $keywordList): View
    {
        $this->authorizeOwner($request, $keywordList);

        return view('keyword-lists.show', [
            'list' => $keywordList,
            'items' => $keywordList->items()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $list = $request->user()->keywordLists()->create(['name' => $validated['name']]);

        return redirect()->route('keyword-lists.show', $list)->with('status', "Created \"{$list->name}\".");
    }

    public function destroy(Request $request, KeywordList $keywordList): RedirectResponse
    {
        $this->authorizeOwner($request, $keywordList);

        $keywordList->delete();

        return redirect()->route('keyword-lists.index')->with('status', 'List deleted.');
    }

    /**
     * The shared "Add to List" endpoint both Phase O3's Keyword
     * Research page and Phase O4's Keyword Magic Tool call — either
     * with an existing list_id (adding to a list the person already
     * has) or a new_list_name (creating one on the fly, matching this
     * app's own explicit "বা নতুন list তৈরি করে" requirement). AJAX-
     * only (called via fetch() from
     * public/js/keyword-lists.js), never a full page navigation —
     * returns JSON either way.
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'list_id' => ['nullable', 'integer'],
            'new_list_name' => ['nullable', 'string', 'max:255'],
            'keyword' => ['required', 'string', 'max:255'],
            'volume' => ['nullable', 'integer'],
            'difficulty' => ['nullable', 'integer'],
            'cpc' => ['nullable', 'numeric'],
        ]);

        if (empty($validated['list_id']) && empty($validated['new_list_name'])) {
            return response()->json(['message' => 'Choose an existing list or name a new one.'], 422);
        }

        if (! empty($validated['list_id'])) {
            $list = KeywordList::query()->find($validated['list_id']);

            // Same ownership check as every other method here — a
            // list_id posted from the browser is just as untrusted as
            // a route-bound {keywordList}, and must be verified
            // identically before anything is written to it.
            if ($list === null || $list->user_id !== $request->user()->id) {
                return response()->json(['message' => 'List not found.'], 404);
            }
        } else {
            $list = $request->user()->keywordLists()->create(['name' => $validated['new_list_name']]);
        }

        $list->items()->updateOrCreate(
            ['keyword' => $validated['keyword']],
            [
                'volume' => $validated['volume'] ?? null,
                'difficulty' => $validated['difficulty'] ?? null,
                'cpc' => $validated['cpc'] ?? null,
            ],
        );

        return response()->json(['message' => "Added to \"{$list->name}\".", 'list_id' => $list->id, 'list_name' => $list->name]);
    }

    public function removeItem(Request $request, KeywordList $keywordList, int $item): RedirectResponse
    {
        $this->authorizeOwner($request, $keywordList);

        $keywordList->items()->where('id', $item)->delete();

        return redirect()->route('keyword-lists.show', $keywordList)->with('status', 'Keyword removed.');
    }

    public function export(Request $request, KeywordList $keywordList): StreamedResponse
    {
        $this->authorizeOwner($request, $keywordList);

        $items = $keywordList->items()->get();

        return Response::streamDownload(function () use ($items): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Keyword', 'Volume', 'Difficulty', 'CPC']);

            foreach ($items as $item) {
                fputcsv($handle, [$item->keyword, $item->volume, $item->difficulty, $item->cpc]);
            }

            fclose($handle);
        }, str_replace(' ', '-', strtolower($keywordList->name)).'.csv');
    }

    /**
     * PRODUCTION-CRITICAL — see this class's own docblock. Called at
     * the top of EVERY method that receives a KeywordList, no
     * exceptions, including for an Admin.
     */
    private function authorizeOwner(Request $request, KeywordList $keywordList): void
    {
        abort_unless($keywordList->user_id === $request->user()->id, 403);
    }
}