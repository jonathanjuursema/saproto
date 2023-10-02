<?php

/**
 * Kindly copied from
 * http://stackoverflow.com/questions/33791494/laravel-restricting-access-for-development-site.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevelopmentAccess
{
    /** @var string[] */
    protected $except = [
        'webhook/*',
    ];

    /**
     * Client IPs allowed to access the app. Defaults are loopback IPv4 and IPv6 for use in local development.
     *
     * @var string[]
     */
    protected $ipWhitelist = [];

    /**
     * Whether the current request client is allowed to access the app.
     *
     * @return bool
     */
    protected function clientNotAllowed()
    {
        $isAllowedIP = in_array(request()->ip(), $this->ipWhitelist);

        return auth()->guest() || ! $isAllowedIP;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Closure
     */
    public function handle($request, $next)
    {
        if (config('app-proto.debug-whitelist') == null) {
            return $next($request);
        }

        $this->ipWhitelist = explode(',', config('app-proto.debug-whitelist'));

        if ($this->clientNotAllowed()) {
            config(['app.debug' => false]);
            abort(403);
        }

        return $next($request);
    }
}
