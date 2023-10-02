<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Redirect;
use Session;

class SurfConextController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if ($request->wizard) {
            Session::flash('wizard', true);
        }

        Session::flash('link_edu_to_user', $user);

        if ($request->has('wizard')) {
            Session::flash('link_wizard', true);
        }

        return Redirect::route('login::edu');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $user->utwente_username = null;
        $user->edu_username = null;
        $user->utwente_department = null;
        $user->save();

        Session::flash('flash_message', 'The link with your university account has been deleted.');

        return Redirect::route('user::dashboard');
    }
}
