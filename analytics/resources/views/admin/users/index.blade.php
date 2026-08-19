@extends('layouts.app')

@section('title', 'Manage Users — Admin')

@section('content')
    <section class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="text-secondary small mb-1">Admin</p>
                <h1 class="h4 fw-semibold mb-0">Manage Users</h1>
            </div>
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
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-secondary small">No role assigned</span>
                                    @endforelse
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        Edit Access
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </section>
@endsection