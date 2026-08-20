@extends('layouts.app')

@section('title', ($provider->exists ? 'Edit ' . $provider->name : 'New Provider') . ' — Admin')

@section('content')
    <section class="container py-4" style="max-width: 680px;">
        <p class="text-secondary small mb-1">Admin</p>
        <h1 class="h4 fw-semibold mb-4">{{ $provider->exists ? 'Edit ' . $provider->name : 'New API Provider' }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ $provider->exists ? route('admin.api-providers.update', $provider) : route('admin.api-providers.store') }}">
            @csrf
            @if ($provider->exists)
                @method('PUT')
            @endif

            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Provider Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $provider->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Provider Type</label>
                        <select class="form-select" id="type" name="type" @disabled($provider->exists)>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}"
                                    @selected(old('type', $provider->exists ? $provider->type->value : null) === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($provider->exists)
                            {{-- Phase O1 — type is locked after creation: the
                                 credential SHAPE and possible capabilities both
                                 depend on type (see App\Enums\ApiProviderType's
                                 own docblock), so changing it on an existing row
                                 would leave stale, wrong-shaped credentials
                                 behind. Delete and recreate instead if a
                                 genuinely different type is needed. --}}
                            <input type="hidden" name="type" value="{{ $provider->type->value }}">
                            <p class="text-secondary small mt-1 mb-0">
                                Type can't be changed after creation — delete and recreate if needed.
                            </p>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label for="priority" class="form-label">
                                Priority <span class="text-secondary small">(lower tries first)</span>
                            </label>
                            <input type="number" min="0" class="form-control" id="priority" name="priority"
                                value="{{ old('priority', $provider->priority ?? 0) }}">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" @checked(old('is_active', $provider->is_active ?? false))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-3">Capabilities</h2>
                    <p class="text-secondary small mb-3">
                        Which capabilities THIS provider can answer — only shown here are ones this type
                        actually supports.
                    </p>

                    @foreach ($types as $type)
                        <div class="capability-group" data-type="{{ $type->value }}" style="display:none;">
                            @foreach ($type->possibleCapabilities() as $capability)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="capabilities[]"
                                        id="capability-{{ $type->value }}-{{ $capability->value }}"
                                        value="{{ $capability->value }}"
                                        @checked(in_array($capability->value, old('capabilities', $provider->capabilities ?? []), true))>
                                    <label class="form-check-label" for="capability-{{ $type->value }}-{{ $capability->value }}">
                                        {{ $capability->label() }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-1">Credentials</h2>
                    {{--
                        Phase O1 — PRODUCTION-CRITICAL: every credential input
                        below is ALWAYS rendered empty, even when editing an
                        existing provider whose credentials already have real
                        values. A real API secret must never round-trip back
                        into an HTML input's own value="..." attribute — that
                        would put it in plain text in the page source, browser
                        history, and any proxy/cache along the way, defeating
                        the whole point of encrypting it at rest. See
                        App\Http\Controllers\Admin\ApiProviderController::validated()'s
                        own docblock for how a left-blank field on an EXISTING
                        provider is correctly treated as "keep the current
                        value", not "clear it".
                    --}}
                    <p class="text-secondary small mb-3">
                        @if ($provider->exists)
                            Leave any field blank to keep its current (already-saved) value — only fields
                            you actually type into will be changed.
                        @else
                            All fields below are required for a new provider.
                        @endif
                    </p>

                    @foreach ($types as $type)
                        <div class="credential-group" data-type="{{ $type->value }}" style="display:none;">
                            @foreach ($type->credentialFields() as $key => $field)
                                <div class="mb-3">
                                    <label for="cred-{{ $type->value }}-{{ $key }}" class="form-label">
                                        {{ $field['label'] }}
                                    </label>
                                    <input type="{{ $field['type'] }}" class="form-control"
                                        id="cred-{{ $type->value }}-{{ $key }}"
                                        name="credentials[{{ $key }}]" autocomplete="off"
                                        placeholder="{{ $provider->exists ? '(unchanged)' : '' }}">
                                    @if ($field['help'])
                                        <div class="form-text">{{ $field['help'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $provider->exists ? 'Save Changes' : 'Create Provider' }}</button>
                <a href="{{ route('admin.api-providers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            // Phase O1 — shows only the credential/capability field group
            // matching the currently-selected type; the others stay in the
            // DOM (so their own values still submit correctly if a browser
            // back/forward restores a previous selection) but hidden.
            (function () {
                const typeSelect = document.getElementById('type');

                function syncGroups() {
                    const selected = typeSelect.value;

                    document.querySelectorAll('.credential-group, .capability-group').forEach(function (group) {
                        group.style.display = group.dataset.type === selected ? '' : 'none';
                    });
                }

                typeSelect.addEventListener('change', syncGroups);
                syncGroups();
            })();
        </script>
    @endpush
@endsection