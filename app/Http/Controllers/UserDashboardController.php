<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

use Redirect;
use Hash;

use Proto\Http\Requests;
use Proto\Http\Controllers\Controller;

use Proto\Models\User;

use Auth;
use Session;

class UserDashboardController extends Controller
{

    /**
     * Display the dashboard for a specific user.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        if ($id == null) {
            $id = Auth::id();
        }

        $user = User::find($id);

        if ($user == null) {
            abort(404);
        }

        if ($user->id != Auth::id() && !Auth::user()->can('board')) {
            abort(403);
        }

        $qrcode = null;
        if (!$user->tfa_totp_key) {
            $google2fa = new Google2FA();
            $request->session()->flash('2fa_secret', ($request->session()->has('2fa_secret') ? $request->session()->get('2fa_secret') : $google2fa->generateSecretKey(32)));
            $qrcode = $google2fa->getQRCodeGoogleUrl('S.A.%20Proto', str_replace(' ', '%20', $user->name), $request->session()->get('2fa_secret'));
        }

        return view('users.dashboard.dashboard', ['user' => $user, 'tfa_qrcode' => $qrcode]);
    }

    public function update($id = null, Request $request)
    {
        if ($id == null) {
            $id = Auth::id();
        }

        $user = User::find($id);

        if ($user == null) {
            abort(404);
        }

        if ($user->id != Auth::id() && !User::can('board')) {
            abort(403);
        }

        $userdata = array('email' => $user->email);

        if (($user->email != $request->input('email')) || ($request->input('newpassword') != "")) {
            if (!Hash::check($request->input('old_pass'), $user->password)) {
                Session::flash("flash_message", "You need to enter your current password in order to change your e-mail or password. No changes made to e-mail or password.");
                return Redirect::route('user::dashboard', ['id' => $user->id]);
            } else {
                $userdata['email'] = $request->input('email');

                if ($request->input('newpassword') != "") {
                    if ($request->input('newpassword') != $request->input('newpassword2')) {
                        Session::flash("flash_message", "The two new passwords weren't identical. Password not changed.");
                    } else {
                        $userdata['password'] = Hash::make($request->input('newpassword'));
                    }
                }
            }
        }

        $userdata['phone'] = $request->input('phone');
        $userdata['website'] = $request->input('website');
        $userdata['phone_visible'] = $request->has('phone_visible');
        $userdata['receive_newsletter'] = $request->has('receive_newsletter');
        $userdata['receive_sms'] = $request->has('receive_sms');

        if (!$user->validate($userdata)) {
            return Redirect::route('user::dashboard', ['id' => $user->id])->withErrors($user->errors());
        }

        $user->fill($userdata);
        $user->save();
        Session::flash("flash_message", "Changes saved.");
        return Redirect::route('user::dashboard', ['id' => $user->id]);

    }

}
