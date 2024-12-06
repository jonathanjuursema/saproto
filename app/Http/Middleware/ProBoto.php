<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ProBoto
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $authHeader = request()->header('Authorization');
        if ($authHeader) {
            //Remove the "Bearer" part from the header
            $secret = explode(' ', $authHeader)[1];
            if ($secret === Config::string('app-proto.proboto-secret')) {
                return $next($request);
            }
        }

        return response('Unauthorized.', 401);
    }
}
