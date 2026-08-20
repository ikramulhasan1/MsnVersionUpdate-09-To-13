<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\ApiProviders\ApiProviderConnectionTester;
use App\Enums\ApiProviderType;
use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A convenience layer on top of Phase O1's own "API Providers" CRUD —
 * this controller creates/updates NOTHING that couldn't already be
 * done by hand through the normal Admin "New Provider" form three
 * separate times (see App\Http\Controllers\Admin\ApiProviderController,
 * unchanged). DataForSEO's own Keywords Data, Labs, and Backlinks
 * products all authenticate with the SAME account login/password (see
 * App\Enums\ApiProviderType::credentialFields()'s own docblock) — this
 * page exists purely so an Admin types that ONE pair once instead of
 * three times across three separate forms.
 *
 * Every capability
 * ApiProviderType::DATAFORSEO_KEYWORDS/DATAFORSEO_LABS/DATAFORSEO_BACKLINKS
 * ->possibleCapabilities() lists is turned on for each row here — an
 * Admin who wants finer-grained control (e.g. deliberately NOT
 * offering search_intent) should use the normal per-provider Edit form
 * afterward, same as they could with any provider created by hand.
 *
 * Re-running this form (e.g. to rotate a password) UPDATES the same
 * three rows rather than creating duplicates — matched by BOTH type
 * AND a specific, reserved name ("DataForSEO Keywords (Quick Setup)"
 * etc.), so a DIFFERENT, manually-created row of the same type (an
 * Admin who set up a second DataForSEO Keywords provider by hand, say,
 * to route to a different DataForSEO account) is never touched or
 * confused with this one.
 */
final class DataForSeoQuickSetupController extends Controller
{
    /**
     * The reserved names this quick-setup flow owns — anything with a
     * DIFFERENT name, even of the same type, was created some other
     * way and is left alone by store() below.
     */
    private const array MANAGED_NAMES = [
        ApiProviderType::DATAFORSEO_KEYWORDS->value => 'DataForSEO Keywords (Quick Setup)',
        ApiProviderType::DATAFORSEO_LABS->value => 'DataForSEO Labs (Quick Setup)',
        ApiProviderType::DATAFORSEO_BACKLINKS->value => 'DataForSEO Backlinks (Quick Setup)',
    ];

    public function show(): View
    {
        $existing = ApiProvider::query()
            ->whereIn('type', array_keys(self::MANAGED_NAMES))
            ->whereIn('name', self::MANAGED_NAMES)
            ->get()
            ->keyBy(fn (ApiProvider $provider): string => $provider->type->value);

        return view('admin.api-providers.quick-setup-dataforseo', [
            'alreadySetUp' => $existing->count() === 3,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request, ApiProviderConnectionTester $tester): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $credentials = ['login' => $validated['login'], 'password' => $validated['password']];

        $createdOrUpdated = [];

        foreach (self::MANAGED_NAMES as $typeValue => $name) {
            $type = ApiProviderType::from($typeValue);

            $provider = ApiProvider::query()->updateOrCreate(
                ['type' => $typeValue, 'name' => $name],
                [
                    'credentials' => $credentials,
                    'capabilities' => array_map(
                        static fn ($capability): string => $capability->value,
                        $type->possibleCapabilities(),
                    ),
                    'is_active' => true,
                    'priority' => 0,
                ],
            );

            $createdOrUpdated[] = $provider;
        }

        // A real, live test against ONE of the three (Keywords Data —
        // the cheapest, lightest of the three products to verify
        // against) rather than all three separately; since all three
        // share the identical login/password pair, one successful test
        // confirms the credentials themselves are valid for all of
        // them — a failure here means the credentials are wrong
        // regardless of which specific product would have been tested.
        $testResult = $tester->test($createdOrUpdated[0]);

        foreach ($createdOrUpdated as $provider) {
            $provider->update([
                'last_tested_at' => now(),
                'last_test_succeeded' => $testResult['success'],
                'last_test_message' => $testResult['message'],
            ]);
        }

        return redirect()->route('admin.api-providers.index')->with(
            $testResult['success'] ? 'status' : 'test_error',
            $testResult['success']
                ? 'DataForSEO Quick Setup complete — Keywords, Labs, and Backlinks are all active.'
                : "Quick Setup saved the credentials, but the test call failed: {$testResult['message']}",
        );
    }
}