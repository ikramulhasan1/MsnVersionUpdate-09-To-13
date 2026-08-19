<?php

declare(strict_types=1);

namespace App\Payments;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Phase N6 (Multiple Payment Methods) — SSLCommerz is Bangladesh's own
 * dominant payment aggregator: ONE integration that itself covers
 * bKash, Nagad, Rocket, local bank transfer, and local cards, which is
 * why it was chosen over integrating bKash/Nagad as separate,
 * standalone gateways (each of which has its own, narrower merchant
 * API and would need its own separate integration).
 *
 * FLOW:
 *   1. initSession() POSTs to SSLCommerz's own session-init API,
 *      which returns a GatewayPageURL — the person is redirected
 *      THERE (SSLCommerz's own hosted payment page), never asked to
 *      enter payment details on this app's own pages at all.
 *   2. SSLCommerz redirects the BROWSER back to success_url/fail_url/
 *      cancel_url (a simple POST, spoofable by anyone who guesses the
 *      URL — NEVER trusted on its own) and SEPARATELY calls ipn_url
 *      server-to-server (SSLCommerz's own backend calling this app's
 *      own backend directly — much harder to spoof, but still not
 *      blindly trusted either).
 *   3. validateTransaction() is the ONE actually-authoritative step —
 *      calls SSLCommerz's own Validation API with the val_id from
 *      either the redirect or the IPN, and only a real, positive
 *      response from THAT call is treated as a genuinely confirmed
 *      payment. See App\Http\Controllers\Payments\SslCommerzController
 *      for where all three of these actually get called.
 */
final class SslCommerzGateway
{
    public function __construct(
        private readonly string $storeId,
        private readonly string $storePassword,
        private readonly bool $sandbox,
    ) {
    }

    private function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * @return array{success: bool, gateway_page_url: ?string, tran_id: string, raw: array<string, mixed>}
     */
    public function initSession(User $user, Plan $plan, string $tranId): array
    {
        // Phase N6 — price_bdt_cents specifically, never price_cents
        // (USD) — see that column's own migration docblock
        // (database/migrations/2026_08_19_000011_add_price_bdt_cents_to_plans_table.php)
        // for the currency-mismatch bug this fixes. Callers
        // (App\Http\Controllers\Payments\CheckoutController::start())
        // already guarantee this is non-null before ever reaching
        // here.
        $amount = $plan->price_bdt_cents / 100;

        $response = Http::asForm()->post("{$this->baseUrl()}/gwprocess/v4/api.php", [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => number_format($amount, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $tranId,
            'success_url' => route('billing.sslcommerz.success'),
            'fail_url' => route('billing.sslcommerz.fail'),
            'cancel_url' => route('billing.sslcommerz.cancel'),
            'ipn_url' => route('billing.sslcommerz.ipn'),
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            // SSLCommerz's own API requires these fields even when this
            // app has no real address on file — a real Bangladeshi
            // merchant integration always sends SOMETHING here; "N/A"
            // is SSLCommerz's own documented convention for "not
            // collected", not a fabricated real address.
            'cus_add1' => 'N/A',
            'cus_city' => 'N/A',
            'cus_country' => 'Bangladesh',
            'cus_phone' => 'N/A',
            'shipping_method' => 'NO',
            'product_name' => $plan->name,
            'product_category' => 'Subscription',
            'product_profile' => 'general',
        ]);

        $body = $response->json() ?? [];

        if (($body['status'] ?? null) !== 'SUCCESS') {
            Log::warning('SSLCommerz session init failed', ['response' => $body]);
        }

        return [
            'success' => ($body['status'] ?? null) === 'SUCCESS',
            'gateway_page_url' => $body['GatewayPageURL'] ?? null,
            'tran_id' => $tranId,
            'raw' => $body,
        ];
    }

    /**
     * The ONE authoritative confirmation step — see this class's own
     * docblock. Never treat a success_url redirect or an IPN payload
     * alone as a real payment; always confirm via THIS call first.
     *
     * @return array{valid: bool, amount_cents: ?int, raw: array<string, mixed>}
     */
    public function validateTransaction(string $valId): array
    {
        $response = Http::get("{$this->baseUrl()}/validator/api/validationserverAPI.php", [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ]);

        $body = $response->json() ?? [];

        $status = $body['status'] ?? null;
        $valid = in_array($status, ['VALID', 'VALIDATED'], true);

        return [
            'valid' => $valid,
            'amount_cents' => $valid && isset($body['amount'])
                ? (int) round(((float) $body['amount']) * 100)
                : null,
            'raw' => $body,
        ];
    }

    public function generateTransactionId(): string
    {
        return 'sslc_' . Str::uuid()->toString();
    }
}