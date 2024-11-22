<?php

namespace App\Http\Controllers;

use App\Mail\AnonymousEmail;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\StorageEntry;
use App\Models\User;
use Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CommitteeController extends Controller
{
    /**
     * @param  bool  $showSociety
     * @return View
     */
    public function index($showSociety = false)
    {
        $user = Auth::user();

        if (Auth::check() && $user->can('board')) {
            return view('committee.list', ['data' => Committee::query()->where('is_society', $showSociety)->orderby('name', 'asc')->get()]);
        }

        $publicGroups = Committee::query()->where('public', 1)->where('is_society', $showSociety)->get();
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

        $pastEvents = $committee->pastEvents(6);

        return view('committee.show', [
            'committee' => $committee,
            'members' => $committee->allMembers(),
            'pastEvents' => $pastEvents,
        ]);
    }

    /** @return View */
    public function create()
    {
        return view('committee.edit', ['new' => true]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $committee = new Committee;

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
        $committee = Committee::query()->findOrFail($id);

        return view('committee.edit', ['new' => false, 'id' => $id, 'committee' => $committee, 'members' => $committee->allMembers()]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(int $id, Request $request)
    {
        // Retrieve the committee
        $committee = Committee::query()->find($id);

        // Check if the committee is protected
        if ($committee->slug === Config::string('proto.rootcommittee') && $request->slug != $committee->slug) {
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
        $committee = Committee::query()->find($id);

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry;
            $file->createFromFile($image);
            $committee->image()->associate($file);
        } else {
            $committee->image()->dissociate();
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
        User::query()->findOrFail($request->user_id);
        Committee::query()->findOrFail($request->committee_id);

        $membership = new CommitteeMembership;
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
    public function updateMembershipForm(Request $request, $id)
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
        $memberships = CommitteeMembership::query()->where('edition', $edition)->whereHas('committee', static function ($q) use ($committeeID) {
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
    public function sendAnonMailForm(Request $request, $id)
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
