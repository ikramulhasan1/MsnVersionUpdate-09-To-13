<?php

namespace App\Providers;

use App\Audit\Export\Pdf\AuditPdfExportService;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Repositories\AuditRepository;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Services\AuditService;
use App\Audit\Services\Contracts\AuditServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuditServiceInterface::class,
            AuditService::class
        );
        $this->app->bind(
            AuditRepositoryInterface::class,
            AuditRepository::class
        );
        $this->app->bind(
            AuditPdfExportServiceInterface::class,
            AuditPdfExportService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
