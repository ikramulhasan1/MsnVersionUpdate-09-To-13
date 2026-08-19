@extends('layouts.app')

@section('title', 'Pricing Plans — Admin')

@section('content')
    <section class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="text-secondary small mb-1">Admin</p>
                <h1 class="h4 fw-semibold mb-0">Pricing Plans</h1>
            </div>
            <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">New Plan</a>
        </div>

        @include('admin.partials.nav')

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Visibility</th>
                            <th>Features</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td>
                                    {{ $plan->name }}
                                    @if ($plan->is_default_trial)
                                        <span class="badge bg-warning-subtle text-warning-emphasis ms-1">
                                            Default Trial
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $plan->priceLabel() }}</td>
                                <td>{{ $plan->duration_days !== null ? $plan->duration_days . ' day(s)' : 'No expiry' }}</td>
                                <td>
                                    @if ($plan->is_public)
                                        <span class="badge bg-success-subtle text-success-emphasis">Public</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Hidden</span>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    @foreach (\App\Http\Controllers\Admin\PlanController::featureToggles() as $key => $label)
                                        @if ($plan->allowsFeature($key))
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis me-1">{{ $label }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.plans.edit', $plan) }}"
                                        class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                        class="d-inline" onsubmit="return confirm('Delete this plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection