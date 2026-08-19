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

        // TODO (Phase N4, User Dashboard): once a real 'dashboard'
        // route exists, redirect there instead of 'home' — a logged-in
        // person's own landing page after signing in should be their
        // dashboard, not the public marketing homepage they just came
        // from. Left as 'home' for now since Phase N4 hasn't been
        // built yet and this route must not 404.
        return redirect()->intended(route('home', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}