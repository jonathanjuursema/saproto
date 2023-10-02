<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Redirect;

class ForceDomain
{
    /**
     * This middleware forces the entire application to use SSL. We like that, because it's secure.
     * Shamelessly copied from: http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $force = config('app.forcedomain');
        $environment = config('app.env');

        if ($environment != 'local' && $force != null && $request->getHttpHost() != $force) {
            return Redirect::to(config('app-proto.app-url').'/'.($request->path() == '/' ? '' : $request->path()), 301);
        }

        return $next($request);
    }
}
