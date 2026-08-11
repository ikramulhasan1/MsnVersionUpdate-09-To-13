<?php

declare(strict_types=1);

use App\Http\Controllers\AuditController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuditController::class, 'index'])->name('home');

Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');

Route::get('/audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
Route::get('/audits/{audit}/progress', [AuditController::class, 'progress'])->name('audits.progress');
Route::get('/audits/{audit}/export', [AuditController::class, 'export'])->name('audits.export');

Route::get('/audits/{audit}/export-excel', [AuditController::class, 'exportExcel'])
    ->name('audits.export.excel');
