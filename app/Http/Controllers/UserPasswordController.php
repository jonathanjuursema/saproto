<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use nickurt\PwnedPasswords\PwnedPasswords;

class UserPasswordController extends Controller
{
    /**
     * Show the page to request a password reset via an email.
     */
    public function requestPasswordResetIndex(): View
    {
        return view('auth.passreset_mail');
    }

    /**
     * Send an email with a password reset link to the user if found.
     */
    public function requestPasswordReset(Request $request): RedirectResponse
    {
        $user = User::query()->where('email', $request->email)->first();
        if ($user !== null) {
            $user->sendPasswordResetEmail();
        }

        Session::flash('flash_message', 'If an account exists at this e-mail address, you will receive an e-mail with instructions to reset your password.');

        return Redirect::route('login::show');
    }

    /**
     * Show the page to reset the password.
     *
     * @param  string  $token  The reset token, as e-mailed to the user.
     */
    public function resetPasswordIndex(string $token): RedirectResponse|View
    {
        PasswordReset::query()->where('valid_to', '<', date('U'))->delete();
        $reset = PasswordReset::query()->where('token', $token)->first();
        if ($reset !== null) {
            return view('auth.passreset_pass', ['reset' => $reset]);
        }

        Session::flash('flash_message', 'This reset token does not exist or has expired.');

        return Redirect::route('login::password::reset');
    }

    /**
     * Reset the password of the user.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        PasswordReset::query()->where('valid_to', '<', date('U'))->delete();
        $reset = PasswordReset::query()->where('token', $request->token)->first();
        if ($reset !== null) {
            if ($request->password !== $request->password_confirmation) {
                Session::flash('flash_message', "Your passwords don't match.");

                return Redirect::back();
            }

            if (strlen($request->password) < 10) {
                Session::flash('flash_message', 'Your new password should be at least 10 characters long.');

                return Redirect::back();
            }

            $reset->user->setPassword($request->password);
            PasswordReset::query()->where('token', $request->token)->delete();
            Session::flash('flash_message', 'Your password has been changed.');

            return Redirect::route('login::show');
        }

        Session::flash('flash_message', 'This reset token does not exist or has expired.');

        return Redirect::route('login::password::reset');
    }

    /**
     * Show the page to synchronize passwords.
     */
    public function syncPasswordsIndex(): View
    {
        return view('auth.sync');
    }

    /**
     * Synchronize the password of the user.
     */
    public function syncPasswords(Request $request): View|RedirectResponse
    {
        $pass = $request->get('password');
        $user = Auth::user();
        $user_verify = AuthController::verifyCredentials($user->email, $pass);

        if ($user_verify?->id === $user->id) {
            $user->setPassword($pass);
            Session::flash('flash_message', 'Your password was successfully synchronized.');

            return Redirect::route('user::dashboard::show');
        }

        Session::flash('flash_message', 'Password incorrect.');

        return view('auth.sync');
    }

    /**
     * Show the page to request a username via an email.
     */
    public function forgotUsernameIndex(): View
    {
        return view('auth.username');
    }

    /**
     * Send an email with the username to the user if found.
     */
    public function forgotUsername(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::whereEmail($request->get('email'))->first();
        if ($user) {
            $user->sendForgotUsernameEmail();
        }

        Session::flash('flash_message', 'If your e-mail belongs to an account, we have just e-mailed you the username.');

        return Redirect::route('login::show');
    }

    /**
     * Show the page to gracefully change the password.
     */
    public function changePasswordIndex(): View
    {
        return view('auth.passchange');
    }

    /**
     * Change the password of the user.
     */
    public function changePassword(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        $pass_old = $request->get('old_password');
        $pass_new1 = $request->get('new_password1');
        $pass_new2 = $request->get('new_password2');

        $user_verify = AuthController::verifyCredentials($user->email, $pass_old);

        if ($user_verify?->id === $user->id) {
            if ($pass_new1 !== $pass_new2) {
                Session::flash('flash_message', 'The new passwords do not match.');

                return view('auth.passchange');
            }

            if (strlen($pass_new1) < 10) {
                Session::flash('flash_message', 'Your new password should be at least 10 characters long.');

                return view('auth.passchange');
            }

            if ((new PwnedPasswords)->setPassword($pass_new1)->isPwnedPassword()) {
                Session::flash('flash_message', 'The password you would like to set is unsafe because it has been exposed in one or more data breaches. Please choose a different password and <a href="https://wiki.proto.utwente.nl/ict/pwned-passwords" target="_blank">click here to learn more</a>.');

                return view('auth.passchange');
            }

            $user->setPassword($pass_new1);
            Session::flash('flash_message', 'Your password has been changed.');

            return Redirect::route('user::dashboard::show');
        }

        Session::flash('flash_message', 'Old password incorrect.');

        return view('auth.passchange');
    }
}
