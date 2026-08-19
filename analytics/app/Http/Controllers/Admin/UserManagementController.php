<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PlanUpgradeRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanUpgradeRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Phase N3 (Role & Permission System) — every action here is reachable
 * ONLY by an Admin (routes/web.php's own 'admin.' route group is
 * gated by role:Admin middleware, not a permission check — see
 * database/seeders/RolesAndPermissionsSeeder's own docblock on
 * 'view-admin-panel' for why role is the right check here, not that
 * permission).
 */
final class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->with(['roles', 'plan'])->orderBy('name')->paginate(20),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'targetUser' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'userRoleNames' => $user->roles->pluck('name')->all(),
            'userPermissionNames' => $user->getDirectPermissions()->pluck('name')->all(),
            // Phase N5 (Dynamic Pricing/Subscription)
            'plans' => Plan::query()->orderBy('sort_order')->orderBy('name')->get(),
            'pendingUpgradeRequests' => $user->planUpgradeRequests()
                ->where('status', PlanUpgradeRequestStatus::PENDING)
                ->with('plan')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Replaces BOTH this user's role AND their own individual
     * (directly-assigned, not role-inherited) permissions in one
     * submission — syncRoles()/syncPermissions() rather than
     * assignRole()/givePermissionTo() specifically, since an unchecked
     * checkbox on this form means "remove this", not merely "don't add
     * this": a plain assign call only ever ADDS, it never removes
     * something the person unchecked.
     *
     * Only ONE role at a time in practice, even though $request->role
     * is technically a single value (a radio group, not checkboxes, in
     * resources/views/admin/users/edit.blade.php — see that view's own
     * comment for why a single role rather than multiple was the right
     * choice here) — syncRoles() still takes an array, so this wraps
     * it in one.
     *
     * Phase N5 — ALSO assigns a plan directly (a separate form section
     * on the same page, its own <form> tag — see
     * resources/views/admin/users/edit.blade.php — so a role/permission
     * change and a plan change are two independent submissions, never
     * accidentally combined into one request). plan_id is nullable —
     * an Admin can unassign a plan entirely, leaving the user planless
     * (every planAllowsFeature() check already treats "no plan" as
     * "nothing allowed", the safe default). trial_ends_at, if provided,
     * lets an Admin grant a CUSTOM expiry (or none at all, for a real
     * non-expiring paid assignment) rather than being locked to
     * whatever that plan's own duration_days would compute — this is
     * the "custom/বিশেষ plan বা feature-set" this phase's own
     * requirement asked for: an Admin isn't limited to the plan's own
     * default duration when assigning it to one specific person.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated {$user->name}'s role and permissions.");
    }

    public function updatePlan(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['nullable', 'exists:plans,id'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $plan = $validated['plan_id'] !== null ? Plan::query()->find($validated['plan_id']) : null;

        $user->forceFill([
            'plan_id' => $plan?->id,
            'subscribed_at' => $plan !== null ? now() : null,
            'trial_ends_at' => $validated['trial_ends_at'] !== null
                ? \Illuminate\Support\Carbon::parse($validated['trial_ends_at'])
                : ($plan?->duration_days !== null ? now()->addDays($plan->duration_days) : null),
        ])->save();

        // Phase N5 — marks any PENDING request for this exact plan as
        // fulfilled, since an Admin assigning it here IS how a request
        // gets fulfilled (see App\Models\PlanUpgradeRequest's own
        // migration docblock — there's no automatic payment event to
        // do this instead). Requests for a DIFFERENT plan than the one
        // just assigned are left pending/untouched — assigning "Starter"
        // doesn't silently resolve someone's separate request for
        // "Agency".
        if ($plan !== null) {
            $user->planUpgradeRequests()
                ->where('plan_id', $plan->id)
                ->where('status', PlanUpgradeRequestStatus::PENDING)
                ->update(['status' => PlanUpgradeRequestStatus::FULFILLED]);
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', $plan !== null ? "Assigned {$plan->name} to {$user->name}." : "Removed {$user->name}'s plan.");
    }
}