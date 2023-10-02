<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Redirect;

class EnforceHTTPS
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
        if (! $request->secure() && config('app.ssl')) {
            return Redirect::secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
