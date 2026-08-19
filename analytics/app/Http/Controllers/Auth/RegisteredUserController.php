<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Auth\NewUserOnboarder;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Phase N1 (Authentication Foundation).
 */
final class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly NewUserOnboarder $onboarder,
    ) {
    }

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ])->validate();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Phase N1.5 — see App\Auth\NewUserOnboarder's own docblock
        // for exactly what this assigns (the 'User' role and the
        // default Free Trial plan) and the production gap it closes.
        $this->onboarder->onboard($user);

        // Fires App\Notifications\... (Laravel's own built-in
        // VerifyEmail notification, wired up via MustVerifyEmail on
        // the User model itself) — sends the actual verification
        // email. Nothing in THIS class needs to know how that email
        // gets built or sent.
        event(new Registered($user));

        Auth::login($user);

        // Note: NOT App\Http\Controllers\Auth\Concerns\RedirectsToPendingAudit
        // here — a brand new registration still has to clear email
        // verification first (the 'verified' middleware blocks
        // audits.show until then), so the real "authentication
        // complete" moment for a fresh signup is
        // App\Http\Controllers\Auth\VerifyEmailController, not this
        // method. session('pending_audit_uuid') (if any) simply stays
        // in the session, untouched, until that later point.
        return redirect()->route('verification.notice');
    }
}