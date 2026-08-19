<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Payments\PlanActivator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;

/**
 * Phase N6 (Multiple Payment Methods).
 */
final class BillingController extends Controller
{
    /**
     * Backs the Billing History page (this phase's own explicit
     * requirement) — every payment attempt this user has ever made,
     * success or failure alike (see App\Models\Payment's own migration
     * docblock for why a failed attempt still gets its own row rather
     * than being discarded).
     */
    public function history(Request $request): View
    {
        return view('billing.history', [
            'payments' => $request->user()->payments()->with('plan')->latest()->paginate(20),
        ]);
    }

    /**
     * Where Stripe Checkout's own success_url sends the browser back
     * to (App\Http\Controllers\Payments\CheckoutController::startStripeCheckout()).
     * The REAL confirmation is
     * App\Http\Controllers\Payments\StripeWebhookController's own
     * webhook — this method is a FALLBACK only, for the case where the
     * webhook hasn't arrived yet by the time the browser redirect
     * does (a real, common race — Stripe's own webhook delivery can
     * lag a few seconds behind the browser redirect). Calling
     * Stripe's own API directly here to check the Session's real
     * payment_status and activating through the SAME idempotent
     * App\Payments\PlanActivator the webhook uses means this never
     * double-activates even if the webhook ALSO arrives moments later
     * — whichever gets there first wins, the second is a safe no-op.
     */
    public function stripeSuccess(Request $request, PlanActivator $activator): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (is_string($sessionId)) {
            $payment = Payment::query()->where('gateway', 'stripe')->where('gateway_reference', $sessionId)->first();

            if ($payment !== null) {
                try {
                    $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

                    if (($session->payment_status ?? null) === 'paid') {
                        $activator->activate($payment);
                    }
                } catch (\Throwable $exception) {
                    report($exception);
                    // Left PENDING — the webhook (or a later visit to
                    // this same page) still has a chance to confirm it
                    // properly; a transient Stripe API error here must
                    // never be treated as a real payment failure.
                }
            }
        }

        return redirect()->route('billing.history')
            ->with('status', 'Thanks! If your payment succeeded, your plan will update within a moment.');
    }
}