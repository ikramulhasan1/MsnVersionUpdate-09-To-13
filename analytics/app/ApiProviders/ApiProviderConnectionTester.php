<?php

declare(strict_types=1);

namespace App\ApiProviders;

use App\Enums\ApiProviderType;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

/**
 * Phase O1 (API Provider Management System) — backs the "Test
 * Connection" button on resources/views/admin/api-providers/index.blade.php.
 * Every check here deliberately uses the CHEAPEST possible real
 * endpoint for that provider — confirming the credentials
 * authenticate at all, never a real keyword-data call that would cost
 * money just to verify a login works.
 */
final class ApiProviderConnectionTester
{
    /**
     * @return array{success: bool, message: string}
     */
    public function test(ApiProvider $provider): array
    {
        return match ($provider->type) {
            ApiProviderType::DATAFORSEO_KEYWORDS, ApiProviderType::DATAFORSEO_LABS => $this->testDataForSeo($provider),
            ApiProviderType::GOOGLE_ADS => $this->testGoogleAds($provider),
        };
    }

    /**
     * DataForSEO's own documented "check your account" endpoint —
     * appendix/user_data — returns the account's own current balance
     * and doesn't consume any paid credits itself; this is the
     * lightest real confirmation that a login/password pair actually
     * authenticates, without spending anything to find out.
     */
    private function testDataForSeo(ApiProvider $provider): array
    {
        $login = $provider->credential('login');
        $password = $provider->credential('password');

        if ($login === null || $password === null) {
            return ['success' => false, 'message' => 'Login and password are both required.'];
        }

        try {
            $response = Http::withBasicAuth($login, $password)
                ->timeout(10)
                ->get('https://api.dataforseo.com/v3/appendix/user_data');

            if (! $response->successful()) {
                return ['success' => false, 'message' => "HTTP {$response->status()} — check your credentials."];
            }

            $body = $response->json();
            $statusCode = $body['status_code'] ?? null;

            if ($statusCode !== 20000) {
                $message = $body['status_message'] ?? 'Unknown error.';

                return ['success' => false, 'message' => "DataForSEO error: {$message}"];
            }

            $balance = $body['tasks'][0]['result'][0]['money']['balance'] ?? null;

            return [
                'success' => true,
                'message' => $balance !== null
                    ? "Connected — account balance: \${$balance}"
                    : 'Connected successfully.',
            ];
        } catch (\Throwable $exception) {
            return ['success' => false, 'message' => 'Connection failed: '.$exception->getMessage()];
        }
    }

    /**
     * Google Ads' own REST API requires a real access token exchanged
     * from the stored refresh_token first (OAuth2's own standard token
     * endpoint) — this test performs that exchange, then makes the
     * lightest real authenticated call available (fetching the target
     * customer's own resource name) to confirm the WHOLE credential
     * chain (developer token, OAuth client, refresh token, customer
     * id) actually works together, not just that the refresh token
     * alone is valid.
     */
    private function testGoogleAds(ApiProvider $provider): array
    {
        $clientId = $provider->credential('client_id');
        $clientSecret = $provider->credential('client_secret');
        $refreshToken = $provider->credential('refresh_token');
        $developerToken = $provider->credential('developer_token');
        $customerId = $provider->credential('customer_id');

        if ($clientId === null || $clientSecret === null || $refreshToken === null
            || $developerToken === null || $customerId === null) {
            return ['success' => false, 'message' => 'All five Google Ads fields are required.'];
        }

        try {
            $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if (! $tokenResponse->successful()) {
                return ['success' => false, 'message' => 'OAuth token exchange failed — check client ID/secret/refresh token.'];
            }

            $accessToken = $tokenResponse->json('access_token');

            if ($accessToken === null) {
                return ['success' => false, 'message' => 'OAuth exchange succeeded but returned no access token.'];
            }

            $customerId = preg_replace('/\D/', '', $customerId);

            $adsResponse = Http::withToken($accessToken)
                ->withHeaders(['developer-token' => $developerToken])
                ->timeout(10)
                ->get("https://googleads.googleapis.com/v18/customers/{$customerId}");

            if (! $adsResponse->successful()) {
                $errorMessage = $adsResponse->json('error.message') ?? "HTTP {$adsResponse->status()}";

                return ['success' => false, 'message' => "Google Ads API error: {$errorMessage}"];
            }

            return ['success' => true, 'message' => 'Connected successfully.'];
        } catch (\Throwable $exception) {
            return ['success' => false, 'message' => 'Connection failed: '.$exception->getMessage()];
        }
    }
}