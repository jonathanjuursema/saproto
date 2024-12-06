<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class ForceDomain
{
    /**
     * This middleware forces the entire application to use SSL. We like that, because it's secure.
     * Shamelessly copied from: http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (App::environment('production') && config('app.forcedomain') != null && $request->getHttpHost() != Config::string('app.forcedomain')) {
            return Redirect::to(config('app-proto.app-url').'/'.($request->path() == '/' ? '' : $request->path()), 301);
        }

        return $next($request);
    }
}
