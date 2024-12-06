<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

class TFAController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function create(Request $request, Google2FA $google2fa)
    {
        $user = Auth::user();
        $code = $request->input('2facode');
        $secret = $request->input('2fakey');

        if ($google2fa->verifyKey($secret, $code)) {
            $user->tfa_totp_key = $secret;
            $user->save();
            Session::flash('flash_message', 'Time-Based 2 Factor Authentication enabled!');
        } else {
            Session::flash('flash_message', 'The code you entered is not correct. Remove the account from your 2FA app and try again.');
        }

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(Request $request, Google2FA $google2fa)
    {
        $user = Auth::user();
        $code = $request->input('2facode');

        if ($user->tfa_totp_key !== null) {
            if ($google2fa->verifyKey($user->tfa_totp_key, $code)) {
                $user->tfa_totp_key = null;
                $user->save();
            } else {
                Session::flash('flash_message', 'Invalid code supplied, could not disable 2FA!');

                return Redirect::back();
            }
        }

        Session::flash('flash_message', 'Time-Based 2 Factor Authentication disabled!');

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function adminDestroy(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);
        $user->tfa_totp_key = null;
        $user->save();

        Session::flash('flash_message', 'Time-Based 2 Factor Authentication disabled!');

        return Redirect::back();
    }
}
