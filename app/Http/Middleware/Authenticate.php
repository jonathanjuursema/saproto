<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Redirect;

class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Factory
     */
    protected $auth;

    /**
     * Handle an incoming request.
     **/
    public function handle($request, Closure $next, ...$guards): mixed
    {
        if ($request->ajax() && $request->auth->guest()) {
            return response('Unauthorized.', 401);
        }

        if ($request->auth->guest()) {
            return Redirect::route('login::show');
        }

        return $next($request);

    }
}
