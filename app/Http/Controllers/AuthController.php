<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetEmail;
use App\Mail\PwnedPasswordNotification;
use App\Mail\RegistrationConfirmation;
use App\Mail\UsernameReminderEmail;
use App\Models\AchievementOwnership;
use App\Models\Address;
use App\Models\Alias;
use App\Models\Bank;
use App\Models\EmailListSubscription;
use App\Models\HashMapItem;
use App\Models\Member;
use App\Models\PasswordReset;
use App\Models\RfidCard;
use App\Models\User;
use App\Models\WelcomeMessage;
use App\Rules\NotUtwenteEmail;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mail;
use nickurt\PwnedPasswords\PwnedPasswords;
use OneLogin\Saml2\AuthnRequest;
use PragmaRX\Google2FA\Google2FA;
use Redirect;
use Session;

class AuthController extends Controller
{
    /* These are the regular, non-static methods serving as entry point to the AuthController */

    /**
     * @return View|RedirectResponse
     */
    public function getLogin(Request $request)
    {
        if (Auth::check()) {
            if ($request->has('SAMLRequest')) {
                return self::handleSAMLRequest(Auth::user(), $request->input('SAMLRequest'));
            }

            return Redirect::route('homepage');
        } else {
            if ($request->has('SAMLRequest')) {
                Session::flash('incoming_saml_request', $request->get('SAMLRequest'));
            }

            return view('auth.login');
        }
    }

    /**
     * Handle a submitted log-in form. Returns the application's response.
     *
     * @param  Request  $request The request object, needed for the log-in data.
     * @param  Google2FA  $google2fa The Google2FA object, because this is apparently the only way to access it.
     * @return RedirectResponse
     */
    public function postLogin(Request $request, Google2FA $google2fa)
    {
        Session::keep('incoming_saml_request');

        // User is already logged in
        if (Auth::check()) {
            self::postLoginRedirect();
        }
        // Catch a login form submission for two-factor authentication.
        if ($request->session()->has('2fa_user')) {
            return self::handleTwofactorSubmit($request, $google2fa);
        }

        // Otherwise, this is a regular login.
        return self::handleRegularLogin($request);
    }

    /** @return RedirectResponse */
    public function getLogout()
    {
        Auth::logout();

        return Redirect::route('homepage');
    }

    /** @return RedirectResponse */
    public function getLogoutRedirect(Request $request)
    {
        Auth::logout();

        return Redirect::route($request->route, $request->parameters);
    }

    /**
     * @return View|RedirectResponse
     */
    public function getRegister(Request $request)
    {
        if (Auth::check()) {
            Session::flash('flash_message', 'You already have an account. To register an account, please log off.');

            return Redirect::route('user::dashboard');
        }

        if ($request->wizard) {
            Session::flash('wizard', true);
        }

        return view('users.register');
    }

    /**
     * @return RedirectResponse
     */
    public function postRegister(Request $request)
    {
        if (Auth::check()) {
            Session::flash('flash_message', 'You already have an account. To register an account, please log off.');

            return Redirect::route('user::dashboard');
        }

        Session::flash('register_persist', $request->all());
        $this->validate($request, [
            'email' => ['required', 'unique:users', 'email', new NotUtwenteEmail()],
            'name' => 'required|string',
            'calling_name' => 'required|string',
            'g-recaptcha-response' => 'required|recaptcha',
            'privacy_policy_acceptance' => 'present',
        ]);

        $this->registerAccount($request);

        Session::flash('flash_message', 'Your account has been created. You will receive an e-mail with instructions on how to set your password shortly.');

        return Redirect::route('homepage');
    }

    /**
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function postRegisterSurfConext(Request $request)
    {
        if (Auth::check()) {
            Session::flash('flash_message', 'You already have an account. To register an account, please log off.');

            return Redirect::route('user::dashboard');
        }

        if (! Session::has('surfconext_create_account')) {
            Session::flash('flash_message', 'Account creation expired. Please try again.');

            return Redirect::route('login::show');
        }

        $remote_data = Session::get('surfconext_create_account');

        $request->request->add([
            'email' => $remote_data['mail'],
            'name' => $remote_data['name'],
            'calling_name' => $remote_data['calling-name'],
            'edu_username' => $remote_data['uid-full'],
            'utwente_username' => $remote_data['org'] == 'utwente.nl' ? $remote_data['uid'] : null,
        ]);

        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'calling_name' => 'required|string',
            'privacy_policy_acceptance' => 'present',
        ]);

        $new_user = $this->registerAccount($request);

        $new_user->fill([
            'edu_username' => $request->get('edu_username'),
            'utwente_username' => $request->get('utwente_username'),
        ]);

        $new_user->save();

        if (Session::get('wizard')) {
            HashMapItem::create([
                'key' => 'wizard',
                'subkey' => $new_user->id,
                'value' => 1,
            ]);
        }

        Session::flash('flash_message', 'Your account has been created. You will receive a confirmation e-mail shortly.');

        return Redirect::route('login::edu');
    }

    /**
     * @param  Request  $request
     * @return User
     */
    private function registerAccount($request)
    {
        $user = User::create($request->only(['email', 'name', 'calling_name']));

        if (Session::get('wizard')) {
            HashMapItem::create([
                'key' => 'wizard',
                'subkey' => $user->id,
                'value' => 1,
            ]);
        }

        $user->save();

        Mail::to($user)->queue((new RegistrationConfirmation($user))->onQueue('high'));

        self::dispatchPasswordEmailFor($user);

        EmailListController::autoSubscribeToLists('autoSubscribeUser', $user);

        return $user;
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteUser(Request $request)
    {
        $user = User::findOrFail($request->id ?? Auth::id());

        if ($user->name != $request->name) {
            Session::flash('flash_message', 'You need to correctly input the user\'s name before the account is deactivated.');

            return Redirect::back();
        }

        if (Auth::id() != $user->id && Auth::user()->cannot('sysadmin')) {
            Session::flash('flash_message', 'You cannot deactivate someone else\'s account.');
        }

        if (Auth::id() == $user->id) {
            $password = $request->input('password');
            $auth_check = self::verifyCredentials($user->email, $password);
            if ($auth_check == null || $auth_check->id != $user->id) {
                Session::flash('flash_message', 'You need to provide a valid password to delete an account.');

                return Redirect::back();
            }
        }

        if ($user->hasUnpaidOrderlines()) {
            Session::flash('flash_message', 'An account cannot be deactivated while it has open payments!');

            return Redirect::route('omnomcom::orders::list');
        }

        if ($user->member) {
            Session::flash('flash_message', 'An account cannot be deactivated while it still has an active membership.');

            return Redirect::back();
        }

        Address::where('user_id', $user->id)->delete();
        Bank::where('user_id', $user->id)->delete();
        EmailListSubscription::where('user_id', $user->id)->delete();
        AchievementOwnership::where('user_id', $user->id)->delete();
        Alias::where('user_id', $user->id)->delete();
        RfidCard::where('user_id', $user->id)->delete();
        WelcomeMessage::where('user_id', $user->id)->delete();

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

        $user->did_study_create = 0;
        $user->phone_visible = 0;
        $user->address_visible = 0;
        $user->receive_sms = 0;

        $user->email = 'deleted-'.$user->id.'@deleted.'.config('proto.emaildomain');

        // Save and softDelete.
        $user->save();
        $user->delete();

        Session::flash('flash_message', 'The account has been deactivated.');

        return Redirect::route('homepage');
    }

    /** @return View */
    public function getPasswordResetEmail()
    {
        return view('auth.passreset_mail');
    }

    /**
     * @return RedirectResponse
     */
    public function postPasswordResetEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user !== null) {
            self::dispatchPasswordEmailFor($user);
        }

        Session::flash('flash_message', 'If an account exists at this e-mail address, you will receive an e-mail with instructions to reset your password.');

        return Redirect::route('login::show');
    }

    /**
     * @param  string  $token The reset token, as e-mailed to the user.
     * @return View|RedirectResponse
     *
     * @throws Exception
     */
    public function getPasswordReset(Request $request, $token)
    {
        PasswordReset::where('valid_to', '<', date('U'))->delete();
        $reset = PasswordReset::where('token', $token)->first();
        if ($reset !== null) {
            return view('auth.passreset_pass', ['reset' => $reset]);
        } else {
            Session::flash('flash_message', 'This reset token does not exist or has expired.');

            return Redirect::route('login::resetpass');
        }
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function postPasswordReset(Request $request)
    {
        PasswordReset::where('valid_to', '<', date('U'))->delete();
        $reset = PasswordReset::where('token', $request->token)->first();
        if ($reset !== null) {
            if ($request->password !== $request->password_confirmation) {
                Session::flash('flash_message', 'Your passwords don\'t match.');

                return Redirect::back();
            } elseif (strlen($request->password) < 10) {
                Session::flash('flash_message', 'Your new password should be at least 10 characters long.');

                return Redirect::back();
            }
            $reset->user->setPassword($request->password);
            PasswordReset::where('token', $request->token)->delete();
            Session::flash('flash_message', 'Your password has been changed.');

            return Redirect::route('login::show');
        } else {
            Session::flash('flash_message', 'This reset token does not exist or has expired.');

            return Redirect::route('login::resetpass');
        }
    }

    /**
     * @return View|RedirectResponse
     */
    public function getPasswordChange(Request $request)
    {
        if (! Auth::check()) {
            Session::flash('flash_message', 'Please log-in first.');

            return Redirect::route('login::show');
        }

        return view('auth.passchange');
    }

    /**
     * @param  Request  $request The request object.
     * @return View|RedirectResponse
     *
     * @throws Exception
     */
    public function postPasswordChange(Request $request)
    {
        if (! Auth::check()) {
            Session::flash('flash_message', 'Please log-in first.');

            return Redirect::route('login::show');
        }

        $user = Auth::user();

        $pass_old = $request->get('old_password');
        $pass_new1 = $request->get('new_password1');
        $pass_new2 = $request->get('new_password2');

        $user_verify = self::verifyCredentials($user->email, $pass_old);

        if ($user_verify?->id === $user->id) {
            if ($pass_new1 !== $pass_new2) {
                Session::flash('flash_message', 'The new passwords do not match.');

                return view('auth.passchange');
            } elseif (strlen($pass_new1) < 10) {
                Session::flash('flash_message', 'Your new password should be at least 10 characters long.');

                return view('auth.passchange');
            } elseif ((new PwnedPasswords())->setPassword($pass_new1)->isPwnedPassword()) {
                Session::flash('flash_message', 'The password you would like to set is unsafe because it has been exposed in one or more data breaches. Please choose a different password and <a href="https://wiki.proto.utwente.nl/ict/pwned-passwords" target="_blank">click here to learn more</a>.');

                return view('auth.passchange');
            } else {
                $user->setPassword($pass_new1);
                Session::flash('flash_message', 'Your password has been changed.');

                return Redirect::route('user::dashboard');
            }
        }

        Session::flash('flash_message', 'Old password incorrect.');

        return view('auth.passchange');
    }

    /** @return View|RedirectResponse */
    public function getPasswordSync(Request $request)
    {
        if (! Auth::check()) {
            Session::flash('flash_message', 'Please log-in first.');

            return Redirect::route('login::show');
        }

        return view('auth.sync');
    }

    /**
     * @return View|RedirectResponse
     *
     * @throws Exception
     */
    public function postPasswordSync(Request $request)
    {
        if (! Auth::check()) {
            Session::flash('flash_message', 'Please log-in first.');

            return Redirect::route('login::show');
        }

        $pass = $request->get('password');
        $user = Auth::user();
        $user_verify = self::verifyCredentials($user->email, $pass);

        if ($user_verify?->id === $user->id) {
            $user->setPassword($pass);
            Session::flash('flash_message', 'Your password was successfully synchronized.');

            return Redirect::route('user::dashboard');
        } else {
            Session::flash('flash_message', 'Password incorrect.');

            return view('auth.sync');
        }
    }

    /** @return RedirectResponse */
    public function startSurfConextAuth()
    {
        Session::reflash();

        return Redirect::route('saml2_login', ['idpName' => 'surfconext']);
    }

    /**
     * This is where we land after a successful SurfConext SSO auth.
     * We do the authentication here because only using the Event handler for the SAML login doesn't let us do the proper redirects.
     *
     * @return View|RedirectResponse
     */
    public function postSurfConextAuth(Request $request)
    {
        if (! Session::has('surfconext_sso_user')) {
            return Redirect::route('login::show');
        }

        $remoteUser = Session::pull('surfconext_sso_user');
        $remoteData = [
            'uid' => $remoteUser[config('saml2-attr.uid')][0],
            'surname' => array_key_exists(config('saml2-attr.surname'), $remoteUser) ? $remoteUser[config('saml2-attr.surname')][0] : null,
            'mail' => $remoteUser[config('saml2-attr.email')][0],
            'givenname' => array_key_exists(config('saml2-attr.givenname'), $remoteUser) ? $remoteUser[config('saml2-attr.givenname')][0] : null,
            'org' => isset($remoteUser[config('saml2-attr.institute')]) ? $remoteUser[config('saml2-attr.institute')][0] : 'utwente.nl',
        ];
        $remoteEduUsername = $remoteData['uid'].'@'.$remoteData['org'];
        $remoteFullName = 'User';
        $remoteCallingName = 'User';
        if ($remoteData['surname'] && $remoteData['givenname']) {
            $remoteFullName = $remoteData['givenname'].' '.$remoteData['surname'];
            $remoteCallingName = $remoteData['givenname'];
        } elseif ($remoteData['surname'] || $remoteData['givenname']) {
            $remoteFullName = $remoteData['surname'] ? $remoteData['surname'] : $remoteData['givenname'];
            $remoteCallingName = $remoteFullName;
        }
        $remoteData['name'] = $remoteFullName;
        $remoteData['calling-name'] = $remoteCallingName;
        $remoteData['uid-full'] = $remoteEduUsername;

        // We can be here for two reasons:
        // Reason 1: we were trying to link a university account to a user
        if (Session::has('link_edu_to_user')) {
            $user = Session::get('link_edu_to_user');
            $user->utwente_username = ($remoteData['org'] == 'utwente.nl' ? $remoteData['uid'] : null);
            $user->edu_username = $remoteEduUsername;
            $user->save();
            Session::flash('flash_message', "We linked your institution account $remoteEduUsername to your Proto account.");
            if (Session::has('link_wizard')) {
                return Redirect::route('becomeamember');
            } else {
                return Redirect::route('user::dashboard');
            }
        }

        // Reason 2: we were trying to login using a university account
        Session::keep('incoming_saml_request');
        $localUser = User::where('edu_username', $remoteEduUsername)->first();

        // If we can't find a user account to login to, we have to options:
        if ($localUser == null) {
            $localUser = User::where('email', $remoteData['mail'])->first();

            // If we recognize the e-mail address, reminder the user they may already have an account.
            if ($localUser) {
                Session::flash('flash_message', 'We recognize your e-mail address, but you have not explicitly allowed authentication to your account using your university account. You can link your university account on your dashboard after you have logged in.');

                return Redirect::route('login::show');
            }

            // Else, we'll allow them to create an account using their university account
            else {
                Session::flash('surfconext_create_account', $remoteData);
                $request->session()->reflash();

                return view('users.registersurfconext', ['remote_data' => $remoteData]);
            }
        }

        $localUser->name = $remoteData['name'];
        $localUser->save();

        return self::continueLogin($localUser);
    }

    /**
     * @return View|RedirectResponse
     */
    public function requestUsername(Request $request)
    {
        if ($request->has('email')) {
            $user = User::whereEmail($request->get('email'))->first();
            if ($user) {
                self::dispatchUsernameEmailFor($user);
            }
            Session::flash('flash_message', 'If your e-mail belongs to an account, we have just e-mailed you the username.');

            return Redirect::route('login::show');
        } else {
            return view('auth.username');
        }
    }

    /* These are the static helper functions of the AuthController for more overview and modularity. Heuh! */

    /**
     * This static function takes a supplied username and password,
     * and returns the associated user if the combination is valid.
     * Accepts either Proto username or e-mail and password.
     *
     * @param  string  $username Email address or Proto username.
     * @param  string  $password
     * @return User|null The user associated with the credentials, or null if no user could be found or credentials are invalid.
     *
     * @throws Exception
     */
    public static function verifyCredentials($username, $password)
    {
        $user = User::where('email', $username)->first();

        if ($user == null) {
            $member = Member::where('proto_username', $username)->first();
            $user = ($member ? $member->user : null);
        }

        if ($user != null && Hash::check($password, $user->password)) {
            if (HashMapItem::where('key', 'pwned-pass')->where('subkey', $user->id)->first() === null && (new PwnedPasswords())->setPassword($password)->isPwnedPassword()) {
                Mail::to($user)->queue((new PwnedPasswordNotification($user))->onQueue('high'));
                HashMapItem::create(['key' => 'pwned-pass', 'subkey' => $user->id, 'value' => date('r')]);
            }

            return $user;
        }

        return null;
    }

    /**
     * Login the supplied user and perform post-login checks and redirects.
     *
     * @param  User  $user The user to be logged in.
     * @return RedirectResponse
     */
    public static function loginUser($user)
    {
        Auth::login($user, true);
        if (Session::has('incoming_saml_request')) {
            return self::handleSAMLRequest(Auth::user(), Session::get('incoming_saml_request'));
        }

        return self::postLoginRedirect();
    }

    /**
     * The login has been completed (successfully or not). Return where the user is supposed to be redirected.
     *
     * @return RedirectResponse
     */
    private static function postLoginRedirect()
    {
        return Redirect::intended('/');
    }

    /**
     * @return RedirectResponse
     */
    private static function handleRegularLogin(Request $request)
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
     * We know a user has identified itself, but we still need to check for other stuff like SAML or Two Factor Authentication. We do this here.
     *
     * @param  User  $user The username to be logged in.
     * @return View|RedirectResponse
     */
    public static function continueLogin($user)
    {
        // Catch users that have 2FA enabled.
        if ($user->tfa_totp_key) {
            Session::flash('2fa_user', $user);

            return view('auth.2fa');
        } else {
            return self::loginUser($user);
        }
    }

    /**
     * Handle the submission of two factor authentication data. Return the application's response.
     *
     * @param  Google2FA  $google2fa The Google2FA object, because this is apparently the only way to access it.
     * @return View|RedirectResponse
     */
    private static function handleTwoFactorSubmit(Request $request, Google2FA $google2fa)
    {
        $user = $request->session()->get('2fa_user');

        /* Time based Two Factor Authentication (Google2FA) */
        if ($user->tfa_totp_key && $request->has('2fa_totp_token') && $request->input('2fa_totp_token') != '') {

            // Verify if the response is valid.
            if ($google2fa->verifyKey($user->tfa_totp_key, $request->input('2fa_totp_token'))) {
                return self::loginUser($user);
            } else {
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

    /**
     * Static helper function that will dispatch a password reset email for a user.
     *
     * @param  User  $user
     */
    public static function dispatchPasswordEmailFor($user)
    {
        $reset = PasswordReset::create([
            'email' => $user->email,
            'token' => str_random(128),
            'valid_to' => strtotime('+1 hour'),
        ]);

        Mail::to($user)->queue((new PasswordResetEmail($user, $reset->token))->onQueue('high'));
    }

    /**
     * Static helper function that will dispatch a username reminder email for a user.
     */
    public static function dispatchUsernameEmailFor(User $user)
    {
        Mail::to($user)->queue((new UsernameReminderEmail($user))->onQueue('high'));
    }

    /**
     * Static helper function to handle a SAML request.
     * The function expects an authenticated user for which to complete the SAML request.
     * This function assumes the user has already been authenticated one way or another.
     *
     * @param  User  $user The (currently logged in) user to complete the SAML request for.
     * @param  string  $saml The SAML data (deflated and encoded).
     * @return View|RedirectResponse
     */
    private static function handleSAMLRequest($user, $saml)
    {
        if (! $user->member) {
            Session::flash('flash_message', 'Only members can use the Proto SSO. You only have a user account.');

            return Redirect::route('becomeamember');
        }

        // SAML is transmitted base64 encoded and GZip deflated.
        $xml = gzinflate(base64_decode($saml));

        // LightSaml Magic. Taken from https://imbringingsyntaxback.com/implementing-a-saml-idp-with-laravel/
        $deserializationContext = new \LightSaml\Model\Context\DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        $authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
        $authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

        if (! array_key_exists(base64_encode($authnRequest->getAssertionConsumerServiceURL()), config('saml-idp.sp'))) {
            Session::flash('flash_message', 'You are using an unknown Service Provider. Please contact the System Administrators to get your Service Provider whitelisted for Proto SSO.');

            return Redirect::route('login::show');
        }

        $response = self::buildSAMLResponse($user, $authnRequest);

        $bindingFactory = new \LightSaml\Binding\BindingFactory();
        $postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
        $messageContext = new \LightSaml\Context\Profile\MessageContext();
        $messageContext->setMessage($response)->asResponse();

        $httpResponse = $postBinding->send($messageContext);

        return view('auth.saml.samlpostbind', ['response' => $httpResponse->getData()['SAMLResponse'], 'destination' => $httpResponse->getDestination()]);
    }

    /**
     * Another static helper function to build a SAML response based on a user and a request.
     *
     * @param  User  $user The user to generate the SAML response for.
     * @param  AuthnRequest  $authnRequest The request to generate a SAML response for.
     * @return \LightSaml\Model\Protocol\Response A LightSAML response.
     */
    private static function buildSAMLResponse($user, $authnRequest)
    {

        // LightSaml Magic. Taken from https://imbringingsyntaxback.com/implementing-a-saml-idp-with-laravel/
        $audience = config('saml-idp.sp')[base64_encode($authnRequest->getAssertionConsumerServiceURL())]['audience']; /** @phpstan-ignore-line */
        $destination = $authnRequest->getAssertionConsumerServiceURL(); /** @phpstan-ignore-line */
        $issuer = config('saml-idp.idp.issuer');

        $certificate = \LightSaml\Credential\X509Certificate::fromFile(base_path().config('saml-idp.idp.cert'));
        $privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey(base_path().config('saml-idp.idp.key'), '', true);

        $response = new \LightSaml\Model\Protocol\Response();
        $response
            ->addAssertion($assertion = new \LightSaml\Model\Assertion\Assertion())
            ->setID(\LightSaml\Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination($destination)
            ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
            ->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode('urn:oasis:names:tc:SAML:2.0:status:Success')))
            ->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));

        $email = $user->email;

        $assertion
            ->setId(\LightSaml\Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
            ->setSubject(
                (new \LightSaml\Model\Assertion\Subject())
                    ->setNameID(new \LightSaml\Model\Assertion\NameID(
                        $email,
                        \LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL
                    ))
                    ->addSubjectConfirmation(
                        (new \LightSaml\Model\Assertion\SubjectConfirmation())
                            ->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
                            ->setSubjectConfirmationData(
                                (new \LightSaml\Model\Assertion\SubjectConfirmationData())
                                    ->setInResponseTo($authnRequest->getId())
                                    ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
                                    ->setRecipient($authnRequest->getAssertionConsumerServiceURL()) /* @phpstan-ignore-line */
                            )
                    )
            )
            ->setConditions(
                (new \LightSaml\Model\Assertion\Conditions())
                    ->setNotBefore(new \DateTime())
                    ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
                    ->addItem(
                        new \LightSaml\Model\Assertion\AudienceRestriction($audience)
                    )
            )
            ->addItem(
                (new \LightSaml\Model\Assertion\AttributeStatement())
                    ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
                        'urn:mace:dir:attribute-def:mail',
                        $email
                    ))
                    ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
                        'urn:mace:dir:attribute-def:displayName',
                        $user->name
                    ))
                    ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
                        'urn:mace:dir:attribute-def:cn',
                        $user->name
                    ))
                    ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
                        'urn:mace:dir:attribute-def:givenName',
                        $user->name
                    ))
                    ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
                        'urn:mace:dir:attribute-def:uid',
                        $user->member->proto_username
                    ))
            )
            ->addItem(
                (new \LightSaml\Model\Assertion\AuthnStatement())
                    ->setAuthnInstant(new \DateTime('-10 MINUTE'))
                    ->setSessionIndex('_some_session_index')
                    ->setAuthnContext(
                        (new \LightSaml\Model\Assertion\AuthnContext())
                            ->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
                    )
            );

        return $response;
    }
}
