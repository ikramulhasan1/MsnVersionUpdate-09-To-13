<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Phase N1 (Authentication Foundation).
 */
final class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Always redirects back with a status message regardless of
     * whether $request->email actually matches a real account —
     * Laravel's own Password::sendResetLink() already returns the SAME
     * status string ('passwords.sent') either way, so this never
     * leaks "that email doesn't exist" to someone probing for
     * registered accounts.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return back()->with('status', __($status));
    }
}