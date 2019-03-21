<?php

namespace Proto\Http\Middleware;

use Closure;

class ForceDomain
{
    /**
     * This middleware forces the entire application to use SSL. We like that, because it's secure.
     *
     * Shamelessly copied from: http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $force = config('app.forcedomain');

        if ($force != null && $request->getHttpHost() != $force) {
            return redirect()->to(config('app-proto.app-url') . '/' . ($request->path() == '/' ? '' : $request->path()), 301);
        }

        return $next($request);
    }
}
