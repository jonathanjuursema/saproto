<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class EnforceHTTPS
{
    /**
     * This middleware forces the entire application to use SSL. We like that, because it's secure.
     * Shamelessly copied from: http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->secure() && Config::boolean('app.ssl')) {
            return Redirect::secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
