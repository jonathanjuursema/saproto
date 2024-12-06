<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Utwente
{
    /**
     * This middleware only allows access if the visiting user is authenticated and is a member.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::user()?->utwente_username === null) {
            abort(403, 'You need to have an active University of Twente account to continue. If you have one, please link it on your dashboard.');
        }

        return $next($request);
    }
}
