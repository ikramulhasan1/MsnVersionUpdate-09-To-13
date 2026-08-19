<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Phase N6 (Multiple Payment Methods) — App\Models\Payment's own
 * $gateway column. Stripe covers international card payments;
 * SSLCommerz is Bangladesh's own dominant payment aggregator — ONE
 * integration that itself covers bKash, Nagad, Rocket, local bank
 * transfer, AND local cards, which is why it was chosen over
 * integrating bKash/Nagad as separate, standalone gateways (see this
 * phase's own deploy notes for the full reasoning).
 */
enum PaymentGateway: string
{
    case STRIPE = 'stripe';
    case SSLCOMMERZ = 'sslcommerz';

    public function label(): string
    {
        return match ($this) {
            self::STRIPE => 'Card (Stripe)',
            self::SSLCOMMERZ => 'bKash / Nagad / Local Card (SSLCommerz)',
        };
    }
}