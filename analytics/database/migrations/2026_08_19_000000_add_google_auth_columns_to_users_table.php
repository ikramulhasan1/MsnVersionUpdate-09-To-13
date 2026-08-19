<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase N1 (Authentication Foundation) — google_id links a user's
 * account to their Google identity (Socialite's own unique provider
 * user ID, NOT their email — an email can change on Google's side,
 * the provider ID never does) once they've signed in with Google at
 * least once. Nullable + unique: most users will never have a value
 * here (password-only accounts), and no two users can ever share the
 * same Google identity. avatar_url stores the profile photo Google
 * returns at sign-in time — also populated for a password-registered
 * user IF they later link Google, so the app has one single "does
 * this user have an avatar" check regardless of how they signed up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('avatar_url')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['google_id', 'avatar_url']);
        });
    }
};