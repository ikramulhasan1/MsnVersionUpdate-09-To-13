<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payments;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Payments\PlanActivator;
use App\Payments\SslCommerzGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase N6 (Multiple Payment Methods) — see App\Payments\SslCommerzGateway's
 * own docblock for the full three-step flow. success()/fail()/cancel()
 * are what the person's own BROWSER hits (a redirect SSLCommerz sends
 * it to) — never trusted on their own, only used to send the person
 * somewhere sensible while the REAL confirmation happens via ipn()
 * below (or, as a fallback, success()'s own validation call, in case
 * the IPN hasn't arrived yet by the time the browser redirect does).
 */
final class SslCommerzController extends Controller
{
    public function success(Request $request, SslCommerzGateway $gateway, PlanActivator $activator): RedirectResponse
    {
        $this->confirmAndActivate($request, $gateway, $activator);

        return redirect()->route('billing.history')
            ->with('status', 'Payment received — your plan has been updated!');
    }

    public function fail(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        if (is_string($tranId)) {
            $payment = Payment::query()->where('gateway', 'sslcommerz')->where('gateway_reference', $tranId)->first();
            $payment?->update(['status' => PaymentStatus::FAILED]);
        }

        return redirect()->route('billing.history')
            ->with('checkout_error', 'Your payment could not be completed. Please try again.');
    }

    /**
     * PRODUCTION GAP CLOSED — read before removing this method's own
     * DB update: an earlier version just redirected without touching
     * the Payment row at all, leaving it stuck at PaymentStatus::PENDING
     * forever (a cancel is a real, final outcome — "the person
     * abandoned this payment" — not an in-progress state). Billing
     * History (resources/views/billing/history.blade.php) would have
     * shown a cancelled attempt as still "Pending" indefinitely,
     * looking like something was still processing when nothing was.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        if (is_string($tranId)) {
            $payment = Payment::query()->where('gateway', 'sslcommerz')->where('gateway_reference', $tranId)->first();
            $payment?->update(['status' => PaymentStatus::FAILED]);
        }

        return redirect()->route('billing.history')
            ->with('checkout_error', 'Payment cancelled.');
    }

    /**
     * SSLCommerz's own server-to-server IPN — the authoritative
     * confirmation path (see App\Payments\SslCommerzGateway's own
     * docblock). Always returns a plain 200 regardless of outcome —
     * SSLCommerz's own IPN delivery retries on anything else, and a
     * genuinely invalid/duplicate notification would never become
     * valid on retry.
     */
    public function ipn(Request $request, SslCommerzGateway $gateway, PlanActivator $activator): Response
    {
        $this->confirmAndActivate($request, $gateway, $activator);

        return new Response('', 200);
    }

    private function confirmAndActivate(Request $request, SslCommerzGateway $gateway, PlanActivator $activator): void
    {
        $valId = $request->input('val_id');
        $tranId = $request->input('tran_id');

        if (! is_string($valId) || ! is_string($tranId)) {
            return;
        }

        $payment = Payment::query()->where('gateway', 'sslcommerz')->where('gateway_reference', $tranId)->first();

        if ($payment === null) {
            return;
        }

        $result = $gateway->validateTransaction($valId);

        if (! $result['valid']) {
            $activator->markFailed($payment);

            return;
        }

        // Phase N6 — a real, defensive amount check: validateTransaction()'s
        // own response carries the amount SSLCommerz actually
        // confirms was paid, compared against what THIS app expected
        // for that transaction (payment->amount_cents, set at checkout
        // time from the plan's own real BDT price — see
        // App\Http\Controllers\Payments\CheckoutController). A
        // mismatch means something is wrong (a tampered request, a
        // gateway-side data issue) and this payment is NOT activated —
        // "the gateway says it validated" alone is not enough if the
        // amount doesn't match what was actually owed.
        if ($result['amount_cents'] !== null && $result['amount_cents'] !== $payment->amount_cents) {
            $activator->markFailed($payment);

            return;
        }

        $activator->activate($payment);
    }
}