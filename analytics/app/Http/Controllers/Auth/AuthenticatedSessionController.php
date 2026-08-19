<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Phase N1 (Authentication Foundation).
 */
final class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * LoginRequest::authenticate() does the real work (real credential
     * check, rate limiting) — by the time this method's own body runs,
     * the session is already a genuinely authenticated one.
     *
     * session()->regenerate() (not merely a side effect of
     * Auth::attempt()) — a fresh session ID after every successful
     * login prevents session fixation: an attacker who somehow knew
     * the PRE-login session ID (e.g. handed a crafted link) gains
     * nothing once the person actually logs in, since that ID is now
     * abandoned.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Phase N4 (User Dashboard) — resolves the TODO this redirect
        // originally carried: a logged-in person's own landing page
        // now really is their dashboard, not the public marketing
        // homepage they just came from.
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}