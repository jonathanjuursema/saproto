<?php

namespace App\Http\Controllers;

use App\Mail\ActivityMovedFromBackup;
use App\Mail\ActivitySubscribedTo;
use App\Mail\ActivityUnsubscribedFrom;
use App\Mail\ActivityUnsubscribedToHelp;
use App\Mail\HelperMutation;
use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\Event;
use App\Models\HelpingCommittee;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mail;
use Redirect;
use Session;

class ParticipationController extends Controller
{
    /**
     * @param  int  $id
     * @return RedirectResponse|JsonResponse
     */
    public function create($id, Request $request)
    {
        /** @var Event $event */
        $event = Event::findOrFail($id);
        if (! $event->activity) {
            abort(403, 'You cannot subscribe for '.$event->title.'.');
        } elseif ($event->activity->getParticipation(Auth::user(), ($request->has('helping_committee_id') ? HelpingCommittee::findOrFail($request->input('helping_committee_id')) : null)) !== null) {
            abort(403, 'You are already subscribed for '.$event->title.'.');
        } elseif (! $request->has('helping_committee_id') && (! $event->activity->canSubscribeBackup())) {
            abort(403, 'You cannot subscribe for '.$event->title.' at this time.');
        } elseif ($event->activity->closed) {
            abort(403, 'This activity is closed, you cannot change participation anymore.');
        }

        $data = ['activity_id' => $event->activity->id, 'user_id' => Auth::user()->id];

        $is_web = Auth::guard('web')->user();

        if ($request->has('helping_committee_id')) {
            $helping = HelpingCommittee::findOrFail($request->helping_committee_id);
            if (! $helping->committee->isMember(Auth::user())) {
                abort(403, 'You are not a member of the '.$helping->committee.' and thus cannot help on behalf of it.');
            }
            if ($helping->users->count() >= $helping->amount) {
                abort(403, 'There are already enough people of your committee helping, thanks though!');
            }
            $data['committees_activities_id'] = $helping->id;
            Mail::queue((new HelperMutation(Auth::user(), $helping, true))->onQueue('medium'));
        } elseif ($is_web) {
            if ($event->activity->isFull() || ! $event->activity->canSubscribe()) {
                Session::flash('flash_message', 'You have been placed on the back-up list for '.$event->title.'.');
                $data['backup'] = true;
            } else {
                Session::flash('flash_message', 'You claimed a spot for '.$event->title.'.');
            }
        } else {
            if ($event->activity->isFull() || ! $event->activity->canSubscribe()) {
                $data['backup'] = true;
            }
        }

        $participation = new ActivityParticipation();
        $participation->fill($data);
        $participation->save();

        if (! $is_web) {
            if ($event->activity->isFull() || ! $event->activity->canSubscribe()) {
                $message = 'You have been placed on the back-up list for '.$event->title.'.';
            } else {
                $message = 'You claimed a spot for '.$event->title.'.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'participation_id' => $participation->id,
            ]);
        }

        if ($event->activity->redirect_url) {
            return Redirect::to($event->activity->redirect_url);
        }

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function createFor($id, Request $request)
    {
        /** @var Event $event */
        $event = Event::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        $data = ['activity_id' => $event->activity->id, 'user_id' => $user->id];

        if ($request->has('helping_committee_id')) {
            $helping = HelpingCommittee::findOrFail($request->helping_committee_id);
            if (! $helping->committee->isMember($user)) {
                abort(403, $user->name.' is not a member of the '.$helping->committee->name.' and thus cannot help on behalf of it.');
            }
            $data['committees_activities_id'] = $helping->id;
            Mail::queue((new HelperMutation($user, $helping, true))->onQueue('medium'));
        }

        if (! $event->activity) {
            abort(403, 'You cannot subscribe for '.$event->title.'.');
        } elseif ($event->activity->getParticipation($user, ($request->has('helping_committee_id') ? HelpingCommittee::findOrFail($request->input('helping_committee_id')) : null)) !== null) {
            abort(403, 'You are already subscribed for '.$event->title.'.');
        } elseif ($event->activity->closed) {
            abort(403, 'This activity is closed, you cannot change participation anymore.');
        }

        Session::flash('flash_message', 'You added '.$user->name.' for '.$event->title.'.');
        $participation = new ActivityParticipation();
        $participation->fill($data);
        $participation->save();

        $help_committee = ($helping->committee->name ?? null);

        Mail::to($participation->user)->queue((new ActivitySubscribedTo($participation, $help_committee))->onQueue('high'));

        return Redirect::back();
    }

    /**
     * @param  int  $participation_id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($participation_id, Request $request)
    {
        /** @var ActivityParticipation $participation */
        $participation = ActivityParticipation::findOrFail($participation_id);

        $notify = false;

        if ($participation->user->id != Auth::id()) {
            if (! Auth::user()->can('board')) {
                abort(403);
            }
            $notify = true;
        }

        $is_web = Auth::guard('web')->user();

        if ($participation->committees_activities_id === null) {
            if ($participation->activity->closed) {
                abort(403, 'This activity is closed, you cannot change participation anymore.');
            }

            if (! $participation->activity->canUnsubscribe() && ! $participation->backup && ! Auth::user()->can('board')) {
                abort(403, 'You cannot unsubscribe for this event at this time.');
            }

            if ($notify) {
                Mail::to($participation->user)->queue((new ActivityUnsubscribedFrom($participation))->onQueue('high'));
            }

            $message = $participation->user->name.' is not attending '.$participation->activity->event->title.' anymore.';
            if ($is_web) {
                Session::flash('flash_message', $message);
            }

            $participation->delete();

            if ($participation->backup == false && $participation->activity->users()->count() < $participation->activity->participants) {
                self::transferOneBackupUser($participation->activity);
            }
        } else {
            $message = $participation->user->name.' is not helping with '.$participation->activity->event->title.' anymore.';
            if ($is_web) {
                Session::flash('flash_message', $message);
            }

            if ($notify) {
                Mail::to($participation->user)->queue((new ActivityUnsubscribedToHelp($participation))->onQueue('high'));
            }

            Mail::queue((new HelperMutation($participation->user, $participation->help, false))->onQueue('medium'));

            $participation->delete();
        }

        if ($is_web) {
            return Redirect::back();
        } else {
            abort(200, json_encode((object) [
                'success' => true,
                'message' => $message,
            ]));
        }
    }

    /**
     * @param  int  $participation_id
     * @return JsonResponse
     */
    public function togglePresence($participation_id, Request $request)
    {
        /** @var ActivityParticipation $participation */
        $participation = ActivityParticipation::findOrFail($participation_id);

        if (! $participation->activity->event->isEventAdmin(Auth::user())) {
            abort(403, 'You are not an organizer for this event.');
        }

        $participation->is_present = ! $participation->is_present;
        $participation->save();

        return response()->json($participation->activity->getPresent());
    }

    public static function processBackupQueue(Activity $activity)
    {
        while ($activity->backupUsers()->count() > 0 && $activity->users()->count() < $activity->participants) {
            self::transferOneBackupUser($activity);
        }
    }

    public static function transferOneBackupUser(Activity $activity)
    {
        $backup_participation = ActivityParticipation::where('activity_id', $activity->id)->whereNull('committees_activities_id')->where('backup', true)->first();

        if ($backup_participation !== null) {
            $backup_participation->backup = false;
            $backup_participation->save();
            Mail::to($backup_participation->user)->queue((new ActivityMovedFromBackup($backup_participation))->onQueue('high'));
        }
    }
}
