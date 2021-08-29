<?php

namespace Proto\Http\Middleware;

use Session;

class Saml
{
    public function handle($request, $next)
    {
        Session::reflash();
        return $next($request);
    }
}
