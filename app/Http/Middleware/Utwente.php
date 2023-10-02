<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class Utwente
{
    /**
     * This middleware only allows access if the visiting user is authenticated and is a member.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (Auth::user()?->utwente_username != null) {
            return $next($request);
        }
        abort(403, 'You need to have an active University of Twente account to continue. If you have one, please link it on your dashboard.');
    }
}
