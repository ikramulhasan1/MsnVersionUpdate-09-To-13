<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Phase N1.5 (Free Trial) — see this table's own migration
 * (database/migrations/2026_08_19_000006_create_plans_table.php) for
 * why this same shape serves both the one "Free Trial" row this phase
 * creates AND every real paid plan Phase N5's own Admin UI will create
 * later.
 *
 * $features is a JSON column, expected (by convention — nothing
 * enforces this shape at the database level) to hold:
 *   'run-audit'          bool — can this plan run a Website Audit at all.
 *   'run-bulk-audit'      bool — can this plan run Bulk Audit.
 *   'export-data'         bool — can this plan export a PDF/Excel/CSV/JSON.
 *   'daily_audit_limit'   ?int — max audits/day this plan allows, null
 *                        = unlimited.
 * Every one of these mirrors a permission name from
 * database/seeders/RolesAndPermissionsSeeder — deliberately: a
 * feature this JSON marks false is a HARD stop regardless of what the
 * user's own ROLE permission says (see
 * App\Http\Middleware\EnsurePlanAllowsFeature's own docblock for
 * exactly how the two checks stack), so keeping the same names avoids
 * two different vocabularies for "the same" capability.
 */
final class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        // Phase N6 — see this column's own migration docblock
        // (database/migrations/2026_08_19_000011_add_price_bdt_cents_to_plans_table.php)
        // for the currency-mismatch bug this exists to prevent. Null
        // means "no BDT price set" — SSLCommerz checkout is simply not
        // offered for a plan in that state, see
        // App\Http\Controllers\Payments\CheckoutController.
        'price_bdt_cents',
        'billing_cycle',
        'duration_days',
        'features',
        'is_default_trial',
        'is_public',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'price_bdt_cents' => 'integer',
            'duration_days' => 'integer',
            'features' => 'array',
            'is_default_trial' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * True/false for a boolean feature key (e.g. 'run-bulk-audit');
     * defaults to false for a key this plan's own $features has no
     * entry for at all — an UNLISTED feature is treated as "not
     * included", never silently allowed, matching the same
     * "don't fabricate access" principle this app's permission system
     * already follows.
     */
    public function allowsFeature(string $key): bool
    {
        return (bool) ($this->features[$key] ?? false);
    }

    /**
     * Null means unlimited — see this class's own docblock on
     * 'daily_audit_limit'.
     */
    public function dailyAuditLimit(): ?int
    {
        $limit = $this->features['daily_audit_limit'] ?? null;

        return is_int($limit) ? $limit : null;
    }

    public function priceLabel(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }

        $dollars = number_format($this->price_cents / 100, 2);

        return $this->billing_cycle !== null
            ? "\${$dollars}/{$this->billing_cycle}"
            : "\${$dollars}";
    }

    /**
     * Phase N6 — true only when a real, explicit BDT price has been
     * set (see this column's own migration docblock). Used by
     * resources/views/subscription/checkout.blade.php to decide
     * whether SSLCommerz is even offered as a payment option for this
     * particular plan.
     */
    public function hasSslCommerzPrice(): bool
    {
        return $this->price_bdt_cents !== null && $this->price_bdt_cents > 0;
    }

    public function priceBdtLabel(): ?string
    {
        if (! $this->hasSslCommerzPrice()) {
            return null;
        }

        $taka = number_format($this->price_bdt_cents / 100, 2);

        return $this->billing_cycle !== null
            ? "\u{09F3}{$taka}/{$this->billing_cycle}"
            : "\u{09F3}{$taka}";
    }
}