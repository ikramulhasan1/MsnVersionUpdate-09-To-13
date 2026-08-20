<?php

namespace App\Providers;

use App\Audit\Export\Pdf\AuditPdfExportService;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Repositories\AuditRepository;
use App\Audit\Repositories\Contracts\AuditRepositoryInterface;
use App\Audit\Services\AuditService;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Models\User;
use App\Payments\SslCommerzGateway;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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

        // Phase N6 (Multiple Payment Methods) — SslCommerzGateway's own
        // constructor takes plain config values, not auto-resolvable
        // without an explicit binding the way a class with only
        // type-hinted class dependencies would be.
        $this->app->singleton(SslCommerzGateway::class, static fn (): SslCommerzGateway => new SslCommerzGateway(
            storeId: (string) config('services.sslcommerz.store_id'),
            storePassword: (string) config('services.sslcommerz.store_password'),
            sandbox: (bool) config('services.sslcommerz.sandbox'),
        ));
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

        // Phase N6 (Multiple Payment Methods) — see routes/web.php's
        // own comment on the 'cashier.webhook' route for the full
        // "why": without this, Cashier's own auto-registered webhook
        // route would point at Cashier's generic base controller
        // instead of App\Http\Controllers\Payments\StripeWebhookController,
        // and every real Stripe payment would silently never activate
        // a plan.
        \Laravel\Cashier\Cashier::ignoreRoutes();

        // PRODUCTION GAP CLOSED (post-Phase N3) — this app's own
        // explicit requirement was "Admin (সব এক্সেস)" — Admin should
        // never need any INDIVIDUAL permission granted at all, every
        // feature check should simply pass. Phase N3's own
        // RolesAndPermissionsSeeder tried to achieve this by
        // syncPermissions()-ing every KNOWN permission onto the Admin
        // role — which works for permissions that existed when that
        // seeder last ran, but silently stops covering a NEW
        // permission added later (e.g. a future Phase N7 API
        // permission) unless someone remembers to re-run that seeder.
        // A real Admin promoted by hand via `$user->assignRole('Admin')`
        // (the documented tinker command for bootstrapping the very
        // first Admin) already has every CURRENT permission from that
        // sync — but this Gate::before() is what makes "Admin bypasses
        // everything" actually TRUE and future-proof, not just true by
        // coincidence of when the seeder last ran. Every
        // ->middleware('permission:...') check anywhere in this app
        // (routes/web.php) goes through Laravel's own Gate system
        // under the hood, so this one callback covers all of them —
        // no route file needs to special-case Admin individually.
        // PRODUCTION INCIDENT — read before reverting to the simpler
        // "Admin bypass only, defer to spatie for everyone else"
        // version of this callback: on this app's own live deployment,
        // spatie/laravel-permission's OWN Gate registration (the
        // mechanism this package normally uses to make
        // $user->can('some-permission-name') work automatically)
        // was NOT correctly authorizing a real, confirmed permission —
        // $user->hasPermissionTo('run-audit') returned true (spatie's
        // own direct, non-Gate method — always reliable), but
        // $user->can('run-audit') / Gate::forUser($user)->check(...)
        // returned false for the SAME user and SAME permission,
        // immediately after a permission-cache reset ruled out staleness
        // as the cause. Root cause not fully isolated (a registration-
        // order interaction between this app's own Gate::before() and
        // spatie's own is suspected, but not confirmed) — rather than
        // depend on spatie's own hook running correctly at all, this
        // callback now independently verifies hasPermissionTo() itself
        // for ability names that are real Permission rows, making it
        // the single, self-sufficient source of truth for every
        // permission check in this app: Admin bypasses unconditionally,
        // then hasPermissionTo() is checked directly, and only an
        // ability name that ISN'T a real permission at all falls
        // through as null (deferring to whatever other Gate/policy
        // logic exists, currently none in this app, harmlessly).
        Gate::before(static function (User $user, string $ability): ?bool {
            if ($user->isAdmin()) {
                return true;
            }

            if (\Spatie\Permission\Models\Permission::where('name', $ability)->where('guard_name', 'web')->exists()) {
                return $user->hasPermissionTo($ability) ? true : false;
            }

            return null;
        });
    }
}