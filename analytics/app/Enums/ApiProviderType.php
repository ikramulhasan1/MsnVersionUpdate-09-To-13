<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase O1 (API Provider Management System) — App\Models\ApiProvider's
 * own $type column. Each case's own credentialFields() drives BOTH the
 * Admin form's dynamic field rendering
 * (resources/views/admin/api-providers/form.blade.php) and what
 * App\ApiProviders\Contracts\ApiProviderAdapterInterface implementation
 * (Phase O2) actually reads out of a row's own decrypted $credentials
 * array — the two must always agree on field NAMES, since the form
 * writes exactly the keys credentialFields() lists and the adapter
 * reads them back by those same keys.
 */
enum ApiProviderType: string
{
    case DATAFORSEO_KEYWORDS = 'dataforseo_keywords';
    case DATAFORSEO_LABS = 'dataforseo_labs';
    case GOOGLE_ADS = 'google_ads';

    public function label(): string
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS => 'DataForSEO — Keywords Data API',
            self::DATAFORSEO_LABS => 'DataForSEO — Labs API',
            self::GOOGLE_ADS => 'Google Ads API',
        };
    }

    /**
     * DataForSEO's own two products share ONE credential shape — both
     * use the same account's HTTP Basic Auth (a "login" email and a
     * separate API "password" DataForSEO issues, NOT the account's own
     * login password) — see dataforseo.com's own dashboard, where this
     * pair is generated once and works across every DataForSEO product
     * the account has access to. Google Ads is a full OAuth2 client
     * instead: a developer token (issued once per Google Ads manager
     * account, requires Google's own approval), an OAuth client
     * id/secret (from a Google Cloud project), a long-lived refresh
     * token (obtained once via Google's own OAuth consent flow — this
     * app has no built-in flow to GENERATE one, an Admin must obtain it
     * externally and paste it in), and the target Ads account's own
     * customer id.
     *
     * @return array<string, array{label: string, type: string, help: ?string}>
     */
    public function credentialFields(): array
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS, self::DATAFORSEO_LABS => [
                'login' => ['label' => 'API Login (email)', 'type' => 'text', 'help' => null],
                'password' => ['label' => 'API Password', 'type' => 'password', 'help' => 'From your DataForSEO dashboard — not your account login password.'],
            ],
            self::GOOGLE_ADS => [
                'developer_token' => ['label' => 'Developer Token', 'type' => 'password', 'help' => null],
                'client_id' => ['label' => 'OAuth Client ID', 'type' => 'text', 'help' => null],
                'client_secret' => ['label' => 'OAuth Client Secret', 'type' => 'password', 'help' => null],
                'refresh_token' => ['label' => 'OAuth Refresh Token', 'type' => 'password', 'help' => 'Obtained once via Google\'s own OAuth consent flow — this app has no built-in way to generate one.'],
                'customer_id' => ['label' => 'Customer ID', 'type' => 'text', 'help' => 'The target Google Ads account, digits only, no dashes.'],
            ],
        };
    }

    /**
     * @return array<int, KeywordCapability>
     */
    public function possibleCapabilities(): array
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS => [
                KeywordCapability::VOLUME,
                KeywordCapability::CPC,
            ],
            self::DATAFORSEO_LABS => [
                KeywordCapability::DIFFICULTY,
                KeywordCapability::RELATED_KEYWORDS,
                KeywordCapability::SEARCH_INTENT,
                KeywordCapability::SERP_DATA,
            ],
            self::GOOGLE_ADS => [
                KeywordCapability::VOLUME,
                KeywordCapability::CPC,
            ],
        };
    }
}