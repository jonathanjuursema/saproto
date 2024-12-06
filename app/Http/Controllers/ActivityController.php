<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\Committee;
use App\Models\Event;
use App\Models\HelpingCommittee;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function store(Request $request, $id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);

        $new = $event->activity === null;
        $activity = ($new ? new Activity : $event->activity);

        $newPrice = floatval(str_replace(',', '.', $request->price));
        $newNoShow = floatval(str_replace(',', '.', $request->no_show_fee));

        $newRegistrationStart = strtotime($request->registration_start);
        $newRegistrationEnd = strtotime($request->registration_end);

        if ($newRegistrationEnd < $newRegistrationStart) {
            Session::flash('flash_message', 'You cannot let the event sign-up end before it starts.');

            return Redirect::route('event::edit', ['id' => $event->id]);
        }

        if ($newNoShow > floatval($activity->no_show_fee) && $activity->users->count() > 0) {
            Session::flash('flash_message', 'You cannot make the no show fee higher since this activity already has participants.');

            return Redirect::route('event::edit', ['id' => $event->id]);
        }

        if ($newNoShow < 0) {
            Session::flash('flash_message', 'The no show fee should be a positive amount.');

            return Redirect::route('event::edit', ['id' => $event->id]);
        }

        if ($newPrice > floatval($activity->price) && $activity->users->count() > 0) {
            Session::flash('flash_message', 'You cannot make the price of this activity higher since this activity already has participants.');

            return Redirect::route('event::edit', ['id' => $event->id]);
        }

        $data = [
            'registration_start' => $newRegistrationStart,
            'registration_end' => $newRegistrationEnd,
            'deregistration_end' => strtotime($request->deregistration_end),
            'participants' => $request->participants,
            'price' => $newPrice,
            'no_show_fee' => $newNoShow,
            'hide_participants' => $request->has('hide_participants'),
            'redirect_url' => $request->redirect_url,
        ];

        if (! $activity->validate($data)) {
            return Redirect::route('event::edit', ['id' => $event->id])->withErrors($activity->errors());
        }

        $activity->fill($data);

        $activity->save();

        if ($new) {
            $activity->event()->associate($event);
            $activity->save();
        }

        ParticipationController::processBackupQueue($activity);

        Session::flash('flash_message', 'Your changes have been saved.');

        return Redirect::route('event::edit', ['id' => $event->id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);
        if (! $event->activity) {
            Session::flash('flash_message', 'There is no participation data to delete.');

            return Redirect::back();
        }

        if (count($event->activity->users) > 0) {
            Session::flash('flash_message', 'You cannot delete participation data because there are still participants to this activity.');

            return Redirect::back();
        }

        ActivityParticipation::withTrashed()->where('activity_id', $event->activity->id)->forceDelete();

        $event->activity->delete();

        Session::flash('flash_message', 'Participation data deleted.');

        return Redirect::route('event::edit', ['id' => $event->id]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function checklist($id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);
        if (! Auth::check() || ! Auth::user()->can('board') && ! $event->isEventAdmin(Auth::user())) {
            abort(403, 'You may not see this page.');
        }

        if (! $event->activity) {
            abort(404, 'This event has no activity.');
        }

        return view('event.checklist', ['event' => $event]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function addHelp(Request $request, $id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);
        if (! $event->activity) {
            Session::flash('flash_message', 'This event has no activity data.');

            return Redirect::back();
        }

        $amount = $request->input('amount');
        if ($amount < 1) {
            Session::flash('flash_message', 'The amount of helpers should be positive.');

            return Redirect::back();
        }

        $committee = Committee::query()->findOrFail($request->input('committee'));

        if (HelpingCommittee::whereActivityId($event->activity->id)->whereCommitteeId($committee->id)->count() > 0) {
            Session::flash('flash_message', 'This committee is already helping at this event.');

            return Redirect::back();
        }

        HelpingCommittee::query()->create([
            'activity_id' => $event->activity->id,
            'committee_id' => $committee->id,
            'amount' => $amount,
            'notification_sent' => false,
        ]);

        Session::flash('flash_message', 'Added '.$committee->name.' as helping committee.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function updateHelp(Request $request, $id)
    {
        /** @var HelpingCommittee $help */
        $help = HelpingCommittee::query()->findOrFail($id);
        $amount = $request->input('amount');

        $help->amount = ($amount > 0 ? $amount : $help->amount);

        $help->notification_sent = false;
        $help->save();

        Session::flash('flash_message', 'Updated '.$help->committee->name.' as helping committee.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteHelp(Request $request, $id)
    {
        /** @var HelpingCommittee $help */
        $help = HelpingCommittee::query()->findOrFail($id);

        foreach (ActivityParticipation::withTrashed()->where('committees_activities_id', $help->id)->get() as $participation) {
            $participation->delete();
        }

        $help->delete();
        Session::flash('flash_message', 'Removed '.$help->committee->name.' as helping committee.');

        return Redirect::back();
    }
}
