<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payments;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Payments\SslCommerzGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Phase N6 (Multiple Payment Methods) — the real checkout flow Phase
 * N5's own "request an upgrade" (App\Http\Controllers\SubscriptionController)
 * deliberately deferred to this phase. A Payment row is created with
 * PaymentStatus::PENDING the moment checkout STARTS (before any money
 * has actually moved) for BOTH gateways — see
 * App\Http\Controllers\Payments\StripeWebhookController/
 * App\Http\Controllers\Payments\SslCommerzController for where each
 * gateway's own confirmation flips that row to SUCCEEDED (and actually
 * activates the plan) or FAILED.
 */
final class CheckoutController extends Controller
{
    public function show(Request $request, Plan $plan): View
    {
        return view('subscription.checkout', [
            'plan' => $plan,
            'gateways' => PaymentGateway::cases(),
        ]);
    }

    public function start(Request $request, Plan $plan, SslCommerzGateway $sslCommerz): RedirectResponse
    {
        $validated = $request->validate([
            'gateway' => ['required', 'string', 'in:stripe,sslcommerz'],
        ]);

        $user = $request->user();
        $gateway = PaymentGateway::from($validated['gateway']);

        // Phase N6 — see database/migrations/2026_08_19_000011_add_price_bdt_cents_to_plans_table.php's
        // own docblock for the currency-mismatch bug this check
        // prevents: a plan with no real BDT price set must never reach
        // startSslCommerzCheckout() at all, which would otherwise have
        // nothing correct to charge.
        if ($gateway === PaymentGateway::SSLCOMMERZ && ! $plan->hasSslCommerzPrice()) {
            return redirect()->route('subscription.checkout', $plan)
                ->with('checkout_error', 'This plan isn\'t available for bKash/Nagad/local card payment yet — please use a card payment instead.');
        }

        return match ($gateway) {
            PaymentGateway::STRIPE => $this->startStripeCheckout($user, $plan),
            PaymentGateway::SSLCOMMERZ => $this->startSslCommerzCheckout($user, $plan, $sslCommerz),
        };
    }

    /**
     * Billable::checkout() (Cashier) creates a one-time Stripe Checkout
     * Session — see App\Models\User's own docblock on Billable for why
     * this app uses ONLY this one Cashier feature. The Payment row's
     * own gateway_reference is set to the Checkout Session's own ID
     * ($session->id) — that's the value
     * App\Http\Controllers\Payments\StripeWebhookController later
     * matches the incoming webhook event against.
     */
    private function startStripeCheckout(\App\Models\User $user, Plan $plan): RedirectResponse
    {
        $session = $user->checkout([], [
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => config('cashier.currency', 'usd'),
                    'product_data' => ['name' => $plan->name],
                    'unit_amount' => $plan->price_cents,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('billing.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription.checkout', $plan),
            'metadata' => ['plan_id' => $plan->id, 'user_id' => $user->id],
        ]);

        Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => PaymentGateway::STRIPE,
            'gateway_reference' => $session->id,
            'amount_cents' => $plan->price_cents,
            'currency' => strtoupper((string) config('cashier.currency', 'usd')),
            'status' => PaymentStatus::PENDING,
        ]);

        return redirect($session->url);
    }

    private function startSslCommerzCheckout(\App\Models\User $user, Plan $plan, SslCommerzGateway $sslCommerz): RedirectResponse
    {
        $tranId = $sslCommerz->generateTransactionId();

        $result = $sslCommerz->initSession($user, $plan, $tranId);

        if (! $result['success'] || $result['gateway_page_url'] === null) {
            return redirect()->route('subscription.checkout', $plan)
                ->with('checkout_error', "We couldn't start your payment right now. Please try again in a moment.");
        }

        Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => PaymentGateway::SSLCOMMERZ,
            'gateway_reference' => $tranId,
            // Phase N6 — price_bdt_cents (the plan's own real BDT
            // price, guaranteed non-null here by start()'s own
            // hasSslCommerzPrice() check above), NEVER price_cents
            // (USD) — see this column's own migration docblock for the
            // bug this fixes.
            'amount_cents' => $plan->price_bdt_cents,
            'currency' => 'BDT',
            'status' => PaymentStatus::PENDING,
        ]);

        return redirect($result['gateway_page_url']);
    }
}