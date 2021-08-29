<?php

namespace Proto\Http\Controllers;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Redirect;

class TFAController extends Controller
{
    /**
     * @param Request $request
     * @param Google2FA $google2fa
     * @return RedirectResponse
     */
    public function timebasedPost(Request $request, Google2FA $google2fa)
    {
        $user = Auth::user();
        $code = $request->input('2facode');
        $secret = $request->input('2fakey');

        if ($google2fa->verifyKey($secret, $code)) {
            $user->tfa_totp_key = $secret;
            $user->save();
            $request->session()->flash('flash_message', 'Time-Based 2 Factor Authentication enabled!');
        } else {
            $request->session()->flash('flash_message', 'The code you entered is not correct. Remove the account from your 2FA app and try again.');
        }

        return Redirect::route('user::dashboard');
    }

    /**
     * @param Request $request
     * @param Google2FA $google2fa
     * @return RedirectResponse
     */
    public function timebasedDelete(Request $request, Google2FA $google2fa)
    {
        $user = Auth::user();
        $code = $request->input('2facode');

        if ($user->tfa_totp_key !== null) {
            if ($google2fa->verifyKey($user->tfa_totp_key, $code)) {
                $user->tfa_totp_key = null;
                $user->save();
            } else {
                $request->session()->flash('flash_message', 'Invalid code supplied, could not disable 2FA!');

                return Redirect::back();
            }
        }

        $request->session()->flash('flash_message', 'Time-Based 2 Factor Authentication disabled!');
        return Redirect::route('user::dashboard');
    }
}
