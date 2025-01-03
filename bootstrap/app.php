<?php

use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\DevelopmentAccess;
use App\Http\Middleware\EnforceHTTPS;
use App\Http\Middleware\EnforceTFA;
use App\Http\Middleware\EnforceWizard;
use App\Http\Middleware\ForceDomain;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\Member;
use App\Http\Middleware\ProBoto;
use App\Http\Middleware\Saml;
use App\Http\Middleware\Utwente;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Sentry\Laravel\Integration;
use Spatie\Csp\AddCspHeaders;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/minisites.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->web(append: [
            EnforceHTTPS::class,
            DevelopmentAccess::class,
            EnforceTFA::class,
            EnforceWizard::class,
            ApiMiddleware::class,
            AddCspHeaders::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'member' => Member::class,
            'utwente' => Utwente::class,
            'forcedomain' => ForceDomain::class,
            'saml' => Saml::class,
            'throttle' => ThrottleRequests::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'proboto' => ProBoto::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/*',
            'saml2/*',
            'api/*',
            'image/*',
            'file/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
