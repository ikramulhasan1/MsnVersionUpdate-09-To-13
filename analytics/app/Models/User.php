<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
   

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasRoles, Notifiable;

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
        // Phase N1.5 — see this class's own plan()/onTrial() methods.
        'plan_id',
        'subscribed_at',
        'trial_ends_at',
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
            'subscribed_at' => 'datetime',
            'trial_ends_at' => 'datetime',
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

    /**
     * Phase N3 — a thin, readable wrapper over $this->hasRole('Admin')
     * (HasRoles' own method), used in several places
     * (resources/views/layouts/partials/sidebar.blade.php's own
     * conditional Admin Panel link, App\Http\Middleware — anywhere a
     * plain boolean reads more clearly than a string-comparison role
     * check spelled out inline).
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Phase N1.5 (Free Trial) — null for any account created before
     * this column existed, or one an Admin hasn't assigned a plan to
     * yet.
     */
    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Plan::class);
    }

    /**
     * True only while both a plan AND a real trial_ends_at are set
     * AND that date hasn't passed — a paid, non-expiring plan
     * (trial_ends_at null) is never "on trial" even though it's a
     * real assigned plan, and a user with no plan at all isn't either.
     */
    public function onTrial(): bool
    {
        return $this->plan_id !== null
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    /**
     * True specifically for a trial that existed and has now passed —
     * NOT true for someone who was never on a trial at all (a plan
     * with no expiry, or no plan whatsoever). Used by
     * resources/views/dashboard/index.blade.php's own "Upgrade Now"
     * banner and App\Http\Middleware\EnsurePlanAllowsFeature to tell
     * "you were never on a trial" apart from "your trial just ended",
     * which call for different messaging.
     */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isPast();
    }

    /**
     * Phase N1.5 — the ONE method every feature-gating check in this
     * app (App\Http\Middleware\EnsurePlanAllowsFeature,
     * App\Audit\Services\AuditService::submit()'s own daily-limit
     * check) actually calls, rather than each reaching into
     * $this->plan->features directly. False whenever there's no plan
     * at all OR the plan itself has expired (trialExpired()) — an
     * expired trial blocks EVERY plan feature, not just the ones the
     * plan's own JSON already marked false, since the whole plan
     * period is over.
     */
    public function planAllowsFeature(string $key): bool
    {
        if ($this->plan === null || $this->trialExpired()) {
            return false;
        }

        return $this->plan->allowsFeature($key);
    }

    /**
     * Phase N5 (Dynamic Pricing/Subscription).
     */
    public function planUpgradeRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PlanUpgradeRequest::class);
    }

    /**
     * Phase N6 — backs the Billing History page
     * (resources/views/billing/history.blade.php).
     */
    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * Phase O5 (Keyword List/Project Management).
     */
    public function keywordLists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\KeywordList::class);
    }
}