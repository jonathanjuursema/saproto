<?php

namespace App\Http\Controllers;

use App\Mail\PwnedPasswordNotification;
use App\Models\HashMapItem;
use App\Models\Member;
use App\Models\User;
use App\Rules\NotUtwenteEmail;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use nickurt\PwnedPasswords\PwnedPasswords;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    /**
     * Show the register page
     */
    public function registerIndex(Request $request): RedirectResponse|View
    {
        if (Auth::check()) {
            Session::flash('flash_message', 'You already have an account. To register an account, please log off.');

            return Redirect::route('user::dashboard::show');
        }

        return view('users.register');
    }

    /**
     * Register a new account.
     * It is possible to register an account without a UT account, the user will automatically be redirected.
     */
    public function register(Request $request): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'unique:users', 'email:rfc', new NotUtwenteEmail],
            'create_without_ut_account' => 'nullable',
            'name' => 'required_if:create_without_ut_account,true|string',
            'calling_name' => 'required_if:create_without_ut_account,true|string',
            'g-recaptcha-response' => 'required|recaptcha',
            'privacy_policy_acceptance' => 'required|accepted',
        ]);

        // create with UT account, redirect to UT login
        if (! $request->has('create_without_ut_account')) {
            return SurfConextController::createAccount($request->email);
        }

        User::register($request->email, $request->calling_name, $request->name);

        Session::flash('flash_message', 'Your account has been created. You will receive an e-mail with instructions on how to set your password shortly.');

        return Redirect::route('homepage');
    }

    /**
     * Show the login page
     */
    public function loginIndex(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a submitted log-in form. Returns the application's response.
     *
     * @param  Request  $request  The request object, needed for the log-in data.
     * @param  Google2FA  $google2fa  The Google2FA object, because this is apparently the only way to access it.
     *
     * @throws Exception
     */
    public function login(Request $request, Google2FA $google2fa): View|RedirectResponse
    {
        // User is already logged in
        if (Auth::check()) {
            self::postLoginRedirect();
        }

        // Catch a login form submission for two-factor authentication.
        if ($request->session()->has('2fa_user')) {
            return $this->handleTwoFactorSubmit($request, $google2fa);
        }

        // Otherwise, this is a regular login.
        return $this->handleRegularLogin($request);
    }

    /**
     * Log out the user and redirect to the homepage.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        return Redirect::route('homepage');
    }

    /**
     * Log out the user and redirect arbitrarily.
     */
    public function logoutAndRedirect(Request $request): RedirectResponse
    {
        Auth::logout();

        return Redirect::route($request->route, $request->parameters);
    }

    /* These are the static helper functions of the AuthController for more overview and modularity. */
    /**
     * This static function takes a supplied username and password,
     * and returns the associated user if the combination is valid.
     * Accepts either Proto username or e-mail and password.
     *
     * @param  string  $username  Email address or Proto username.
     * @return User|null The user associated with the credentials, or null if no user could be found or credentials are invalid.
     *
     * @throws Exception
     */
    public static function verifyCredentials(string $username, string $password)
    {
        $user = User::query()->where('email', $username)->first();

        if ($user == null) {
            $member = Member::query()->where('proto_username', $username)->first();
            $user = ($member ? $member->user : null);
        }

        if ($user != null && Hash::check($password, $user->password)) {
            if (HashMapItem::query()->where('key', 'pwned-pass')->where('subkey', $user->id)->first() === null && (new PwnedPasswords)->setPassword($password)->isPwnedPassword()) {
                Mail::to($user)->queue((new PwnedPasswordNotification($user))->onQueue('high'));
                HashMapItem::query()->create(['key' => 'pwned-pass', 'subkey' => $user->id, 'value' => date('r')]);
            }

            return $user;
        }

        return null;
    }

    /**
     * Login the supplied user and perform post-login checks and redirects.
     *
     * @param  User  $user  The user to be logged in.
     * @return RedirectResponse
     */
    public static function loginUser(User $user)
    {
        Auth::login($user, true);

        return self::postLoginRedirect();
    }

    /**
     * The login has been completed (successfully or not). Return where the user is supposed to be redirected.
     *
     * @return RedirectResponse
     */
    private static function postLoginRedirect()
    {
        return Redirect::intended();
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    private function handleRegularLogin(Request $request)
    {
        $username = $request->input('email');
        $password = $request->input('password');

        $user = self::verifyCredentials($username, $password);

        if ($user) {
            return self::continueLogin($user);
        }

        Session::flash('flash_message', 'Invalid username or password provided.');

        return Redirect::route('login::show');
    }

    /**
     * We know a user has identified itself, but we still need to check for other stuff like Two-Factor Authentication. We do this here.
     *
     * @param  User  $user  The username to be logged in.
     * @return View|RedirectResponse
     */
    public static function continueLogin(User $user)
    {
        // Catch users that have 2FA enabled.
        if ($user->tfa_totp_key) {
            Session::flash('2fa_user', $user);

            return view('auth.2fa');
        }

        return self::loginUser($user);
    }

    /**
     * Handle the submission of two-factor authentication data. Return the application's response.
     *
     * @param  Google2FA  $google2fa  The Google2FA object, because this is apparently the only way to access it.
     * @return View|RedirectResponse
     */
    private function handleTwoFactorSubmit(Request $request, Google2FA $google2fa)
    {
        $user = $request->session()->get('2fa_user');

        /* Time based Two-Factor Authentication (Google2FA) */
        if ($user->tfa_totp_key && $request->has('2fa_totp_token') && $request->input('2fa_totp_token') != '') {

            // Verify if the response is valid.
            try {
                if ($google2fa->verifyKey($user->tfa_totp_key, $request->input('2fa_totp_token'))) {
                    return self::loginUser($user);
                }

                Session::flash('flash_message', 'Your code is invalid. Please try again.');
                $request->session()->reflash();

                return view('auth.2fa');
            } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException) {
                Session::flash('flash_message', 'Your code is invalid. Please try again.');
                $request->session()->reflash();

                return view('auth.2fa');
            }
        }

        /* Something we don't recognize */
        Session::flash('flash_message', 'Please complete the requested challenge.');
        $request->session()->reflash();

        return view('auth.2fa');
    }
}
