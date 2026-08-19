<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Phase N3 (Role & Permission System) — spatie/laravel-permission's
        // own middleware classes need EXPLICIT alias registration on
        // Laravel 11+'s new bootstrap/app.php-based middleware config
        // (there is no more app/Http/Kernel.php with a $routeMiddleware
        // array for a package to hook into automatically the way it
        // could on older Laravel versions) — without this block, every
        // `->middleware('permission:...')` / `->middleware('role:...')`
        // in routes/web.php throws "Target class [permission] does not
        // exist" at request time, not at deploy time, so this is easy
        // to miss until the very first protected route is actually hit.
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // Phase N1.5 (Free Trial) — see
            // App\Http\Middleware\EnsurePlanAllowsFeature's own
            // docblock for how this differs from 'permission' above.
            'plan' => \App\Http\Middleware\EnsurePlanAllowsFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();