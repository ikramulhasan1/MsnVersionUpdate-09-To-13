<?php

namespace App\Providers;

use App\Audit\Export\Pdf\AuditPdfExportService;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Repositories\AuditRepository;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Services\AuditService;
use App\Audit\Services\Contracts\AuditServiceInterface;
use Illuminate\Pagination\Paginator;
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
        // This whole app is built on Bootstrap 5 (see the CDN link in
        // resources/views/layouts/app.blade.php) — Laravel's own
        // pagination views default to Tailwind CSS classes, which would
        // render completely unstyled here. Website Discovery's result
        // list (Phase D3, resources/views/discovery/index.blade.php's
        // $websites->links() call) is this app's first use of Laravel's
        // paginator, so this wasn't needed until now.
        Paginator::useBootstrapFive();
    }
}