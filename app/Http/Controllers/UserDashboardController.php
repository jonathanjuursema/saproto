<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTypeEnum;
use App\Mail\UserMailChange;
use App\Models\Member;
use App\Models\StorageEntry;
use App\Models\User;
use App\Rules\NotUtwenteEmail;
use Carbon;
use DateTime;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Milon\Barcode\DNS2D;
use PDF;
use PragmaRX\Google2FA\Google2FA;
use Spatie\Permission\Models\Permission;

class UserDashboardController extends Controller
{
    /** @return View */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        $qrcode = null;
        $tfakey = null;
        if (! $user->tfa_totp_key) {
            $google2fa = new Google2FA;
            $tfakey = $google2fa->generateSecretKey(32);
            $qrcode = (new DNS2D)->getBarcodeSVG($google2fa->getQRCodeUrl('S.A. Proto', $user->name, $tfakey), 'QRCODE');
        }

        $memberships = $user->getMemberships();

        return view('users.dashboard.dashboard', ['user' => $user, 'memberships' => $memberships, 'tfa_qrcode' => $qrcode, 'tfa_key' => $tfakey]);
    }

    /**
     * Add a new email address to the user's account.
     * This will send a verification email to the new address.
     * Board members can change the email of other users, except for when they do not have a permission of that user.
     * This is to prevent them from being able to change a sysadmin's email address and take over an account with more permissions.
     *
     * @return RedirectResponse
     */
    public function updateMail(Request $request, int $id)
    {
        $user = User::query()->findOrFail($id);

        $password = $request->input('password');
        $new_email = $request->input('email');
        $auth_check = AuthController::verifyCredentials($user->email, $password);

        if (Auth::user()->can('board')) {

            $auth_check = AuthController::verifyCredentials(Auth::user()->email, $password);

            if (! Auth::user()->can('sysadmin')) {
                foreach ($user->roles as $role) {
                    /** @var Permission $permission */
                    foreach ($role->permissions as $permission) {
                        if (! Auth::user()->can($permission->name)) {
                            abort(403, 'You can not change the email of this person!.');
                        }
                    }
                }
            }
        }

        if ($auth_check == null || ($auth_check->id != $user->id && ! $auth_check->can('board'))) {
            Session::flash('flash_message', 'You need to provide a valid password to update your e-mail address.');

            return Redirect::back();
        }

        if ($new_email !== $user->email) {
            $validator = Validator::make($request->only(['email']), [
                'email' => ['required', 'unique:users', 'email:rfc', new NotUtwenteEmail],
            ]);
            if ($validator->fails()) {

                if ($user->id == Auth::id()) {
                    return Redirect::route('user::dashboard::show')->withErrors($validator);
                }

                return Redirect::route('user::admin::details', ['id' => $user->id])->withErrors($validator);
            }

            $email = [
                'old' => $user->email,
                'new' => $new_email,
            ];

            $to = [
                (object) [
                    'email' => $email['old'],
                    'name' => $user->name,
                ],
                (object) [
                    'email' => $email['new'],
                    'name' => $user->name,
                ],
            ];

            $changer = [
                'name' => Auth::user()->name,
                'ip' => $request->ip(),
            ];

            Mail::to($to)->queue((new UserMailChange($user, $changer, $email))->onQueue('high'));
        }

        $user->email = $new_email;
        $user->save();

        Session::flash('flash_message', 'E-mail address changed.');
        if ($user->id == Auth::id()) {
            return Redirect::route('user::dashboard::show');
        }

        return Redirect::route('user::admin::details', ['id' => $user->id]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $userdata['website'] = $request->input('website');

        if ($user->phone) {
            $userdata['phone'] = str_replace([' ', '-', '(', ')'], ['', '', '', ''], $request->input('phone'));
            $userdata['phone_visible'] = $request->has('phone_visible');
            $userdata['receive_sms'] = $request->has('receive_sms');
            $validator = Validator::make($userdata, [
                'phone' => 'required|regex:(\+[0-9]{8,16})',
            ], ['phone.regex' => 'Please enter your phone number in international format, with a plus (+) and country code: +123456789012']);
            if ($validator->fails()) {
                return Redirect::route('user::dashboard::show')->withErrors($validator);
            }
        }

        if ($user->is_member) {
            $userdata['show_birthday'] = $request->has('show_birthday');
            $userdata['show_omnomcom_total'] = $request->has('show_omnomcom_total');
            $userdata['show_omnomcom_calories'] = $request->has('show_omnomcom_calories');
            $userdata['show_achievements'] = $request->has('show_achievements');
            $userdata['profile_in_almanac'] = $request->has('profile_in_almanac');
        }

        if ($request->has('disable_omnomcom')) {
            $userdata['disable_omnomcom'] = true;
        }

        $userdata['keep_omnomcom_history'] = $request->has('keep_omnomcom_history');
        $userdata['theme'] = $request->input('theme');

        $user->fill($userdata);
        $user->save();

        Session::flash('flash_message', 'Changes saved.');

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @return RedirectResponse
     */
    public function editDiet(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->diet = htmlspecialchars($request->input('diet'));
        $user->save();

        Session::flash('flash_message', 'Your diet and allergy information has been updated.');

        return Redirect::route('user::dashboard::show');
    }

    /** @return View */
    public function becomeAMemberOf()
    {
        /* @var null|User $user */
        $user = Auth::check() ? Auth::user() : null;

        $steps = [
            [
                'url' => route('login::register::index', ['wizard' => 1]),
                'unlocked' => true,
                'done' => Auth::check(),
                'heading' => 'Create an account',
                'icon' => 'fas fa-user-plus',
                'text' => 'In order to become a member of Study Association Proto, you need a Proto account. You can create that here. After creating your account, activate it by using the link mailed to you.',
            ],
            [
                'url' => Auth::check() ? route('user::surf::link', ['id' => $user->id, 'wizard' => 1]) : null,
                'unlocked' => Auth::check(),
                'done' => Auth::check() && Auth::user()->edu_username,
                'heading' => 'Link your UTwente account',
                'icon' => 'fas fa-university',
                'text' => "If you are a student at the University of Twente, we would appreciate it if you would add your student account to your Proto account. If you don't study at the University of Twente, you can skip this step.",
            ],
            [
                'url' => Auth::check() ? route('user::memberprofile::show', ['wizard' => 1]) : null,
                'unlocked' => Auth::check(),
                'done' => Auth::check() && Auth::user()->completed_profile,
                'heading' => 'Provide some personal details',
                'icon' => 'fas fa-id-card',
                'text' => 'To enter your in our member administration, you need to provide is with some extra information.',
            ],
            [
                'url' => Auth::check() ? route('user::bank::create', ['id' => $user->id, 'wizard' => 1]) : null,
                'unlocked' => Auth::check(),
                'done' => Auth::check() && Auth::user()->bank,
                'heading' => 'Provide payment details',
                'icon' => 'fas fa-euro-sign',
                'text' => 'We need your bank authorisation to withdraw your membership fee, but also your purchases within the Omnomcom and fees of activities you attend.',
            ],
            [
                'url' => Auth::check() ? route('user::address::create', ['id' => $user->id, 'wizard' => 1]) : null,
                'unlocked' => Auth::check(),
                'done' => Auth::check() && Auth::user()->address,
                'heading' => 'Provide contact details',
                'icon' => 'fas fa-home',
                'text' => 'To make you a member of our association, we need your postal address. Please add it to your account here.',
            ],
            [
                'url' => Auth::check() ? route('memberform::showsign', ['id' => $user->id, 'wizard' => 1]) : null,
                'unlocked' => Auth::check() && Auth::user()->completed_profile && Auth::user()->bank && Auth::user()->address,
                'done' => Auth::check() && ((Auth::user()->completed_profile && Auth::user()->signed_membership_form) || Auth::user()->is_member),
                'heading' => 'Sign the membership form',
                'icon' => 'fas fa-signature',
                'text' => 'To complete your membership request we need you to sign the membership form.',
            ],
            [
                'url' => route('page::show', ['slug' => 'board', 'wizard' => 1]),
                'unlocked' => Auth::check() && Auth::user()->completed_profile && Auth::user()->bank && Auth::user()->address && Auth::user()->signed_membership_form,
                'done' => Auth::check() && Auth::user()->is_member,
                'heading' => 'Become a member!',
                'icon' => 'fas fa-trophy',
                'text' => "You're almost a full-fledged Proto member! You'll need to find one of the board-members to finalize your registration. You can usually find them in the Protopolis (Zilverling A230).",
            ],
            [
                'url' => 'https://saproto.nl/go/discord',
                'unlocked' => Auth::check() && Auth::user()->is_member,
                'done' => Auth::check() && Auth::user()->discord_id,
                'heading' => 'Join the Discord server',
                'icon' => 'fa-brands fa-discord',
                'text' => 'Join our Discord server to chat with other students, play some games, send memes and more!',
                'shouldOpenInNewTab' => true,
            ],
            [
                'url' => route('user::dashboard::show', ['wizard' => 1]),
                'unlocked' => Auth::check() && Auth::user()->is_member,
                'done' => false,
                'heading' => 'Add some additional info on your dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'text' => "Congratulations, you're now a member of Study Association Proto! As a next step, we would like to guide you to your dashboard, where you can sign up for (educational) mailing lists, some of which provide you regular information about your study, as well as some that provide you with Proto-related information. On your dashboard you can also add a profile picture and indicate any allergies or dietary restrictions you may have so Proto can take those into account when you're visiting an activity.",
            ],
        ];

        $todo = [];
        $done = [];

        foreach ($steps as $step) {
            if ($step['done']) {
                $done[] = $step;
            } else {
                $todo[] = $step;
            }
        }

        return view('users.becomeamember', ['user' => $user, 'todo' => $todo, 'done' => $done]);
    }

    /** @return View|RedirectResponse */
    public function getCompleteProfile()
    {
        $user = Auth::user();
        if ($user->completed_profile) {
            Session::flash('flash_message', 'Your membership profile is already complete.');

            return Redirect::route('becomeamember');
        }

        return view('users.dashboard.completeprofile');
    }

    /**
     * @return RedirectResponse|View
     *
     * @throws Exception
     */
    public function postCompleteProfile(Request $request)
    {
        $user = Auth::user();
        if ($user->completed_profile) {
            Session::flash('flash_message', 'Your membership profile is already complete.');

            return Redirect::route('becomeamember');
        }

        $userdata = Session::has('flash_userdata') ? Session::get('flash_userdata') : $request->only(['birthdate', 'phone']);
        $userdata['phone'] = str_replace([' ', '-', '(', ')'], ['', '', '', ''], $userdata['phone']);

        $validator = Validator::make($userdata, [
            'birthdate' => 'required|date',
            'phone' => 'required|regex:(\+[0-9]{8,16})',
        ], ['phone.regex' => 'Please enter your phone number in international format, with a plus (+) and country code: +123456789012']);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        if (Session::has('flash_userdata') && $request->has('verified')) {
            $userdata['birthdate'] = date('Y-m-d', strtotime($userdata['birthdate']));
            $user->fill($userdata);
            $user->save();

            Session::flash('flash_message', 'Completed profile.');

            return Redirect::route('becomeamember');
        }

        Session::flash('flash_userdata', $userdata);

        return view(
            'users.dashboard.completeprofile_verify',
            ['userdata' => $userdata, 'age' => Carbon::instance(new DateTime($userdata['birthdate']))->age]
        );
    }

    /**
     * @return RedirectResponse|View
     */
    public function getMemberForm()
    {
        $user = Auth::user();
        if ($user->is_member || $user->signed_membership_form) {
            Session::flash('flash_message', 'You have already signed the membership form');

            return Redirect::route('becomeamember');
        }

        return view('users.dashboard.membershipform', ['user' => $user]);
    }

    /**
     * @return RedirectResponse
     */
    public function postMemberForm(Request $request)
    {
        $user = Auth::user();
        if ($user->is_member || $user->signed_membership_form) {
            Session::flash('flash_message', 'You have already signed the membership form');

            return Redirect::route('becomeamember');
        }

        if ($user->member?->membership_type === MembershipTypeEnum::PENDING) {
            $user->member->delete();
        }

        /** @var Member $member */
        $member = Member::query()->create();
        $member->user()->associate($user);
        $member->membership_type = MembershipTypeEnum::PENDING;

        $form = new PDF('P', 'A4', 'en');
        $form->writeHTML(view('users.admin.membershipform_pdf', ['user' => $user, 'signature' => $request->input('signature')]));

        $file = new StorageEntry;
        $file->createFromData($form->output('membership_form_user_'.$user->id.'.pdf', 'S'), 'application/pdf', 'membership_form_user_'.$user->id.'.pdf');

        $member->membershipForm()->associate($file);
        $member->save();

        Session::flash('flash_message', 'Thanks for signing the membership form!');

        return Redirect::route('becomeamember');
    }

    /** @return View */
    public function getClearProfile()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->completed_profile) {
            abort(403, 'You have not yet completed your membership profile.');
        }

        if ($user->is_member) {
            abort(403, 'You cannot clear your membership profile while your membership is active.');
        }

        return view('users.dashboard.clearprofile', ['user' => $user]);
    }

    /** @return RedirectResponse */
    public function postClearProfile()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->completed_profile) {
            abort(403, 'You have not yet completed your membership profile.');
        }

        if ($user->is_member) {
            abort(403, 'You cannot clear your membership profile while your membership is active.');
        }

        $user->clearMemberProfile();

        Session::flash('flash_message', 'Profile cleared.');

        return Redirect::route('user::dashboard::show');
    }

    /** @return RedirectResponse */
    public function generateKey()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->generateNewPersonalKey();

        Session::flash('flash_message', 'New personal key generated.');

        return Redirect::route('user::dashboard::show');
    }
}
