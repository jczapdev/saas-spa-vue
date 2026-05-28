<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    /*
    |--------------------------------------------------------------------------
    | Routing Configuration
    |--------------------------------------------------------------------------
    | Central domains, tenant domains, API and console routes
    */
    ->withRouting(
        using: function () {
            $centralDomains = config('tenancy.central_domains', []);

            /*
            |--------------------------------------------------------------
            | Central Routes (Admin / Platform)
            |--------------------------------------------------------------
            */
            foreach ($centralDomains as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }

            /*
            |--------------------------------------------------------------
            | Tenant Routes (Customer / Client)
            |--------------------------------------------------------------
            */
            Route::middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ])->group(base_path('routes/tenant.php'));

            /*
            |--------------------------------------------------------------
            | API Routes (Central & Tenant)
            |--------------------------------------------------------------
            */
            Route::prefix('api')
                ->middleware([
                    'api',
                    InitializeTenancyByDomain::class,
                ])
                ->group(base_path('routes/api.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {
        // Cookies que no deben ser encriptadas
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        
        // Habilitar API stateful para Sanctum
        $middleware->statefulApi();

        // Middlewares adicionales para rutas web
        $middleware->web(append: [
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Redirección para invitados según si están en tenant o central
        $middleware->redirectGuestsTo(function (Request $request) {
            if (function_exists('tenancy') && tenancy()->initialized) {
                return route('tenant.login');
            }

            return route('login');
        });
    })
    
    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Renderizar respuestas JSON para las rutas API
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    
    ->create();