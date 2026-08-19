<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Phase N1.5 (Free Trial) — must run before
        // RolesAndPermissionsSeeder's own ADMIN_EMAIL bootstrap has any
        // real bearing on this, and well before any real registration
        // ever happens: App\Http\Controllers\Auth\RegisteredUserController::store()
        // looks up the is_default_trial plan at registration time, so
        // this seeder needs to have run at least once before the first
        // real signup.
        $this->call(PlansSeeder::class);

        // Phase N3 (Role & Permission System) — must run AFTER any
        // User rows this method creates above it, so
        // RolesAndPermissionsSeeder's own ADMIN_EMAIL bootstrap (see
        // that class's own docblock) has a real chance of finding a
        // matching user if ADMIN_EMAIL happens to be one created here.
        // In practice this app's own real Admin is a person who
        // registers through the normal /register flow, not this
        // factory-created test user — but the ordering is still
        // correct either way.
        $this->call(RolesAndPermissionsSeeder::class);
    }
}