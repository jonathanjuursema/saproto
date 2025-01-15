<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Override;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies headers for the application.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PREFIX |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Override the laravel's trustproxies function to return the trusted proxies
     * in order to load the proxies from the config file.
     */
    #[Override]
    protected function proxies(): array|string|null
    {
        return config('app.trusted_proxies');
    }
}
