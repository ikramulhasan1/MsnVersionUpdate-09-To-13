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
    // Phase Q1 (Domain Data Service Layer) — added after this enum's
    // own initial Phase O1 version, once Competitor Analysis/Backlink
    // Analysis (Phase Q2/Q3) needed real backlink data sources. See
    // App\Enums\DomainCapability's own docblock for what each of
    // these three can actually answer.
    case DATAFORSEO_BACKLINKS = 'dataforseo_backlinks';
    case MAJESTIC = 'majestic';
    case MOZ = 'moz';

    public function label(): string
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS => 'DataForSEO — Keywords Data API',
            self::DATAFORSEO_LABS => 'DataForSEO — Labs API',
            self::GOOGLE_ADS => 'Google Ads API',
            self::DATAFORSEO_BACKLINKS => 'DataForSEO — Backlinks API',
            self::MAJESTIC => 'Majestic API',
            self::MOZ => 'Moz Link Explorer API',
        };
    }

    /**
     * DataForSEO's own two products share ONE credential shape — both
     * use the same account's HTTP Basic Auth (a "login" email and a
     * separate API "password" DataForSEO issues, NOT the account's own
     * login password) — see dataforseo.com's own dashboard, where this
     * pair is generated once and works across every DataForSEO product
     * the account has access to, INCLUDING Backlinks API (Phase Q1) —
     * the same login/password pair used for Keywords Data/Labs works
     * here too, no separate DataForSEO account needed. Google Ads is a
     * full OAuth2 client instead: a developer token (issued once per
     * Google Ads manager account, requires Google's own approval), an
     * OAuth client id/secret (from a Google Cloud project), a
     * long-lived refresh token (obtained once via Google's own OAuth
     * consent flow — this app has no built-in flow to GENERATE one, an
     * Admin must obtain it externally and paste it in), and the target
     * Ads account's own customer id.
     *
     * Phase Q1 — Majestic and Moz are each their own separate service,
     * with their own separate accounts/credentials, unrelated to
     * DataForSEO or each other. Majestic authenticates with a single
     * API key (majestic.com's own "Internal/Reseller" auth mode — see
     * App\DomainData\Adapters\MajesticAdapter's own docblock for why
     * this mode was chosen over their OpenApp flow). Moz authenticates
     * with an Access ID + Secret Key pair (a DIFFERENT pair than
     * DataForSEO's login/password despite the similar shape — Moz's
     * own dashboard issues these, not interchangeable with anything
     * else).
     *
     * @return array<string, array{label: string, type: string, help: ?string}>
     */
    public function credentialFields(): array
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS, self::DATAFORSEO_LABS, self::DATAFORSEO_BACKLINKS => [
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
            self::MAJESTIC => [
                'api_key' => ['label' => 'API Key', 'type' => 'password', 'help' => 'From majestic.com/account/api — requires the API option enabled on your Majestic account.'],
            ],
            self::MOZ => [
                'access_id' => ['label' => 'Access ID', 'type' => 'text', 'help' => null],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'help' => 'From your Moz API account settings.'],
            ],
        };
    }

    /**
     * Phase Q1 — return type widened to KeywordCapability|DomainCapability
     * now that this app has two independent capability vocabularies
     * (per-keyword data vs per-domain data — see App\Enums\DomainCapability's
     * own docblock for why these stayed separate enums rather than one
     * merged one).
     *
     * @return array<int, KeywordCapability|DomainCapability>
     */
    public function possibleCapabilities(): array
    {
        return match ($this) {
            self::DATAFORSEO_KEYWORDS => [
                KeywordCapability::VOLUME,
                KeywordCapability::CPC,
                // Phase O3 — see KeywordCapability::VOLUME_TREND/COMPETITIVE_DENSITY's
                // own docblock for why these were added after Phase O2.
                KeywordCapability::VOLUME_TREND,
                KeywordCapability::COMPETITIVE_DENSITY,
            ],
            self::DATAFORSEO_LABS => [
                KeywordCapability::DIFFICULTY,
                KeywordCapability::RELATED_KEYWORDS,
                KeywordCapability::SEARCH_INTENT,
                KeywordCapability::SERP_DATA,
                // Phase Q1 — DataForSEO Labs is ALSO where the
                // domain-level capabilities live (Domain Rank Overview,
                // Competitors Domain, Ranked Keywords, Relevant Pages
                // endpoints — see App\DomainData\Adapters\DataForSeoDomainAdapter's
                // own docblock), separate from the per-keyword
                // capabilities above even though they're the same
                // DataForSEO product/credentials.
                DomainCapability::DOMAIN_OVERVIEW,
                DomainCapability::ORGANIC_COMPETITORS,
                DomainCapability::RANKING_KEYWORDS,
                DomainCapability::TOP_PAGES,
            ],
            self::GOOGLE_ADS => [
                KeywordCapability::VOLUME,
                KeywordCapability::CPC,
            ],
            self::DATAFORSEO_BACKLINKS => [
                DomainCapability::BACKLINKS_SUMMARY,
                DomainCapability::BACKLINKS_LIST,
                DomainCapability::REFERRING_DOMAINS,
                DomainCapability::ANCHOR_TEXT_DISTRIBUTION,
            ],
            self::MAJESTIC => [
                DomainCapability::BACKLINKS_SUMMARY,
                DomainCapability::BACKLINKS_LIST,
                DomainCapability::REFERRING_DOMAINS,
                DomainCapability::ANCHOR_TEXT_DISTRIBUTION,
            ],
            self::MOZ => [
                DomainCapability::BACKLINKS_SUMMARY,
                DomainCapability::REFERRING_DOMAINS,
            ],
        };
    }
}