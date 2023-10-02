<?php

namespace App\Http\Middleware;

use App;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Session;

class EnforceTFA
{
    /**
     * This middleware forces power users to use TFA before they can do anything else.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (App::environment('production') && Auth::check() && Auth::user()->hasRole(config('proto.tfaroles')) && (! Auth::user()->hasTFAEnabled())) {
            if (! $request->is('user/dashboard') && ! $request->is('auth/logout') && ! $request->is('user/quit_impersonating') && ! $request->is('user/*/2fa/*') && ! $request->is('user/2fa/*') && ! $request->is('api/*')) {
                Session::flash('flash_message', 'Your account permissions require you to enable Two Factor Authentication on your account before being able to use your account.');

                return Redirect::route('user::dashboard', ['#2fa']);
            }
        }

        return $next($request);
    }
}
