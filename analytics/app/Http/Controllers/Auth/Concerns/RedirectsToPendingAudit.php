<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\Audit;
use Illuminate\Http\RedirectResponse;

/**
 * Phase N1.5 (Homepage + Quick Audit Hero) — used by every controller
 * that represents a real "authentication just completed" moment:
 * App\Http\Controllers\Auth\AuthenticatedSessionController::store()
 * (existing user logging in), App\Http\Controllers\Auth\VerifyEmailController
 * (brand new user just verified — the true final gate for a fresh
 * registration, since 'verified' middleware blocks audits.show until
 * this happens), and App\Http\Controllers\Auth\GoogleAuthController::callback()
 * (already verified immediately at sign-in).
 *
 * See App\Http\Controllers\AuditController::quickAudit()'s own
 * docblock for where session('pending_audit_uuid') gets SET in the
 * first place (the homepage Hero's own public, no-login-required
 * submission) — this trait is the other half: once a real session
 * exists, claim that anonymous Audit row (give it a real owner) and
 * send the person straight to it, rather than a generic dashboard
 * that gives no hint their own audit is sitting there ready.
 */
trait RedirectsToPendingAudit
{
    /**
     * @param  \Illuminate\Http\RedirectResponse  $fallback  where to
     *         redirect when there's no real pending audit to claim (or
     *         it can't be claimed for some reason) — the caller
     *         supplies this rather than a bare route name so
     *         redirect()->intended(...)'s own existing behavior
     *         (respecting a protected-route redirect captured before
     *         login, unrelated to the Quick Audit flow this trait
     *         exists for) still works for every other login scenario.
     */
    protected function redirectAfterAuthentication(RedirectResponse $fallback): RedirectResponse
    {
        $pendingUuid = session()->pull('pending_audit_uuid');

        if (is_string($pendingUuid) && $pendingUuid !== '') {
            $audit = Audit::query()->where('uuid', $pendingUuid)->first();

            // Only ever claims a genuinely UNOWNED audit — an audit
            // that already has a real user_id (e.g. someone re-using
            // an old session value, or a race where two tabs both
            // triggered this) is left alone rather than silently
            // reassigned to whoever happens to authenticate next.
            if ($audit !== null && $audit->user_id === null) {
                $audit->update(['user_id' => auth()->id()]);

                return redirect()->route('audits.show', $audit)
                    ->with('status', 'Here are your results!');
            }
        }

        return $fallback;
    }
}