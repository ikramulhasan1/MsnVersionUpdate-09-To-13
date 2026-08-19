<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Phase N1 (Authentication Foundation) — backs the "Resend
 * verification email" button on resources/views/auth/verify-email.blade.php.
 */
final class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // TODO (Phase N4, User Dashboard): redirect to 'dashboard'
        // instead of 'home' once that route exists.
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}