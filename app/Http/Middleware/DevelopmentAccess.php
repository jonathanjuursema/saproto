<?php

/**
 * Kindly copied from
 * http://stackoverflow.com/questions/33791494/laravel-restricting-access-for-development-site.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DevelopmentAccess
{
    /** @var string[] */
    protected array $except = [
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
     */
    protected function clientNotAllowed(): bool
    {
        $isAllowedIP = in_array(request()->ip(), $this->ipWhitelist);
        if (auth()->guest()) {
            return true;
        }

        return ! $isAllowedIP;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Config::get('app-proto.debug-whitelist') == null) {
            return $next($request);
        }

        $this->ipWhitelist = explode(',', Config::string('app-proto.debug-whitelist'));

        if ($this->clientNotAllowed()) {
            config(['app.debug' => false]);
            abort(403);
        }

        return $next($request);
    }
}
