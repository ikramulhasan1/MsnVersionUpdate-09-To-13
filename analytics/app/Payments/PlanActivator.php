<?php

declare(strict_types=1);

namespace App\Payments;

use App\Enums\PaymentStatus;
use App\Enums\PlanUpgradeRequestStatus;
use App\Models\Payment;

/**
 * Phase N6 (Multiple Payment Methods) — the ONE place a successful
 * payment actually turns into an activated plan, called from BOTH
 * App\Http\Controllers\Payments\StripeWebhookController and
 * App\Http\Controllers\Payments\SslCommerzController so the two
 * gateways can never drift into two different definitions of "what
 * happens when someone pays" — the same reasoning App\Auth\NewUserOnboarder
 * already established for keeping one shared behavior out of two
 * separate call sites (Phase N1.5's own docblock explains the
 * production gap that pattern was built specifically to prevent).
 */
final class PlanActivator
{
    /**
     * Idempotent — safe to call more than once for the SAME $payment
     * (a gateway can and does redeliver webhooks/IPNs). Does nothing
     * on a second call: activate() first re-checks $payment's own
     * CURRENT status and returns immediately if it's already
     * PaymentStatus::SUCCEEDED, so re-activating never double-charges
     * anything, never re-extends an expiry a second time, and never
     * sends a duplicate confirmation.
     */
    public function activate(Payment $payment): void
    {
        $payment->refresh();

        if ($payment->status === PaymentStatus::SUCCEEDED) {
            return;
        }

        $payment->update([
            'status' => PaymentStatus::SUCCEEDED,
            'paid_at' => now(),
        ]);

        $user = $payment->user;
        $plan = $payment->plan;

        $user->forceFill([
            'plan_id' => $plan->id,
            'subscribed_at' => now(),
            'trial_ends_at' => $plan->duration_days !== null
                ? now()->addDays($plan->duration_days)
                : null,
        ])->save();

        // Same reasoning as
        // App\Http\Controllers\Admin\UserManagementController::updatePlan()'s
        // own identical block — a real payment for a plan someone
        // separately REQUESTED (Phase N5) is exactly as much a
        // fulfillment of that request as an Admin manually assigning
        // it would be.
        $user->planUpgradeRequests()
            ->where('plan_id', $plan->id)
            ->where('status', PlanUpgradeRequestStatus::PENDING)
            ->update(['status' => PlanUpgradeRequestStatus::FULFILLED]);
    }

    public function markFailed(Payment $payment): void
    {
        $payment->refresh();

        if ($payment->status === PaymentStatus::SUCCEEDED) {
            // A payment that already succeeded can never be
            // retroactively marked failed by a later, out-of-order
            // webhook delivery — the plan is already active, and
            // silently reverting that based on a late/duplicate
            // failure signal would be a real regression, not a
            // correction.
            return;
        }

        $payment->update(['status' => PaymentStatus::FAILED]);
    }
}