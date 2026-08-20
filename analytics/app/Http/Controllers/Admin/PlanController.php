<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — full CRUD for
 * App\Models\Plan, reachable only by an Admin (routes/web.php's own
 * 'admin.' route group, role:Admin middleware — see Phase N3's own
 * docblock on why role, not a permission, gates the whole admin
 * panel). Every plan created or edited here — including the
 * "Free Trial" row Phase N1.5's own PlansSeeder created — goes
 * through this exact same form; there is no separate, special-cased
 * "edit the trial plan" path.
 */
final class PlanController extends Controller
{
    /**
     * Every recognized feature toggle this app's own middleware/checks
     * actually look at — see App\Models\Plan's own docblock for the
     * full list and what each one gates. The plan FORM (create/edit
     * views) builds its own checkboxes from this array, so a future
     * feature (Phase N7's own API access, say) only needs adding HERE
     * to show up as a real toggle in the Admin UI — no view changes.
     *
     * @return array<string, string>
     */
    public static function featureToggles(): array
    {
        return [
            'run-audit' => 'Website Audit',
            'run-bulk-audit' => 'Bulk Audit',
            'export-data' => 'Export (PDF/Excel/CSV/JSON)',
            // PRODUCTION INCIDENT — see this feature's own backfill
            // migration (database/migrations/2026_08_20_000001_backfill_view_discovery_plan_feature.php)
            // for the full "why": this key genuinely didn't exist on
            // any plan until that migration ran, and 'view-discovery'
            // was ONLY ever gated by role permission before that, not
            // by the person's actual Plan at all.
            'view-discovery' => 'Website Discovery',
        ];
    }

    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.form', [
            'plan' => new Plan(['features' => []]),
            'featureToggles' => self::featureToggles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        Plan::query()->create($validated);

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', [
            'plan' => $plan,
            'featureToggles' => self::featureToggles(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $this->validated($request, $plan);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('status', "Updated {$plan->name}.");
    }

    /**
     * The default trial plan is never deletable — every fresh
     * registration (App\Auth\NewUserOnboarder) depends on exactly one
     * plan having is_default_trial = true existing at all times;
     * deleting it would silently leave every future signup planless.
     * An Admin who genuinely wants a different trial plan should mark
     * a DIFFERENT plan as the default trial first (this controller's
     * own update() already lets is_default_trial move between plans —
     * see validated()'s own single-default enforcement below), THEN
     * delete the old one.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->is_default_trial) {
            return redirect()->route('admin.plans.index')
                ->with('status', "Can't delete {$plan->name} — it's the default trial plan. Mark a different plan as the default trial first.");
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', "Deleted {$plan->name}.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Plan $plan = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                'unique:plans,slug' . ($plan !== null ? ",{$plan->id}" : ''),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_dollars' => ['required', 'numeric', 'min:0'],
            // Phase N6 — nullable: not every plan needs a BDT price
            // set (see App\Models\Plan::hasSslCommerzPrice()'s own
            // docblock — SSLCommerz checkout simply isn't offered for
            // a plan without one).
            'price_bdt_taka' => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['nullable', 'string', 'in:month,year'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'is_public' => ['nullable', 'boolean'],
            'is_default_trial' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'features' => ['array'],
            'daily_audit_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $features = [];

        foreach (array_keys(self::featureToggles()) as $featureKey) {
            $features[$featureKey] = in_array($featureKey, $validated['features'] ?? [], true);
        }

        $features['daily_audit_limit'] = $validated['daily_audit_limit'] ?? null;

        // Phase N1.5's own NewUserOnboarder relies on EXACTLY one plan
        // having is_default_trial = true — checking this box on one
        // plan un-checks it everywhere else, rather than silently
        // allowing two "default" trial plans to exist at once (which
        // NewUserOnboarder's own ->first() query would then resolve
        // ambiguously, based on row order rather than real intent).
        if (! empty($validated['is_default_trial'])) {
            Plan::query()
                ->when($plan !== null, fn ($query) => $query->whereKeyNot($plan->id))
                ->update(['is_default_trial' => false]);
        }

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price_cents' => (int) round(((float) $validated['price_dollars']) * 100),
            'price_bdt_cents' => isset($validated['price_bdt_taka']) && $validated['price_bdt_taka'] !== null
                ? (int) round(((float) $validated['price_bdt_taka']) * 100)
                : null,
            'billing_cycle' => $validated['billing_cycle'] ?? null,
            'duration_days' => $validated['duration_days'] ?? null,
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'is_default_trial' => (bool) ($validated['is_default_trial'] ?? false),
            'sort_order' => $validated['sort_order'] ?? 0,
            'features' => $features,
        ];
    }
}