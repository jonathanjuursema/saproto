<?php

namespace App\Http;

use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\DevelopmentAccess;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnforceHTTPS;
use App\Http\Middleware\EnforceTFA;
use App\Http\Middleware\EnforceWizard;
use App\Http\Middleware\ForceDomain;
use App\Http\Middleware\Member;
use App\Http\Middleware\ProBoto;
use App\Http\Middleware\Saml;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\Utwente;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Csp\AddCspHeaders;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        HandleCors::class,
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        StartSession::class,
        TrustProxies::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            EnforceHTTPS::class,
            DevelopmentAccess::class,
            EnforceTFA::class,
            EnforceWizard::class,
            ApiMiddleware::class,
            AddCspHeaders::class,
        ],
        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $middlewareAliases = [
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
    ];
}
