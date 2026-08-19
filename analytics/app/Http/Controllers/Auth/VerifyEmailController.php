<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RedirectsToPendingAudit;
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
    use RedirectsToPendingAudit;

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterAuthentication(
                redirect()->route('dashboard', ['verified' => 1]),
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Phase N1.5 (Quick Audit Hero) — THIS is the real "just
        // finished authenticating" moment for a brand new registration
        // (see App\Http\Controllers\Auth\RegisteredUserController::store()'s
        // own comment on why it doesn't check
        // session('pending_audit_uuid') itself — email verification
        // sits between registering and actually being able to view a
        // protected page like audits.show).
        return $this->redirectAfterAuthentication(
            redirect()->route('dashboard', ['verified' => 1]),
        );
    }
}