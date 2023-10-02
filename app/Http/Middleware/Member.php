<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class Member
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
        if (Auth::user()?->is_member) {
            return $next($request);
        }
        abort(403, 'You need to be a member of S.A. Proto to see this page.');
    }
}
