<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

/**
 * Phase N1 (Authentication Foundation) — Google Sign-In via
 * laravel/socialite. Requires:
 *   1. composer require laravel/socialite (this app's own network
 *      restrictions mean this couldn't be run automatically — see
 *      this phase's own deploy notes).
 *   2. GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REDIRECT_URI
 *      in .env, read by config/services.php's own 'google' block.
 *   3. Socialite's own service provider — auto-discovered by Laravel's
 *      package auto-discovery, no manual config/app.php edit needed on
     a normal install.
 *
 * ACCOUNT MATCHING RULE, in order:
 *   1. google_id already on file for THIS Google account -> log that
 *      user in directly (returning user, most common case after the
 *      first sign-in).
 *   2. No google_id match, but a user already exists with THIS SAME
 *      email (registered the normal password way, or via a different
 *      provider) -> link this Google identity onto that EXISTING
 *      account (sets google_id, backfills avatar_url if not already
 *      set) rather than creating a confusing second account for the
 *      same person. Their email is already Google-verified, so
     email_verified_at is set too if it wasn't already.
 *   3. Neither matches -> create a brand new account, google_id set,
 *      email_verified_at set immediately (see App\Models\User's own
 *      docblock for why a Google-verified email skips this app's own
 *      separate verification step), no password set at all (a
 *      Google-only account authenticates exclusively via Google until
 *      the person separately sets a password, if this app ever adds
 *      that as an option later).
 */
final class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            /** @var SocialiteUser $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('login')
                ->with('status', 'Google sign-in failed or was cancelled — please try again.');
        }

        $user = User::query()->where('google_id', $googleUser->getId())->first();

        if ($user === null) {
            $user = User::query()->where('email', $googleUser->getEmail())->first();
        }

        if ($user !== null) {
            $user->forceFill([
                'google_id' => $user->google_id ?? $googleUser->getId(),
                'avatar_url' => $user->avatar_url ?? $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::query()->create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                // A Google-only account has no password of its own —
                // a long, random, never-shown value fills the NOT NULL
                // column without this account ever being a real,
                // guessable password login.
                'password' => Str::random(40),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        // Phase N4 (User Dashboard) — see
        // App\Http\Controllers\Auth\AuthenticatedSessionController::store()'s
        // own identical comment.
        return redirect()->intended(route('dashboard', absolute: false));
    }
}