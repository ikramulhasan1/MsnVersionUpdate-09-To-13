@extends('layouts.app')

@section('title', ($plan->exists ? 'Edit Plan — ' . $plan->name : 'New Plan') . ' — Admin')

@section('content')
    <section class="container py-4" style="max-width: 680px;">
        <p class="text-secondary small mb-1">Admin</p>
        <h1 class="h4 fw-semibold mb-4">{{ $plan->exists ? 'Edit ' . $plan->name : 'New Plan' }}</h1>

        <form method="POST"
            action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
            @csrf
            @if ($plan->exists)
                @method('PUT')
            @endif

            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Plan Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $plan->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">
                            Slug <span class="text-secondary small">(auto-generated from name if left blank)</span>
                        </label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                            name="slug" value="{{ old('slug', $plan->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $plan->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="price_dollars" class="form-label">Price (USD)</label>
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('price_dollars') is-invalid @enderror" id="price_dollars"
                                name="price_dollars"
                                value="{{ old('price_dollars', $plan->exists ? number_format($plan->price_cents / 100, 2, '.', '') : '0.00') }}"
                                required>
                            @error('price_dollars')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="billing_cycle" class="form-label">Billing Cycle</label>
                            <select class="form-select" id="billing_cycle" name="billing_cycle">
                                <option value="" @selected(old('billing_cycle', $plan->billing_cycle) === null)>
                                    One-time / Free
                                </option>
                                <option value="month" @selected(old('billing_cycle', $plan->billing_cycle) === 'month')>
                                    Monthly
                                </option>
                                <option value="year" @selected(old('billing_cycle', $plan->billing_cycle) === 'year')>
                                    Yearly
                                </option>
                            </select>
                        </div>
                    </div>

                    {{--
                        Phase N6 (Multiple Payment Methods) — see
                        App\Models\Plan::hasSslCommerzPrice()'s own
                        docblock for why this is a SEPARATE, explicit
                        field rather than an automatic USD-to-BDT
                        conversion: a plan with this left blank simply
                        won't offer SSLCommerz (bKash/Nagad/local card)
                        as a checkout option at all.
                    --}}
                    <div class="mb-3">
                        <label for="price_bdt_taka" class="form-label">
                            Price (BDT) <span class="text-secondary small">(for bKash/Nagad/local card via SSLCommerz — blank disables that option for this plan)</span>
                        </label>
                        <input type="number" step="0.01" min="0"
                            class="form-control @error('price_bdt_taka') is-invalid @enderror" id="price_bdt_taka"
                            name="price_bdt_taka"
                            value="{{ old('price_bdt_taka', $plan->exists && $plan->price_bdt_cents !== null ? number_format($plan->price_bdt_cents / 100, 2, '.', '') : '') }}"
                            style="max-width: 220px;">
                        @error('price_bdt_taka')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="duration_days" class="form-label">
                                Expires After (days) <span class="text-secondary small">(blank = never expires)</span>
                            </label>
                            <input type="number" min="1" class="form-control" id="duration_days"
                                name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}">
                        </div>
                        <div class="col-6">
                            <label for="sort_order" class="form-label">Display Order</label>
                            <input type="number" min="0" class="form-control" id="sort_order" name="sort_order"
                                value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                            @checked(old('is_public', $plan->is_public ?? true))>
                        <label class="form-check-label" for="is_public">
                            Show on public homepage pricing section
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default_trial"
                            name="is_default_trial" value="1"
                            @checked(old('is_default_trial', $plan->is_default_trial ?? false))>
                        <label class="form-check-label" for="is_default_trial">
                            Auto-assign to every new registration (only one plan should have this checked)
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-3">Features</h2>

                    @foreach ($featureToggles as $key => $label)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]"
                                id="feature-{{ $key }}" value="{{ $key }}"
                                @checked(in_array($key, old('features', $plan->exists ? array_keys(array_filter($plan->features ?? [], fn ($v, $k) => is_bool($v) && $v, ARRAY_FILTER_USE_BOTH)) : []), true))>
                            <label class="form-check-label" for="feature-{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <label for="daily_audit_limit" class="form-label">
                            Daily Audit Limit <span class="text-secondary small">(blank = unlimited)</span>
                        </label>
                        <input type="number" min="1" class="form-control" id="daily_audit_limit"
                            name="daily_audit_limit"
                            value="{{ old('daily_audit_limit', $plan->features['daily_audit_limit'] ?? null) }}"
                            style="max-width: 200px;">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $plan->exists ? 'Save Changes' : 'Create Plan' }}</button>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </section>
@endsection