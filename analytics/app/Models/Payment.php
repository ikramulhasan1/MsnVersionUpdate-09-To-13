<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Phase N6 (Multiple Payment Methods) — see this table's own migration
 * (database/migrations/2026_08_19_000009_create_payments_table.php)
 * for the full reasoning.
 */
final class Payment extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'gateway',
        'gateway_reference',
        'amount_cents',
        'currency',
        'status',
        'paid_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'gateway' => PaymentGateway::class,
            'status' => PaymentStatus::class,
            'amount_cents' => 'integer',
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (self $payment): void {
            $payment->uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function amountLabel(): string
    {
        return sprintf('%s %s', $this->currency, number_format($this->amount_cents / 100, 2));
    }
}