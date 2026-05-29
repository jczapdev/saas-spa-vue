<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */
    ->withRouting(
        using: function () {
            $centralDomains = config('tenancy.central_domains', []);

            /*
            |--------------------------------------------------------------
            | Web — Central
            |--------------------------------------------------------------
            */
            foreach ($centralDomains as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }

            /*
            |--------------------------------------------------------------
            | Web — Tenant
            |--------------------------------------------------------------
            */
            Route::middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ])->group(base_path('routes/tenant.php'));

            /*
            |--------------------------------------------------------------
            | API — Central
            |--------------------------------------------------------------
            */
            foreach ($centralDomains as $domain) {
                Route::prefix('api')
                    ->middleware('api')
                    ->domain($domain)
                    ->group(base_path('routes/api.php'));
            }

            /*
            |--------------------------------------------------------------
            | API — Tenant
            |--------------------------------------------------------------
            */
            Route::prefix('api')
                ->middleware([
                    'api',
                    InitializeTenancyByDomain::class,
                ])
                ->group(base_path('routes/api-tenant.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies('*');

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->statefulApi();

        $middleware->redirectGuestsTo(function (Request $request) {
            if (tenant()) {
                return route('tenant.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (tenant()) {
                return route('tenant.dashboard');
            }
            return route('dashboard');
        });

        $middleware->alias([
            'auth.tenant'  => \Illuminate\Auth\Middleware\Authenticate::class . ':tenant',
            'guest.tenant' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class . ':tenant',
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })

    ->create();