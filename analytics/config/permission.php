<?php

declare(strict_types=1);

/**
 * Phase N3 (Role & Permission System) — spatie/laravel-permission's
 * own default published config, written by hand for the same reason
 * database/migrations/2026_08_19_000004_create_permission_tables.php's
 * own docblock explains (this app's deploy process couldn't run
 * `vendor:publish` directly). Every value below matches that package's
 * own out-of-the-box default — nothing here is app-specific tuning,
 * so this file can be safely replaced by the package's own real
 * published copy later (e.g. after a `vendor:publish --force`) without
 * losing anything this app actually depends on.
 */
return [

    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => null,
        'permission_pivot_key' => null,
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],

    'register_permission_check_method' => true,

    'register_octane_reset_listener' => false,

    'events_enabled' => false,

    'teams' => false,

    'team_resolver' => Spatie\Permission\DefaultTeamResolver::class,

    'use_passport_client_credentials' => false,

    'display_permission_in_exception' => false,

    'display_role_in_exception' => false,

    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],

];