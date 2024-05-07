<?php

namespace App\Http\Controllers;

use App\Mail\AnonymousEmail;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\User;
use Auth;
use Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mail;
use Redirect;
use Session;

class CommitteeController extends Controller
{
    /**
     * @param  bool  $showSociety
     * @return View
     */
    public function overview($showSociety = false)
    {
        $user = Auth::user();

        if (Auth::check() && $user->can('board')) {
            return view('committee.list', ['data' => Committee::where('is_society', $showSociety)->orderby('name', 'asc')->get()]);
        }
        $publicGroups = Committee::where('public', 1)->where('is_society', $showSociety)->get();
        if ($showSociety) {
            $userGroups = Auth::check() ? $user->societies : [];
        } else {
            $userGroups = Auth::check() ? $user->committees : [];
        }
        $mergedGroups = $publicGroups->merge($userGroups)->sortBy('name');

        return view('committee.list', ['data' => $mergedGroups]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $committee = Committee::fromPublicId($id);

        if (! $committee->public && (! Auth::check() || (! Auth::user()->can('board') && ! $committee->isMember(Auth::user())))) {
            abort(404);
        }

        return view('committee.show', ['committee' => $committee, 'members' => $committee->allMembers()]);
    }

    /**
     * @return array
     */
    public function indexApi(Request $request)
    {
        $showSocieties = filter_var($request->get('show_societies', false), FILTER_VALIDATE_BOOLEAN);

        $data = [];
        foreach (Committee::where('public', 1)->where('is_society', $showSocieties)->orderBy('name', 'asc')->get() as $committee) {
            if (Auth::user() && Auth::user()->is_member) {
                $current_members = [];
                foreach ($committee->users as $user) {
                    $current_members[] = (object) [
                        'name' => $user->name,
                        'photo' => $user->photo_preview,
                        'edition' => $user->pivot->edition,
                        'role' => $user->pivot->role,
                        'since' => strval($user->pivot->created_at),
                    ];
                }
            } else {
                $current_members = null;
            }

            $data[] = (object) [
                'id' => $committee->id,
                'name' => $committee->name,
                'description' => $committee->description,
                'email' => sprintf('%s@%s', $committee->slug, config('proto.emaildomain')),
                'photo' => $committee->photo->getOriginalUrl(),
                'current_members' => $current_members,
            ];
        }

        return $data;
    }

    /** @return View */
    public function add()
    {
        return view('committee.edit', ['new' => true]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $committee = new Committee();

        $committee->fill($request->all());
        $committee->save();

        Session::flash('flash_message', 'Your new committee has been added!');

        return Redirect::route('committee::show', ['id' => $committee->getPublicId()]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $committee = Committee::findOrFail($id);

        return view('committee.edit', ['new' => false, 'id' => $id, 'committee' => $committee, 'members' => $committee->allMembers()]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        // Retrieve the committee
        $committee = Committee::find($id);

        // Check if the committee is protected
        if ($committee->slug == config('proto.rootcommittee') && $request->slug != $committee->slug) {
            Session::flash('flash_message', "This committee is protected. You cannot change it's e-mail alias.");

            return Redirect::back();
        }

        // Update the committee with the request data
        $committee->fill($request->all());

        // The is_active value is either unset or 'on' so only set it to false if selected.
        $committee->is_active = ! $request->has('is_active');

        $committee->save();

        // Redirect back with a message
        Session::flash('flash_message', 'Changes have been saved.');

        return Redirect::route('committee::edit', ['new' => false, 'id' => $id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function image($id, Request $request)
    {
        $committee = Committee::find($id);

        $image = $request->file('image');
        if ($image) {
            $photo = new Photo();
            $photo->makePhoto($image, $image->getClientOriginalName(), $image->getCTime(), false, 'committee_photos');
            $photo->save();
            $committee->photo()->associate($photo);
        } else {
            $committee->photo()->dissociate();
        }
        $committee->save();

        return Redirect::route('committee::show', ['id' => $committee->getPublicId()]);
    }

    /* Committee membership tools below. */

    /**
     * @return RedirectResponse
     */
    public function addMembership(Request $request)
    {
        User::findOrFail($request->user_id);
        Committee::findOrFail($request->committee_id);

        $membership = new CommitteeMembership();
        $membership->role = $request->role;
        $membership->edition = $request->edition;
        $membership->user_id = $request->user_id;
        $membership->committee_id = $request->committee_id;

        if (($membership->created_at = Carbon::create($request->start)) === false) {
            Session::flash('flash_message', 'Ill-formatted start date.');

            return Redirect::back();
        }
        if ($request->end != '' && ($membership->deleted_at = Carbon::create($request->end)) === false) {
            Session::flash('flash_message', 'Ill-formatted end date.');

            return Redirect::back();
        }

        if ($request->end == '') {
            $membership->deleted_at = null;
        }

        $membership->save();

        Session::flash('flash_message', 'You have added '.$membership->user->name.' to '.$membership->committee->name.'.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function editMembershipForm($id)
    {
        $membership = CommitteeMembership::withTrashed()->findOrFail($id);

        return view('committee.membership-edit', ['membership' => $membership]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function editMembership(Request $request, $id)
    {
        $membership = CommitteeMembership::withTrashed()->findOrFail($id);
        $membership->role = $request->role;
        $membership->edition = $request->edition;

        if (($membership->created_at = Carbon::create($request->start)) === false) {
            Session::flash('flash_message', 'Ill-formatted start date.');

            return Redirect::back();
        }
        if ($request->end != '' && ($membership->deleted_at = Carbon::create($request->end)) === false) {
            Session::flash('flash_message', 'Ill-formatted end date.');

            return Redirect::back();
        }

        if ($request->end == '') {
            $membership->deleted_at = null;
        }

        $membership->save();

        return Redirect::route('committee::edit', ['id' => $membership->committee->id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteMembership($id)
    {
        /** @var CommitteeMembership $membership */
        $membership = CommitteeMembership::withTrashed()->findOrFail($id);
        $committee_id = $membership->committee->id;

        Session::flash('flash_message', 'You have removed '.$membership->user->name.' from '.$membership->committee->name.'.');

        $membership->forceDelete();

        return Redirect::route('committee::edit', ['id' => $committee_id]);
    }

    public function endEdition(int $committeeID, string $edition)
    {
        $memberships = CommitteeMembership::where('edition', $edition)->whereHas('committee', function ($q) use ($committeeID) {
            $q->where('id', $committeeID);
        })->get();
        foreach ($memberships as $membership) {
            $membership->delete();
        }
        Session::flash('flash_message', 'all members from the edition ended!');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse|View
     */
    public function showAnonMailForm($id)
    {
        $committee = Committee::fromPublicId($id);

        if (! $committee->allow_anonymous_email) {
            Session::flash('flash_message', 'This committee does not accept anonymous e-mail at this time.');

            return Redirect::back();
        }

        return view('committee.anonmail', ['committee' => $committee]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function postAnonMailForm(Request $request, $id)
    {
        $committee = Committee::fromPublicId($id);

        if (! $committee->allow_anonymous_email) {
            Session::flash('flash_message', 'This committee does not accept anonymous e-mail at this time.');

            return Redirect::back();
        }
        $name = $committee->name;

        $message_content = strip_tags($request->get('message'));
        $message_hash = md5($message_content);

        Log::info('Anonymous e-mail with hash '.$message_hash.' sent to '.$name.' by user #'.Auth::user()->id);

        Mail::to((object) [
            'name' => $committee->name,
            'email' => $committee->email_address,
        ])->queue((new AnonymousEmail($committee, $message_content, $message_hash))->onQueue('low'));

        Session::flash('flash_message', sprintf('Thanks for submitting your anonymous e-mail! The e-mail will be sent to the %s straightaway. Please remember that they cannot reply to your e-mail, so you will not receive any further confirmation other than this notification.', $committee->name));

        return Redirect::route('committee::show', ['id' => $committee->getPublicId()]);
    }
}
