<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuditController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Versioned under /api/v1 (Prompt 18.2): every route in this file so far
| belongs to v1, grouped under a 'v1' path and route-name prefix so a
| future v2 group can be added beside it without touching these routes
| or the App\Http\Controllers\Api\V1 controllers behind them.
|
| {audit} resolves via Audit's uuid route key (see
| App\Models\Audit::getRouteKeyName()), the same binding the web routes
| already use, so API and web URLs both identify an audit the same way.
| ->missing() gives a "no such audit" request a clean JSON 404 instead
| of Laravel's default HTML/JSON exception page, so every response from
| this API — success or not — has the same JSON shape.
|
*/

Route::prefix('v1')
    ->name('v1.')
    ->group(function (): void {
        Route::get('/audits/{audit}', [AuditController::class, 'show'])
            ->name('audits.show')
            ->missing(static fn (): JsonResponse => response()->json([
                'message' => 'Audit not found.',
            ], 404));
    });
