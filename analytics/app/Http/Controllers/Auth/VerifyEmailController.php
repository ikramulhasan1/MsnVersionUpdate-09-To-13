<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Phase N1 (Authentication Foundation) — EmailVerificationRequest
 * itself (Laravel's own built-in FormRequest) already validates the
 * link's signature and that the {id}/{hash} route parameters match
 * the currently-authenticated user, so a tampered or expired link
 * never reaches this method's own body at all.
 */
final class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard', ['verified' => 1]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('dashboard', ['verified' => 1]);
    }
}