<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Proto\Http\Requests;
use Proto\Http\Controllers\Controller;

use Proto\Models\User;

use Auth;
use Redirect;
use Session;

class SurfConextController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if ($request->wizard) Session::flash("wizard", true);

        Session::flash('link_edu_to_user', $user);

        if ($request->has('wizard')) {
            Session::flash('link_wizard', true);
        }

        return Redirect::route('login::edu');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $user->utwente_username = null;
        $user->edu_username = null;
        $user->utwente_department = null;
        $user->save();

        $request->session()->flash('flash_message', 'The link with your university account has been deleted.');
        return Redirect::route('user::dashboard');
    }
}
