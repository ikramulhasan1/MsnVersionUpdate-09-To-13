@extends('layouts.app')

@section('title', 'Edit Access — ' . $targetUser->name)

@section('content')
    <section class="container py-4" style="max-width: 680px;">
        <p class="text-secondary small mb-1">Admin</p>
        <h1 class="h4 fw-semibold mb-1">Edit Access</h1>
        <p class="text-secondary mb-4">{{ $targetUser->name }} &middot; {{ $targetUser->email }}</p>

        <form method="POST" action="{{ route('admin.users.update', $targetUser) }}">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-1">Role</h2>
                    {{--
                        Phase N3 — a radio group, not checkboxes: this
                        app's own three roles (Admin/Employee/User) are
                        meant to be MUTUALLY EXCLUSIVE tiers, not a set
                        someone can hold several of at once (unlike the
                        individual permission checkboxes below, which
                        genuinely can and should combine freely) — see
                        App\Http\Controllers\Admin\UserManagementController::update()'s
                        own docblock for how this single value gets
                        applied via syncRoles().
                    --}}
                    <p class="text-secondary small mb-3">
                        Admin has every permission automatically. Employee starts with none — grant exactly
                        what this person needs below. User gets the standard customer feature set.
                    </p>

                    @foreach ($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="role" id="role-{{ $role->id }}"
                                value="{{ $role->name }}" @checked(in_array($role->name, $userRoleNames, true))
                                required>
                            <label class="form-check-label" for="role-{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-1">Individual Permissions</h2>
                    <p class="text-secondary small mb-3">
                        These apply on top of the role above — useful mainly for an Employee, whose role
                        grants nothing by default. Admin already has everything regardless of what's checked
                        here.
                    </p>

                    @foreach ($permissions as $permission)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                id="permission-{{ $permission->id }}" value="{{ $permission->name }}"
                                @checked(in_array($permission->name, $userPermissionNames, true))>
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>

        {{--
            Phase N5 (Dynamic Pricing/Subscription) — a SEPARATE <form>
            from the role/permission one above, on purpose: plan
            assignment and role/permission changes are two independent
            actions (see
            App\Http\Controllers\Admin\UserManagementController::updatePlan()'s
            own docblock), so they submit — and can fail validation —
            independently too, rather than one bad field in either
            silently blocking the other from saving.
        --}}
        @if ($pendingUpgradeRequests->isNotEmpty())
            <div class="alert alert-warning small mb-4">
                <strong>{{ $targetUser->name }}</strong> has requested:
                @foreach ($pendingUpgradeRequests as $request)
                    {{ $request->plan->name }}{{ ! $loop->last ? ', ' : '' }}
                @endforeach
                — assigning that plan below marks the request fulfilled automatically.
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-1">Plan</h2>
                <p class="text-secondary small mb-3">
                    Assign any plan directly — including a custom expiry below, independent of that
                    plan's own default duration. Leave "No Plan" selected to remove this person's plan
                    entirely.
                </p>

                <form method="POST" action="{{ route('admin.users.update-plan', $targetUser) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Plan</label>
                        <select class="form-select" id="plan_id" name="plan_id">
                            <option value="" @selected($targetUser->plan_id === null)>No Plan</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected($targetUser->plan_id === $plan->id)>
                                    {{ $plan->name }} ({{ $plan->priceLabel() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="trial_ends_at" class="form-label">
                            Expires On <span class="text-secondary small">(blank = use the plan's own default, or never expires for a plan with no duration)</span>
                        </label>
                        <input type="date" class="form-control" id="trial_ends_at" name="trial_ends_at"
                            value="{{ $targetUser->trial_ends_at?->format('Y-m-d') }}" style="max-width: 220px;">
                    </div>

                    @if ($targetUser->plan !== null)
                        <p class="text-secondary small mb-3">
                            Current: <strong>{{ $targetUser->plan->name }}</strong>
                            @if ($targetUser->trial_ends_at !== null)
                                &mdash; expires {{ $targetUser->trial_ends_at->diffForHumans() }}
                            @endif
                        </p>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm">Save Plan</button>
                </form>
            </div>
        </div>
    </section>
@endsection