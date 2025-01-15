<?php

namespace App\Http\Controllers;

use App\Models\AchievementOwnership;
use App\Models\Address;
use App\Models\Alias;
use App\Models\Bank;
use App\Models\EmailListSubscription;
use App\Models\RfidCard;
use App\Models\User;
use App\Models\WelcomeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Delete a user account.
     */
    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($request->id ?? Auth::id());

        if ($user->hasUnpaidOrderlines()) {
            Session::flash('flash_message', 'An account cannot be deactivated while it has open payments!');

            return Redirect::route('omnomcom::orders::index');
        }

        if ($user->member) {
            Session::flash('flash_message', 'An account cannot be deactivated while it still has an active membership.');

            return Redirect::back();
        }

        if (Auth::id() == $user->id) {
            $password = $request->input('password');
            $auth_check = AuthController::verifyCredentials($user->email, $password);
            if ($auth_check == null || $auth_check->id != $user->id) {
                Session::flash('flash_message', 'You need to provide a valid password to deactivate your account.');

                return Redirect::back();
            }
        } else {
            if (Auth::user()->cannot('board')) {
                Session::flash('flash_message', "You cannot deactivate someone else's account.");

                return Redirect::back();
            }

            if ($user->name != $request->name) {
                Session::flash('flash_message', "You need to correctly input the user's name before the account is deactivated.");

                return Redirect::back();
            }
        }

        Address::query()->where('user_id', $user->id)->delete();
        Bank::query()->where('user_id', $user->id)->delete();
        EmailListSubscription::query()->where('user_id', $user->id)->delete();
        AchievementOwnership::query()->where('user_id', $user->id)->delete();
        Alias::query()->where('user_id', $user->id)->delete();
        RfidCard::query()->where('user_id', $user->id)->delete();
        WelcomeMessage::query()->where('user_id', $user->id)->delete();

        if ($user->photo) {
            $user->photo->delete();
        }

        $user->password = null;
        $user->remember_token = null;
        $user->birthdate = null;
        $user->phone = null;
        $user->website = null;
        $user->utwente_username = null;
        $user->edu_username = null;
        $user->utwente_department = null;
        $user->tfa_totp_key = null;

        $user->did_study_create = false;
        $user->phone_visible = false;
        $user->address_visible = false;
        $user->receive_sms = false;

        $user->email = 'deleted-'.$user->id.'@deleted.'.config('proto.emaildomain');

        // Save and softDelete.
        $user->save();
        $user->delete();

        Session::flash('flash_message', 'The account has been deactivated.');

        return Redirect::route('homepage');
    }
}
