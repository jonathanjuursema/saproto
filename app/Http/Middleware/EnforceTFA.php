<?php

namespace App\Http\Middleware;

use App\Models\Committee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class EnforceTFA
{
    /**
     * This middleware forces power users to use TFA before they can do anything else.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (App::environment('production') && Auth::check() && (Auth::user()->hasRole(Config::array('proto.tfaroles')) || Auth::user()->isInCommittee(Committee::whereSlug(Config::string('proto.rootcommittee'))->firstOrFail())) && ! Auth::user()->hasTFAEnabled() && (! $request->is('user/dashboard') && ! $request->is('auth/logout') && ! $request->is('user/quit_impersonating') && ! $request->is('user/*/2fa/*') && ! $request->is('user/2fa/*') && ! $request->is('api/*'))) {
            Session::flash('flash_message', 'Your account permissions require you to enable Two Factor Authentication on your account before being able to use your account.');

            return Redirect::route('user::dashboard::show', ['#2fa']);
        }

        return $next($request);
    }
}
