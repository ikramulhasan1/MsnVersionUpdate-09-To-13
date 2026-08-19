<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PlanUpgradeRequestStatus;
use App\Models\Plan;
use App\Models\PlanUpgradeRequest;
use App\Models\User;
use App\Notifications\PlanUpgradeRequestedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — the "user নিজের ড্যাশবোর্ড
 * থেকে Upgrade Subscription পেজে গিয়ে অন্য plan-এ upgrade করতে
 * চাইতে পারবে" half of this phase's own requirement. No payment
 * happens here at all (Phase N6) — requestUpgrade() only ever creates
 * a App\Models\PlanUpgradeRequest row and notifies every Admin; a
 * human (an Admin, via
 * App\Http\Controllers\Admin\UserManagementController::updatePlan())
 * is what actually changes the person's plan.
 */
final class SubscriptionController extends Controller
{
    public function upgrade(Request $request): View
    {
        $user = $request->user();

        return view('subscription.upgrade', [
            'currentPlan' => $user->plan,
            // Public plans only — Free Trial itself (is_public = false,
            // see database/seeders/PlansSeeder) is never something a
            // person "upgrades" TO, and a plan they're ALREADY on isn't
            // a real upgrade option either.
            'plans' => Plan::query()
                ->where('is_public', true)
                ->when($user->plan_id !== null, fn ($query) => $query->whereKeyNot($user->plan_id))
                ->orderBy('sort_order')
                ->get(),
            'pendingRequests' => $user->planUpgradeRequests()
                ->where('status', PlanUpgradeRequestStatus::PENDING)
                ->with('plan')
                ->get(),
        ]);
    }

    public function requestUpgrade(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $user = $request->user();
        $plan = Plan::query()->findOrFail($validated['plan_id']);

        // Avoids piling up duplicate pending requests for the SAME
        // plan if someone clicks the button more than once — reuses
        // the existing pending row instead of creating a second one.
        $existing = $user->planUpgradeRequests()
            ->where('plan_id', $plan->id)
            ->where('status', PlanUpgradeRequestStatus::PENDING)
            ->first();

        $upgradeRequest = $existing ?? $user->planUpgradeRequests()->create([
            'plan_id' => $plan->id,
            'status' => PlanUpgradeRequestStatus::PENDING,
        ]);

        if ($existing === null) {
            $admins = User::role('Admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new PlanUpgradeRequestedNotification($upgradeRequest));
            }
        }

        return redirect()->route('subscription.upgrade')
            ->with('status', "Your request to upgrade to {$plan->name} has been sent — we'll be in touch shortly.");
    }
}