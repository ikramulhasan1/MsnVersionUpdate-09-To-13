<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payments;

use App\Models\Payment;
use App\Payments\PlanActivator;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase N6 (Multiple Payment Methods) — extends Cashier's OWN
 * WebhookController rather than building signature verification from
 * scratch: Cashier's base class already validates the
 * Stripe-Signature header against config('cashier.webhook.secret')
 * before any handle*() method below ever runs, using the exact same
 * verification Stripe's own documentation recommends. This app only
 * overrides the ONE event type it actually cares about
 * (checkout.session.completed) — every other Stripe event Cashier's
 * base controller would otherwise route to (subscription lifecycle
 * events, etc) is irrelevant here, since this app deliberately doesn't
 * use Cashier's own subscription features (see App\Models\User's own
 * docblock on Billable).
 */
final class StripeWebhookController extends CashierWebhookController
{
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        $sessionId = $payload['data']['object']['id'] ?? null;
        $paymentStatus = $payload['data']['object']['payment_status'] ?? null;

        if ($sessionId === null) {
            return new Response('', 200);
        }

        $payment = Payment::query()
            ->where('gateway', 'stripe')
            ->where('gateway_reference', $sessionId)
            ->first();

        if ($payment === null) {
            // A webhook for a Checkout Session this app has no
            // matching Payment row for — nothing to act on. Returning
            // 200 (not an error status) is deliberate: Stripe retries
            // a webhook delivery on any non-2xx response, and retrying
            // this one would never succeed differently.
            return new Response('', 200);
        }

        if ($paymentStatus === 'paid') {
            app(PlanActivator::class)->activate($payment);
        } else {
            app(PlanActivator::class)->markFailed($payment);
        }

        return new Response('', 200);
    }
}