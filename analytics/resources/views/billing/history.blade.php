@extends('layouts.app')

@section('title', 'Billing History — Website Audit & Analysis Platform')

@section('content')
    <section class="container py-4">
        <h1 class="h4 fw-semibold mb-1">Billing History</h1>
        <p class="text-secondary mb-4">Every payment attempt on your account, successful or not.</p>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if (session('checkout_error'))
            <div class="alert alert-danger small">{{ session('checkout_error') }}</div>
        @endif

        @if ($payments->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    You don't have any payments yet.
                    <a href="{{ route('subscription.upgrade') }}">Browse plans</a> to get started.
                </div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Plan</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="small">{{ $payment->created_at->format('M j, Y') }}</td>
                                    <td>{{ $payment->plan->name }}</td>
                                    <td class="small">{{ $payment->gateway->label() }}</td>
                                    <td>{{ $payment->amountLabel() }}</td>
                                    <td>
                                        <span class="badge {{ $payment->status->badgeClass() }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @endif
    </section>
@endsection