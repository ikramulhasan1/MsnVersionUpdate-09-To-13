@extends('layouts.app')

@section('title', 'Checkout — ' . $plan->name)

@section('content')
    <section class="container py-4" style="max-width: 560px;">
        <h1 class="h4 fw-semibold mb-1">Checkout</h1>
        <p class="text-secondary mb-4">You're subscribing to <strong>{{ $plan->name }}</strong>.</p>

        @if (session('checkout_error'))
            <div class="alert alert-danger small">{{ session('checkout_error') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium">{{ $plan->name }}</span>
                    <span class="fw-bold">{{ $plan->priceLabel() }}</span>
                </div>
                <p class="text-secondary small mb-0">{{ $plan->description }}</p>
            </div>
        </div>

        <h2 class="h6 fw-semibold mb-3">Choose a payment method</h2>

        <div class="d-flex flex-column gap-3">
            @foreach ($gateways as $gateway)
                @php
                    $disabled = $gateway === \App\Enums\PaymentGateway::SSLCOMMERZ && ! $plan->hasSslCommerzPrice();
                @endphp
                <form method="POST" action="{{ route('subscription.checkout.start', $plan) }}">
                    @csrf
                    <input type="hidden" name="gateway" value="{{ $gateway->value }}">
                    <button type="submit" class="btn btn-outline-secondary w-100 text-start d-flex align-items-center justify-content-between px-3 py-3"
                        @disabled($disabled)>
                        <span>
                            {{ $gateway->label() }}
                            @if ($gateway === \App\Enums\PaymentGateway::SSLCOMMERZ)
                                <br>
                                <span class="text-secondary small">
                                    {{ $disabled ? 'Not available for this plan yet' : $plan->priceBdtLabel() }}
                                </span>
                            @endif
                        </span>
                        <span aria-hidden="true">&rarr;</span>
                    </button>
                </form>
            @endforeach
        </div>

        <p class="text-secondary small mt-4">
            <a href="{{ route('subscription.upgrade') }}">&larr; Back to plans</a>
        </p>
    </section>
@endsection