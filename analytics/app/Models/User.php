<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Phase N1 (Authentication Foundation) — the first real use of this
 * model anywhere in the app; every column below except id/name/email/
 * password/timestamps was added specifically to support real
 * authentication (see this migration:
 * database/migrations/2026_08_19_000000_add_google_auth_columns_to_users_table.php).
 *
 * Implements MustVerifyEmail — a freshly-registered (password-based)
 * account must confirm ownership of the email address before the rest
 * of the app treats it as fully active (see the 'verified' middleware
 * applied to every protected route group in routes/web.php). A Google-
 * signed-in account (App\Http\Controllers\Auth\GoogleAuthController)
 * is marked verified immediately at creation instead — Google has
 * already confirmed that email on this app's behalf, so asking the
 * person to verify it again would be redundant friction with no real
 * security benefit.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * A single letter/pair of letters for an avatar placeholder
     * (navbar user menu, notification bell dropdown, ...) when
     * $avatar_url is null — the common case for a password-registered
     * account that never linked Google. Never a broken <img>, never a
     * generic anonymous-silhouette icon that gives no sense of WHICH
     * user is signed in.
     */
    public function initials(): string
    {
        $parts = array_values(array_filter(explode(' ', trim($this->name))));

        if ($parts === []) {
            return '?';
        }

        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1));
        }

        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
}