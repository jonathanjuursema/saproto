<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTypeEnum;
use App\Http\Requests\MP3Request;
use App\Mail\MembershipEnded;
use App\Mail\MembershipEndSet;
use App\Mail\MembershipStarted;
use App\Models\HashMapItem;
use App\Models\Member;
use App\Models\StorageEntry;
use App\Models\User;
use Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PDF;
use Spatie\Permission\Models\Permission;
use Spipu\Html2Pdf\Exception\Html2PdfException;

class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('query');
        $filter = $request->input('filter');

        $userQuery = User::withTrashed()->with('tempadmin');
        $users = match ($filter) {
            'pending' => $userQuery->whereHas('member', static function ($q) {
                $q->type(MembershipTypeEnum::PENDING)->where('deleted_at', '=', null);
            }),
            'members' => $userQuery->whereHas('member', static function ($q) {
                $q->whereNot('membership_type', MembershipTypeEnum::PENDING)->where('deleted_at', '=', null);
            }),
            'users' => $userQuery->doesntHave('member'),
            default => $userQuery,
        };

        if ($search) {
            $users = $users->where(static function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('calling_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('utwente_username', 'LIKE', "%{$search}%")
                    ->orWhereHas('member', static function ($q) use ($search) {
                        $q->where('proto_username', 'LIKE', "%{$search}%");
                    });
            });
        }

        $users = $users->paginate(20);

        return view('users.admin.overview', ['users' => $users, 'query' => $search, 'filter' => $filter]);
    }

    /**
     * @return View
     */
    public function details(int $id)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $memberships = $user->getMemberships();

        return view('users.admin.details', ['user' => $user, 'memberships' => $memberships]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->name = $request->name;
        $user->calling_name = $request->calling_name;
        $user->birthdate = strtotime($request->birthdate) !== false ? date('Y-m-d', strtotime($request->birthdate)) : null;

        $user->save();

        Session::flash('flash_message', 'User updated!');

        return Redirect::back();
    }

    public function impersonate(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        if (! Auth::user()->can('sysadmin')) {
            foreach ($user->roles as $role) {
                /** @var Permission $permission */
                foreach ($role->permissions as $permission) {
                    if (! Auth::user()->can($permission->name)) {
                        abort(403, 'You may not impersonate this person.');
                    }
                }
            }
        }

        Session::put('impersonator', Auth::user()->id);
        Auth::login($user);

        return Redirect::route('homepage');
    }

    public function quitImpersonating(): RedirectResponse
    {
        if (Session::has('impersonator')) {
            $redirect_user = Auth::id();

            $impersonator = User::query()->findOrFail(Session::get('impersonator'));
            Session::pull('impersonator');

            Auth::login($impersonator);

            return Redirect::route('user::admin::details', ['id' => $redirect_user]);
        }

        return Redirect::back();
    }

    public function addMembership(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        if ($user->is_member) {
            Session::flash('flash_message', 'This user is already a member!');

            return Redirect::back();
        }

        if (! ($user->address && $user->bank)) {
            Session::flash('flash_message', "This user really needs a bank account and address. Don't bypass the system!");

            return Redirect::back();
        }

        if ($user->member == null) {
            /** @var Member $member */
            $member = Member::query()->create();
            $member->user()->associate($user);
        }

        $member = $user->member;
        $member->created_at = Carbon::now();
        $member->membership_type = MembershipTypeEnum::REGULAR;
        $member->proto_username = Member::createProtoUsername($user->name);
        $member->save();

        Mail::to($user)->queue((new MembershipStarted($user))->onQueue('high'));

        EmailListController::autoSubscribeToLists('autoSubscribeMember', $user);

        HashMapItem::query()->create([
            'key' => 'wizard',
            'subkey' => $user->id,
            'value' => 1,
        ]);

        // Disabled because ProTube is down.
        // Removed; Here should the playsound new-member be played

        Session::flash('flash_message', 'Congratulations! '.$user->name.' is now our newest member!');

        return Redirect::back();
    }

    /**
     * Adds membership end date to member object.
     * Member object will be removed by cron job on end date.
     */
    public function endMembership(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->member()->delete();
        $user->clearMemberProfile();

        Mail::to($user)->queue((new MembershipEnded($user))->onQueue('high'));

        Session::flash('flash_message', 'Membership of '.$user->name.' has been terminated.');

        return Redirect::back();
    }

    public function EndMembershipInSeptember(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        if (! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its membership to receive an end date!');

            return Redirect::back();
        }

        $user->member->until = Carbon::parse('Last day of September')->endOfDay()->subDay()->timestamp;
        $user->member->save();
        Mail::to($user)->queue((new MemberShipEndSet($user))->onQueue('high'));
        Session::flash('flash_message', "End date for membership of $user->name set to the end of september!");

        return Redirect::back();
    }

    public function removeMembershipEnd(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        if (! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its membership to receive an end date!');

            return Redirect::back();
        }

        $user->member->until = null;
        $user->member->save();
        Session::flash('flash_message', "End date for membership of $user->name removed!");

        return Redirect::back();
    }

    public function setMembershipType(Request $request, int $id): RedirectResponse
    {
        if (! Auth::user()->can('board')) {
            abort(403, 'Only board members can do this.');
        }

        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $member = $user->member;
        $type = $request->input('type');

        match ($type) {
            'honorary' => $member->membership_type = MembershipTypeEnum::HONORARY,
            'lifelong' => $member->membership_type = MembershipTypeEnum::LIFELONG,
            'donor' => $member->membership_type = MembershipTypeEnum::DONOR,
            'pet' => $member->membership_type = MembershipTypeEnum::PET,
            default => $member->membership_type = MembershipTypeEnum::REGULAR,
        };

        $member->save();

        Session::flash('flash_message', $user->name.' is now a '.$type.' member.');

        return Redirect::back();
    }

    public function toggleNda(int $id): RedirectResponse
    {
        if (! Auth::user()->can('board')) {
            abort(403, 'Only board members can do this.');
        }

        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->signed_nda = ! $user->signed_nda;
        $user->save();

        Session::flash('flash_message', 'Toggled NDA status of '.$user->name.'. Please verify if it is correct.');

        return Redirect::back();
    }

    public function unblockOmnomcom(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->disable_omnomcom = false;
        $user->save();

        Session::flash('flash_message', 'OmNomCom unblocked for '.$user->name.'.');

        return Redirect::back();
    }

    public function toggleStudiedCreate(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->did_study_create = ! $user->did_study_create;
        $user->save();

        Session::flash('flash_message', 'Toggled CreaTe status of '.$user->name.'.');

        return Redirect::back();
    }

    public function toggleStudiedITech(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->did_study_itech = ! $user->did_study_itech;
        $user->save();

        Session::flash('flash_message', 'Toggled ITech status of '.$user->name.'.');

        return Redirect::back();
    }

    public function togglePrimaryAtAnotherAssociation(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        if (! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its status can be toggled!');

            return Redirect::back();
        }

        $user->member->is_primary_at_another_association = ! $user->member->is_primary_at_another_association;
        $user->member->save();

        Session::flash('flash_message', 'Toggled that '.$user->name.'is a primary member at another association.');

        return Redirect::back();
    }

    public function uploadOmnomcomSound(MP3Request $request, int $id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);
        if ($user->member->customOmnomcomSound) {
            $user->member->customOmnomcomSound()->delete();
            $user->member->omnomcom_sound_id = null;
            $user->member->save();
        }

        $file = new StorageEntry;
        $file->createFromFile($request->file('sound'));

        $user->member->customOmnomcomSound()->associate($file);
        $user->member->save();
        Session::flash('flash_message', 'Sound uploaded!');

        return Redirect::back();
    }

    public function deleteOmnomcomSound(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        if ($user->member->customOmnomcomSound) {
            $user->member->customOmnomcomSound()->delete();
            $user->member->omnomcom_sound_id = null;
            $user->member->save();
        }

        Session::flash('flash_message', 'Sound deleted');

        return Redirect::back();
    }

    public function getSignedMemberForm(int $id): RedirectResponse
    {
        $user = Auth::user();
        $member = Member::withTrashed()->where('membership_form_id', '=', $id)->first();

        if ($user->id != $member->user_id && ! $user->can('registermembers')) {
            abort(403);
        }

        $form = $member->membershipForm;

        return Redirect::to($form->generatePath());
    }

    /**
     * @throws Html2PdfException
     */
    public function getNewMemberForm(int $id): string
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        if ($user->address === null) {
            Session::flash('flash_message', 'This user has no address!');

            return Redirect::back();
        }

        if ($user->bank === null) {
            Session::flash('flash_message', 'This user has no bank account!');

            return Redirect::back();
        }

        $form = new PDF('P', 'A4', 'en');
        $form->setDefaultFont('Arial');
        $form->writeHTML(view('users.admin.membershipform_pdf', ['user' => $user, 'signature' => null]));

        return $form->output();
    }

    public function destroyMemberForm(int $id): RedirectResponse
    {
        if ((! Auth::check() || ! Auth::user()->can('board'))) {
            abort(403);
        }

        $member = Member::query()->where('membership_form_id', '=', $id)->first();
        $user = $member->user;

        $member->forceDelete();

        Session::flash('flash_message', 'The digital membership form of '.$user->name.' signed on '.$member->created_at.'has been deleted!');

        return Redirect::back();
    }

    public function printMemberForm(int $id): string
    {
        /** @var User|null $user */
        $user = User::query()->find($id);
        if (! $user) {
            return 'This user could not be found!';
        }

        if (! $user->address->exists()) {
            return 'This user has no address!';
        }

        $result = FileController::requestPrint('document', route('memberform::download', ['id' => $user->id]));

        return "The printer service responded: {$result}";
    }
}
