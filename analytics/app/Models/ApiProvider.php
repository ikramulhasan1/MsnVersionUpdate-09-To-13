<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApiProviderType;
use Illuminate\Database\Eloquent\Model;

/**
 * Phase O1 (API Provider Management System) — see this table's own
 * migration (database/migrations/2026_08_21_000001_create_api_providers_table.php)
 * for the full reasoning.
 *
 * $credentials uses Laravel's own built-in 'encrypted:array' cast —
 * the ENTIRE credentials array is encrypted as one blob using this
 * app's own APP_KEY before ever touching the database, and
 * transparently decrypted back into a plain PHP array on read. A
 * database backup/export, or direct DB access, never exposes a real
 * API key/secret in plain text — only Laravel's own decrypt() (which
 * requires this app's own APP_KEY) can read it back. If APP_KEY is
 * ever rotated, every existing row's own $credentials becomes
 * unreadable — see this app's own deploy notes for why APP_KEY must
 * never be regenerated on an existing production database without a
 * real re-encryption migration first.
 */
final class ApiProvider extends Model
{
    protected $fillable = [
        'name',
        'type',
        'credentials',
        'capabilities',
        'is_active',
        'priority',
        'last_tested_at',
        'last_test_succeeded',
        'last_test_message',
    ];

    protected function casts(): array
    {
        return [
            'type' => ApiProviderType::class,
            'credentials' => 'encrypted:array',
            'capabilities' => 'array',
            'is_active' => 'boolean',
            'priority' => 'integer',
            'last_tested_at' => 'datetime',
            'last_test_succeeded' => 'boolean',
        ];
    }

    /**
     * A single credential value by key (e.g. 'login', 'password') —
     * used throughout App\ApiProviders\* adapters (Phase O2) instead
     * of each one reaching into ->credentials[...] directly, so a
     * missing key returns null consistently rather than a PHP warning.
     */
    public function credential(string $key): ?string
    {
        $value = $this->credentials[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities ?? [], true);
    }

    /**
     * PRODUCTION BUG AVOIDED (Phase Q1) — resources/views/admin/api-providers/index.blade.php's
     * own capability-badge list used to call
     * App\Enums\KeywordCapability::from($capability) unconditionally
     * for every saved capability string — correct for a
     * DATAFORSEO_KEYWORDS/DATAFORSEO_LABS/GOOGLE_ADS row (whose
     * capabilities are all KeywordCapability values), but a genuine
     * \ValueError (uncaught, would have crashed that whole page) the
     * moment a DATAFORSEO_BACKLINKS/MAJESTIC/MOZ row's own
     * DomainCapability values (Phase Q1) reached that same line. This
     * method tries KeywordCapability first, falls back to
     * DomainCapability, and returns the raw string as a last resort
     * (never throws) — the one place that ambiguity gets resolved, so
     * the view itself never needs to know which enum a given
     * capability string belongs to.
     */
    public function capabilityLabel(string $capability): string
    {
        return \App\Enums\KeywordCapability::tryFrom($capability)?->label()
            ?? \App\Enums\DomainCapability::tryFrom($capability)?->label()
            ?? $capability;
    }
}