<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Session;

class Saml
{
    public function handle($request, $next): mixed
    {
        Session::reflash();

        return $next($request);
    }
}
