<?php

namespace App\Http\Controllers;

use Auth;
use Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mail;
use PDF;
use App\Http\Requests\MP3Request;
use App\Mail\MembershipEnded;
use App\Mail\MembershipEndSet;
use App\Mail\MembershipStarted;
use App\Models\HashMapItem;
use App\Models\Member;
use App\Models\StorageEntry;
use App\Models\User;
use Redirect;
use Session;
use Spatie\Permission\Models\Permission;

class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('query');
        $filter = $request->input('filter');

        switch ($filter) {
            case 'pending':
                $users = User::withTrashed()->whereHas('member', function ($q) {
                    $q->where('is_pending', '=', true)->where('deleted_at', '=', null);
                });
                break;
            case 'members':
                $users = User::withTrashed()->whereHas('member', function ($q) {
                    $q->where('is_pending', '=', false)->where('deleted_at', '=', null);
                });
                break;
            case 'users':
                $users = User::withTrashed()->doesntHave('member');
                break;
            default:
                $users = User::withTrashed();
        }

        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('calling_name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('utwente_username', 'LIKE', "%$search%")
                    ->orWhereHas('member', function ($q) use ($search) {
                        $q->where('proto_username', 'LIKE', "%$search%");
                    });
            });
        }

        $users = $users->paginate(20);

        return view('users.admin.overview', ['users' => $users, 'query' => $search, 'filter' => $filter]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function details($id)
    {
        $user = User::findOrFail($id);
        $memberships = $user->getMemberships();

        return view('users.admin.details', ['user' => $user, 'memberships' => $memberships]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->calling_name = $request->calling_name;
        if (strtotime($request->birthdate) !== false) {
            $user->birthdate = date('Y-m-d', strtotime($request->birthdate));
        } else {
            $user->birthdate = null;
        }
        $user->save();

        Session::flash('flash_message', 'User updated!');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function impersonate($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

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

    /** @return RedirectResponse */
    public function quitImpersonating()
    {
        if (Session::has('impersonator')) {
            $redirect_user = Auth::id();

            $impersonator = User::findOrFail(Session::get('impersonator'));
            Session::pull('impersonator');

            Auth::login($impersonator);

            return Redirect::route('user::admin::details', ['id' => $redirect_user]);
        }

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function addMembership($id, Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        if ($user->is_member) {
            Session::flash('flash_message', 'This user is already a member!');

            return Redirect::back();
        }

        if (! ($user->address && $user->bank)) {
            Session::flash('flash_message', "This user really needs a bank account and address. Don't bypass the system!");

            return Redirect::back();
        }

        if ($user->member == null) {
            $member = Member::create();
            $member->user()->associate($user);
        }

        $member = $user->member;
        $member->created_at = Carbon::now();
        $member->is_pending = false;
        $member->proto_username = Member::createProtoUsername($user->name);
        $member->save();

        Mail::to($user)->queue((new MembershipStarted($user))->onQueue('high'));

        EmailListController::autoSubscribeToLists('autoSubscribeMember', $user);

        HashMapItem::create([
            'key' => 'wizard',
            'subkey' => $user->id,
            'value' => 1,
        ]);

        // Disabled because ProTube is down.
        // Artisan::call('proto:playsound', ['sound' =>  config('proto.soundboardSounds')['new-member']]);

        Session::flash('flash_message', 'Congratulations! '.$user->name.' is now our newest member!');

        return Redirect::back();
    }

    /**
     * Adds membership end date to member object.
     * Member object will be removed by cron job on end date.
     *
     * @param  int  $id
     *
     * @throws Exception
     */
    public function endMembership($id): RedirectResponse
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->member()->delete();
        $user->clearMemberProfile();

        Mail::to($user)->queue((new MembershipEnded($user))->onQueue('high'));

        Session::flash('flash_message', 'Membership of '.$user->name.' has been terminated.');

        return Redirect::back();
    }

    public function EndMembershipInSeptember($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if (! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its membership to receive an end date!');

            return Redirect::back();
        }

        $user->member->until = Carbon::create('Last day of September')->endOfDay()->subDay()->timestamp;
        $user->member->save();
        Mail::to($user)->queue((new MemberShipEndSet($user))->onQueue('high'));
        Session::flash('flash_message', "End date for membership of $user->name set to the end of september!");

        return Redirect::back();
    }

    public function removeMembershipEnd($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if (! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its membership to receive an end date!');

            return Redirect::back();
        }
        $user->member->until = null;
        $user->member->save();
        Session::flash('flash_message', "End date for membership of $user->name removed!");

        return Redirect::back();
    }

    /**
     * @param  int  $id
     */
    public function setMembershipType(Request $request, $id): RedirectResponse
    {
        if (! Auth::user()->can('board')) {
            abort(403, 'Only board members can do this.');
        }

        $user = User::findOrFail($id);
        $member = $user->member;
        $type = $request->input('type');

        $member->is_honorary = $type == 'honorary';
        $member->is_lifelong = $type == 'lifelong';
        $member->is_donor = $type == 'donor';
        $member->is_pet = $type == 'pet';
        $member->save();

        Session::flash('flash_message', $user->name.' is now a '.$type.' member.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     */
    public function toggleNda($id): RedirectResponse
    {
        if (! Auth::user()->can('board')) {
            abort(403, 'Only board members can do this.');
        }

        /** @var User $user */
        $user = User::findOrFail($id);
        $user->signed_nda = ! $user->signed_nda;
        $user->save();

        Session::flash('flash_message', 'Toggled NDA status of '.$user->name.'. Please verify if it is correct.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     */
    public function unblockOmnomcom($id): RedirectResponse
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->disable_omnomcom = false;
        $user->save();

        Session::flash('flash_message', 'OmNomCom unblocked for '.$user->name.'.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     */
    public function toggleStudiedCreate($id): RedirectResponse
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->did_study_create = ! $user->did_study_create;
        $user->save();

        Session::flash('flash_message', 'Toggled CreaTe status of '.$user->name.'.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     */
    public function toggleStudiedITech($id): RedirectResponse
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->did_study_itech = ! $user->did_study_itech;
        $user->save();

        Session::flash('flash_message', 'Toggled ITech status of '.$user->name.'.');

        return Redirect::back();
    }

    public function uploadOmnomcomSound(MP3Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if ($user->member->customOmnomcomSound) {
            $user->member->customOmnomcomSound()->delete();
            $user->member->omnomcom_sound_id = null;
            $user->member->save();
        }

        $file = new StorageEntry();
        $file->createFromFile($request->file('sound'));

        $user->member->customOmnomcomSound()->associate($file);
        $user->member->save();
        Session::flash('flash_message', 'Sound uploaded!');

        return Redirect::back();
    }

    public function deleteOmnomcomSound(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
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
     * @param  int  $id
     * @return string
     */
    public function getNewMemberForm($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

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

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroyMemberForm($id)
    {
        if ((! Auth::check() || ! Auth::user()->can('board'))) {
            abort(403);
        }

        $member = Member::where('membership_form_id', '=', $id)->first();
        $user = $member->user;

        $member->forceDelete();

        Session::flash('flash_message', 'The digital membership form of '.$user->name.' signed on '.$member->created_at.'has been deleted!');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return string
     */
    public function printMemberForm($id)
    {
        $user = User::find($id);

        if (! $user) {
            return 'This user could not be found!';
        }

        if ($user->address->count() === 0) {
            return 'This user has no address!';
        }

        $result = FileController::requestPrint('document', route('memberform::download', ['id' => $user->id]));

        return "The printer service responded: $result";
    }
}
