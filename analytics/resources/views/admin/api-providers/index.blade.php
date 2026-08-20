@extends('layouts.app')

@section('title', 'API Providers — Admin')

@section('content')
    <section class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="text-secondary small mb-1">Admin</p>
                <h1 class="h4 fw-semibold mb-0">API Providers</h1>
            </div>
            <a href="{{ route('admin.api-providers.create') }}" class="btn btn-primary btn-sm">New Provider</a>
        </div>

        @include('admin.partials.nav')

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if (session('test_error'))
            <div class="alert alert-danger small">{{ session('test_error') }}</div>
        @endif

        @if ($providers->isEmpty())
            <div class="card">
                <div class="card-body p-4 text-center text-secondary">
                    No API providers configured yet — add one to power Keyword Research and Keyword
                    Magic Tool.
                </div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Capabilities</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Last Test</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($providers as $provider)
                                <tr>
                                    <td>{{ $provider->name }}</td>
                                    <td class="small text-secondary">{{ $provider->type->label() }}</td>
                                    <td class="small">
                                        @forelse ($provider->capabilities as $capability)
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis me-1">
                                                {{ \App\Enums\KeywordCapability::from($capability)->label() }}
                                            </span>
                                        @empty
                                            <span class="text-secondary">None selected</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $provider->priority }}</td>
                                    <td>
                                        @if ($provider->is_active)
                                            <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if ($provider->last_tested_at === null)
                                            <span class="text-secondary">Never tested</span>
                                        @elseif ($provider->last_test_succeeded)
                                            <span class="text-success">✓ {{ $provider->last_tested_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-danger" title="{{ $provider->last_test_message }}">
                                                ✗ {{ $provider->last_tested_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.api-providers.test', $provider) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Test</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.api-providers.toggle', $provider) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                {{ $provider->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.api-providers.edit', $provider) }}"
                                            class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('admin.api-providers.destroy', $provider) }}"
                                            class="d-inline" onsubmit="return confirm('Delete this provider?');">
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
        @endif
    </section>
@endsection