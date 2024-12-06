<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Member
{
    /**
     * This middleware only allows access if the visiting user is authenticated and is a member.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::check()) {
            abort(403, 'You need to be logged in to access this page.');
        }

        if (! Auth::user()?->is_member) {
            abort(403, 'You need to be a member to access this page.');
        }

        return $next($request);
    }
}
