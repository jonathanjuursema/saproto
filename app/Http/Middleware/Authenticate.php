<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Redirect;
use Override;

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
    #[Override]
    public function handle($request, Closure $next, ...$guards): mixed
    {
        if ($request->ajax() && $this->auth->guest()) {
            return response('Unauthorized.', 401);
        }

        if ($this->auth->guest()) {
            return Redirect::route('login::show');
        }

        return $next($request);

    }
}
