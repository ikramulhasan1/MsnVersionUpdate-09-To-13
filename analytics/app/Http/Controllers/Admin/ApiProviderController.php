<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\ApiProviders\ApiProviderConnectionTester;
use App\Enums\ApiProviderType;
use App\Enums\DomainCapability;
use App\Enums\KeywordCapability;
use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase O1 (API Provider Management System) — reachable only by an
 * Admin (routes/web.php's own 'admin.' route group, role:Admin
 * middleware — the same pattern App\Http\Controllers\Admin\PlanController
 * already established). Every credential this controller ever writes
 * goes straight into App\Models\ApiProvider's own encrypted cast —
 * this controller itself never logs, flashes, or otherwise surfaces a
 * real credential value anywhere.
 */
final class ApiProviderController extends Controller
{
    public function index(): View
    {
        return view('admin.api-providers.index', [
            'providers' => ApiProvider::query()->orderBy('type')->orderBy('priority')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.api-providers.form', [
            'provider' => new ApiProvider(['capabilities' => []]),
            'types' => ApiProviderType::cases(),
            'allCapabilities' => [...KeywordCapability::cases(), ...DomainCapability::cases()],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        ApiProvider::query()->create($validated);

        return redirect()->route('admin.api-providers.index')->with('status', 'Provider created.');
    }

    public function edit(ApiProvider $apiProvider): View
    {
        return view('admin.api-providers.form', [
            'provider' => $apiProvider,
            'types' => ApiProviderType::cases(),
            'allCapabilities' => [...KeywordCapability::cases(), ...DomainCapability::cases()],
        ]);
    }

    public function update(Request $request, ApiProvider $apiProvider): RedirectResponse
    {
        $validated = $this->validated($request, $apiProvider);

        $apiProvider->update($validated);

        return redirect()->route('admin.api-providers.index')->with('status', "Updated {$apiProvider->name}.");
    }

    public function destroy(ApiProvider $apiProvider): RedirectResponse
    {
        $apiProvider->delete();

        return redirect()->route('admin.api-providers.index')->with('status', "Deleted {$apiProvider->name}.");
    }

    public function toggleActive(ApiProvider $apiProvider): RedirectResponse
    {
        $apiProvider->update(['is_active' => ! $apiProvider->is_active]);

        return redirect()->route('admin.api-providers.index')
            ->with('status', $apiProvider->is_active ? "{$apiProvider->name} activated." : "{$apiProvider->name} deactivated.");
    }

    public function testConnection(ApiProvider $apiProvider, ApiProviderConnectionTester $tester): RedirectResponse
    {
        $result = $tester->test($apiProvider);

        $apiProvider->update([
            'last_tested_at' => now(),
            'last_test_succeeded' => $result['success'],
            'last_test_message' => $result['message'],
        ]);

        return redirect()->route('admin.api-providers.index')
            ->with($result['success'] ? 'status' : 'test_error', $result['message']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ApiProvider $provider = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(ApiProviderType::cases(), 'value'))],
            'capabilities' => ['array'],
            'capabilities.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'credentials' => ['nullable', 'array'],
        ]);

        $type = ApiProviderType::from($validated['type']);

        // PRODUCTION-SAFE CREDENTIAL HANDLING — read before changing
        // this: resources/views/admin/api-providers/form.blade.php's
        // own credential inputs are ALWAYS rendered empty, even when
        // editing an existing provider (see that view's own docblock
        // for why — a real secret must never round-trip back into
        // HTML). That means a blank credential field on submission is
        // AMBIGUOUS: it could mean "the Admin left this blank on
        // purpose because they're not changing it" (editing an
        // existing provider) or "there's genuinely no value yet"
        // (creating a new one). The rule: for an EXISTING provider,
        // only a field the Admin actually TYPED something into
        // (non-empty in the request) overwrites that key in
        // $credentials — every other key keeps whatever was already
        // encrypted there. For a NEW provider, every field the type
        // expects is required (there's nothing to "keep" yet).
        $submittedCredentials = $validated['credentials'] ?? [];
        $expectedKeys = array_keys($type->credentialFields());

        if ($provider !== null && $provider->exists) {
            $credentials = $provider->credentials ?? [];

            foreach ($expectedKeys as $key) {
                if (isset($submittedCredentials[$key]) && $submittedCredentials[$key] !== '') {
                    $credentials[$key] = $submittedCredentials[$key];
                }
            }
        } else {
            $credentials = [];

            foreach ($expectedKeys as $key) {
                $credentials[$key] = $submittedCredentials[$key] ?? null;
            }

            $missing = array_filter($expectedKeys, static fn (string $key): bool => empty($credentials[$key]));

            if ($missing !== []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'credentials' => 'All credential fields are required for a new provider: '.implode(', ', $missing),
                ]);
            }
        }

        return [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'credentials' => $credentials,
            'capabilities' => array_values(array_intersect(
                $validated['capabilities'] ?? [],
                array_column($type->possibleCapabilities(), 'value'),
            )),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'priority' => $validated['priority'] ?? 0,
        ];
    }
}