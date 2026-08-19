<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase N1 (Authentication Foundation).
 */
final class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        // TODO (Phase N4, User Dashboard): redirect an already-verified
        // person to 'dashboard' instead of 'home' once that route
        // exists — see AuthenticatedSessionController::store()'s own
        // identical TODO for the same reasoning.
        return $request->user()?->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }
}