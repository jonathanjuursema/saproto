<?php

namespace Proto\Http\Controllers;

use Auth;
use Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mail;
use PDF;
use Proto\Mail\MembershipEnded;
use Proto\Mail\MembershipEndSet;
use Proto\Mail\MembershipStarted;
use Proto\Models\HashMapItem;
use Proto\Models\Member;
use Proto\Models\User;
use Redirect;
use Session;
use Spatie\Permission\Models\Permission;

class UserAdminController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
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
     * @param int $id
     * @return View
     */
    public function details($id)
    {
        $user = User::findOrFail($id);
        $memberships = $user->getMemberships();

        return view('users.admin.details', ['user' => $user, 'memberships' => $memberships]);
    }

    /**
     * @param Request $request
     * @param int $id
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
     * @param int $id
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
     * @param int $id
     * @param Request $request
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

        $name = explode(' ', $user->name);
        if (count($name) > 1) {
            $alias_base = self::transliterateString(strtolower(
                preg_replace('/\PL/u', '', substr($name[0], 0, 1))
                .'.'.
                preg_replace('/\PL/u', '', implode('', array_slice($name, 1)))
            ));
        } else {
            $alias_base = self::transliterateString(strtolower(
                preg_replace('/\PL/u', '', $name[0])
            ));
        }

        // make sure usernames are max 20 characters long (windows limitation)
        $alias_base = substr($alias_base, 0, 17);

        $alias = $alias_base;
        $i = 0;

        while (Member::where('proto_username', $alias)->withTrashed()->count() > 0) {
            $i++;
            $alias = $alias_base.'-'.$i;
        }

        $member->proto_username = $alias;
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
     * @param int $id
     * @return RedirectResponse
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
        if(! $user->is_member) {
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
        if(! $user->is_member) {
            Session::flash('flash_message', 'The user needs to be a member for its membership to receive an end date!');
            return Redirect::back();
        }
        $user->member->until = null;
        $user->member->save();
        Session::flash('flash_message', "End date for membership of $user->name removed!");
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
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
     * @param int $id
     * @return RedirectResponse
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
     * @param int $id
     * @return RedirectResponse
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
     * @param int $id
     * @return RedirectResponse
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
     * @param int $id
     * @return RedirectResponse
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

    /**
     * @param int $id
     * @return RedirectResponse
     */
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
     * @param int $id
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
     * @param int $id
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
     * @param int $id
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

    /**
     * Replace all strange characters in a string with normal ones. Shamelessly borrowed from http://stackoverflow.com/a/6837302.
     *
     * @param string $txt
     * @return string|string[]
     */
    public static function transliterateString($txt)
    {
        $transliterationTable = ['á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja'];

        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
    }
}
