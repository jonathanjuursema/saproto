<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Committee;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\HelpingCommittee;
use App\Models\PhotoAlbum;
use App\Models\Product;
use App\Models\StorageEntry;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $data = [[], [], []];
        $data[0] = Event::getEventBlockQuery()
            ->where('start', '>=', strtotime('now'))
            ->where('start', '<=', strtotime('+1 week'));

        $data[1] = Event::getEventBlockQuery()
            ->where('start', '>=', strtotime('now'))
            ->where('start', '>', strtotime('+1 week'))
            ->where('start', '<=', strtotime('+1 month'));

        $data[2] = Event::getEventBlockQuery()
            ->where('start', '>=', strtotime('now'))
            ->where('start', '>', strtotime('+1 month'));

        $category = EventCategory::query()->find($request->input('category'));
        foreach ($data as $index => $query) {
            if ($category) {
                $data[$index] = $query->whereHas('Category', static function ($q) use ($category) {
                    $q->where('id', $category->id)->where('deleted_at', '=', null);
                });
            }

            $data[$index] = $query->get();
        }

        $years = collect(DB::select('SELECT DISTINCT Year(FROM_UNIXTIME(start)) AS start FROM events ORDER BY Year(FROM_UNIXTIME(start))'))->pluck('start');

        $reminder = Auth::check() ? Auth::user()->getCalendarAlarm() : null;

        $calendar_url = route('ical::calendar', ['personal_key' => (Auth::check() ? Auth::user()->getPersonalKey() : null)]);

        return view('event.calendar', [
            'events' => $data,
            'years' => $years,
            'ical_url' => $calendar_url,
            'reminder' => $reminder,
            'cur_category' => $category,
        ]);
    }

    /** @return View */
    public function finindex()
    {
        $activities = Activity::query()->where('closed', false)->orderBy('registration_end', 'asc')->get();

        return view('event.notclosed', ['activities' => $activities]);
    }

    /** @return View */
    public function show($id)
    {
        $event = Event::getEventBlockQuery()->where('id', Event::getIdFromPublicId($id))
            ->with('tickets', 'tickets.product')
            ->firstOrFail();

        $methods = [];
        if (Config::boolean('omnomcom.mollie.use_fees')) {
            $methods = MollieController::getPaymentMethods();
        }

        return view('event.display', ['event' => $event, 'payment_methods' => $methods]);
    }

    /** @return View */
    public function create()
    {
        return view('event.edit', ['event' => null]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(StoreEventRequest $request)
    {
        $event = Event::query()->create([
            'title' => $request->title,
            'start' => strtotime($request->start),
            'end' => strtotime($request->end),
            'location' => $request->location,
            'maps_location' => $request->maps_location,
            'secret' => $request->publication ? false : $request->secret,
            'description' => $request->description,
            'summary' => $request->summary,
            'is_featured' => $request->has('is_featured'),
            'is_external' => $request->has('is_external'),
            'force_calendar_sync' => $request->has('force_calendar_sync'),
            'publication' => $request->publication ? strtotime($request->publication) : null,
        ]);

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));
            $event->image()->associate($file);
        }

        $committee = Committee::query()->find($request->input('committee'));
        $event->committee()->associate($committee);
        $category = EventCategory::query()->find($request->input('category'));
        $event->category()->associate($category);
        $event->save();

        Session::flash('flash_message', "Your event '".$event->title."' has been added.");

        return Redirect::route('event::show', ['id' => $event->getPublicId()]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $event = Event::query()->findOrFail($id);

        return view('event.edit', ['event' => $event]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function update(StoreEventRequest $request, $id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);
        $event->title = $request->title;
        $event->start = strtotime($request->start);
        $event->end = strtotime($request->end);
        $event->location = $request->location;
        $event->maps_location = $request->maps_location;
        $event->secret = $request->publication ? false : $request->secret;
        $event->description = $request->description;
        $event->summary = $request->summary;
        $event->involves_food = $request->has('involves_food');
        $event->is_featured = $request->has('is_featured');
        $event->is_external = $request->has('is_external');
        $event->force_calendar_sync = $request->has('force_calendar_sync');
        $event->publication = $request->publication ? strtotime($request->publication) : null;

        if ($event->end < $event->start) {
            Session::flash('flash_message', 'You cannot let the event end before it starts.');

            return Redirect::back();
        }

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));

            $event->image()->associate($file);
        }

        if ($request->has('committee')) {
            $committee = Committee::query()->find($request->input('committee'));
            $event->committee()->associate($committee);
        }

        if ($request->has('category')) {
            $category = EventCategory::query()->find($request->input('category'));
            $event->category()->associate($category);
        }

        $event->save();

        $changed_important_details = $event->start !== strtotime($request->start) || $event->end !== strtotime($request->end) || $event->location != $request->location;

        if ($changed_important_details) {
            Session::flash('flash_message', "Your event '".$event->title."' has been saved. <br><b class='text-warning'>You updated some important information. Don't forget to update your participants with this info!</b>");
        } else {
            Session::flash('flash_message', "Your event '".$event->title."' has been saved.");
        }

        return Redirect::back();
    }

    /**
     * @param  int  $year
     * @return View
     */
    public function archive(Request $request, $year)
    {
        $years = collect(DB::select('SELECT DISTINCT Year(FROM_UNIXTIME(start)) AS start FROM events ORDER BY Year(FROM_UNIXTIME(start))'))->pluck('start');
        $events = Event::getEventBlockQuery()
            ->where('start', '>', strtotime($year.'-01-01 00:00:01'))
            ->where('start', '<', strtotime($year.'-12-31 23:59:59'))
            ->get();

        $category = EventCategory::query()->find($request->category);

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [];
        }

        foreach ($events as $event) {
            if (! $category || $category == $event->category) {
                $months[intval(date('n', $event->start))][] = $event;
            }
        }

        return view('event.archive', [
            'years' => $years,
            'year' => $year,
            'months' => $months,
            'cur_category' => $category,
        ]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($id);

        if ($event->activity !== null) {
            Session::flash('flash_message', "You cannot delete event '".$event->title."' since it has a participation details.");

            return Redirect::back();
        }

        Session::flash('flash_message', "The event '".$event->title."' has been deleted.");

        $event->delete();

        return Redirect::route('event::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function forceLogin($id)
    {
        return Redirect::route('event::show', ['id' => $id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse|View
     */
    public function admin($id)
    {
        $event = Event::query()->findOrFail($id);

        if (! $event->isEventAdmin(Auth::user())) {
            Session::flash('flash_message', 'You are not an event admin for this event!');

            return Redirect::back();
        }

        return view('event.admin', ['event' => $event]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse|View
     */
    public function scan($id)
    {
        $event = Event::query()->findOrFail($id);

        if (! $event->isEventAdmin(Auth::user())) {
            Session::flash('flash_message', 'You are not an event admin for this event!');

            return Redirect::back();
        }

        return view('event.scan', ['event' => $event]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function finclose(Request $request, $id)
    {
        /** @var Activity $activity */
        $activity = Activity::query()->findOrFail($id);

        if ($activity->event && ! $activity->event->over()) {
            Session::flash('flash_message', 'You cannot close an activity before it has finished.');

            return Redirect::back();
        }

        if ($activity->closed) {
            Session::flash('flash_message', 'This activity is already closed.');

            return Redirect::back();
        }

        $activity->attendees = $request->input('attendees');

        $account = Account::query()->findOrFail($request->input('account'));

        if (count($activity->users) == 0 || $activity->price == 0) {
            $activity->closed = true;
            $activity->closed_account = $account->id;
            $activity->save();

            Session::flash('flash_message', 'This activity is now closed. It either was free or had no participants, so no orderlines or products were created.');

            return Redirect::back();
        }

        $product = Product::query()->create([
            'account_id' => $account->id,
            'name' => 'Activity: '.($activity->event ? $activity->event->title : $activity->comment),
            'price' => $activity->price,
        ]);
        $product->save();

        foreach ($activity->users as $user) {
            $product->buyForUser($user, 1, null, null, null, null, sprintf('activity_closed_by_%u', Auth::user()->id));
        }

        $activity->closed = true;
        $activity->closed_account = $account->id;
        $activity->save();

        Session::flash('flash_message', 'This activity has been closed and the relevant orderlines were added.');

        return Redirect::back();
    }

    /**
     * @param  Event  $event
     * @return RedirectResponse
     */
    public function linkAlbum(Request $request, $event)
    {
        /** @var Event $event */
        $event = Event::query()->findOrFail($event);
        /** @var PhotoAlbum $album */
        $album = PhotoAlbum::query()->findOrFail($request->album_id);

        $album->event()->associate($event);
        $album->save();

        Session::flash('flash_message', 'The album '.$album->name.' has been linked to this activity!');

        return Redirect::back();
    }

    /**
     * @param  PhotoAlbum  $album
     * @return RedirectResponse
     */
    public function unlinkAlbum($album)
    {
        /** @var PhotoAlbum $album */
        $album = PhotoAlbum::query()->findOrFail($album);
        $album->event()->dissociate();
        $album->save();

        Session::flash('flash_message', 'The album '.$album->name.' has been unlinked from an activity!');

        return Redirect::back();
    }

    /**
     * @param  int  $limit
     */
    public function apiUpcomingEvents($limit, Request $request): array
    {
        $user = Auth::user() ?? null;
        $noFutureLimit = filter_var($request->get('no_future_limit', false), FILTER_VALIDATE_BOOLEAN);

        $events = Event::query()->where('end', '>', strtotime('today'))->where('start', '<', strtotime($noFutureLimit ? '+10 years' : '+1 month'))->whereNull('publication')->orderBy('start', 'asc')->take($limit)->get();
        $data = [];

        foreach ($events as $event) {
            if ($event->secret && ($user == null || $event->activity == null || (
                ! $event->activity->isParticipating($user) &&
                ! $event->activity->isHelping($user) &&
                ! $event->activity->isOrganising($user)
            ))) {
                continue;
            }

            $participants = ($user?->is_member && $event->activity ? $event->activity->users->map(static fn ($item) => (object) [
                'name' => $item->name,
                'photo' => $item->photo_preview,
            ]) : null);
            $backupParticipants = ($user?->is_member && $event->activity ? $event->activity->backupUsers->map(static fn ($item) => (object) [
                'name' => $item->name,
                'photo' => $item->photo_preview,
            ]) : null);
            $data[] = (object) [
                'id' => $event->id,
                'title' => $event->title,
                'image' => ($event->image ? $event->image->generateImagePath(800, 300) : null),
                'description' => $event->description,
                'start' => $event->start,
                'organizing_committee' => ($event->committee ? [
                    'id' => $event->committee->id,
                    'name' => $event->committee->name,
                ] : null),
                'registration_start' => ($event->activity ? $event->activity->registration_start : null),
                'registration_end' => ($event->activity ? $event->activity->registration_end : null),
                'deregistration_end' => ($event->activity ? $event->activity->deregistration_end : null),
                'total_places' => ($event->activity ? $event->activity->participants : null),
                'available_places' => ($event->activity ? $event->activity->freeSpots() : null),
                'is_full' => ($event->activity ? $event->activity->isFull() : null),
                'end' => $event->end,
                'location' => $event->location,
                'current' => $event->current(),
                'over' => $event->over(),
                'has_signup' => $event->activity !== null,
                'price' => ($event->activity ? $event->activity->price : null),
                'no_show_fee' => ($event->activity ? $event->activity->no_show_fee : null),
                'user_signedup' => ($user && $event->activity ? $event->activity->isParticipating($user) : null),
                'user_signedup_backup' => (bool) ($user && $event->activity?->isParticipating($user) ? $event->activity->getParticipation($user)->backup : null),
                'user_signedup_id' => ($user && $event->activity?->isParticipating($user) ? $event->activity->getParticipation($user)->id : null),
                'can_signup' => ($user && $event->activity ? $event->activity->canSubscribe() : null),
                'can_signup_backup' => ($user && $event->activity ? $event->activity->canSubscribeBackup() : null),
                'can_signout' => ($user && $event->activity ? $event->activity->canUnsubscribe() : null),
                'tickets' => ($user && $event->tickets->count() > 0 ? $event->getTicketPurchasesFor($user)->pluck('api_attributes') : null),
                'participants' => $participants,
                'is_helping' => ($user && $event->activity ? $event->activity->isHelping($user) : null),
                'is_organizing' => ($user && $event->committee ? $event->committee->isMember($user) : null),
                'backupParticipants' => $backupParticipants,
            ];
        }

        return $data;
    }

    /**
     * @return RedirectResponse
     */
    public function setReminder(Request $request)
    {
        $user = Auth::user();
        $hours = floatval($request->get('hours'));

        if ($request->has('delete') || $hours <= 0) {
            $user->setCalendarAlarm(null);
            Session::flash('flash_message', 'Reminder removed.');
        } else {
            $user->setCalendarAlarm($hours);
            Session::flash('flash_message', sprintf('Reminder set to %s hours.', $hours));
        }

        return Redirect::back();
    }

    /** @return RedirectResponse */
    public function toggleRelevantOnly()
    {
        $user = Auth::user();
        $user->toggleCalendarRelevantSetting();
        if ($user->pref_calendar_relevant_only) {
            Session::flash('flash_message', 'From now on your calendar will only sync events relevant to you.');
        } else {
            Session::flash('flash_message', 'From now on your calendar will sync all events.');
        }

        return Redirect::back();
    }

    /**
     * @param  string|null  $personal_key
     * @return \Illuminate\Http\Response
     */
    public function icalCalendar($personal_key = null)
    {
        $user = ($personal_key ? User::query()->where('personal_key', $personal_key)->first() : null);

        $calendar_name = $user ? sprintf('S.A. Proto Calendar for %s', $user->calling_name) : 'S.A. Proto Calendar';

        $calendar = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//HYTTIOAOAc//S.A. Proto Calendar//EN
CALSCALE:GREGORIAN
'.
            'X-WR-CALNAME:'.$calendar_name."\r\n".
            "X-WR-CALDESC:All of Proto's events, straight from the website!"."\r\n".
            'BEGIN:VTIMEZONE'."\r\n".
            'TZID:Central European Standard Time'."\r\n".
            'BEGIN:STANDARD'."\r\n".
            'DTSTART:20161002T030000'."\r\n".
            'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYHOUR=3;BYMINUTE=0;BYMONTH=10'."\r\n".
            'TZNAME:Central European Standard Time'."\r\n".
            'TZOFFSETFROM:+0200'."\r\n".
            'TZOFFSETTO:+0100'."\r\n".
            'END:STANDARD'."\r\n".
            'BEGIN:DAYLIGHT'."\r\n".
            'DTSTART:20160301T020000'."\r\n".
            'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYHOUR=2;BYMINUTE=0;BYMONTH=3'."\r\n".
            'TZNAME:Central European Daylight Time'."\r\n".
            'TZOFFSETFROM:+0100'."\r\n".
            'TZOFFSETTO:+0200'."\r\n".
            'END:DAYLIGHT'."\r\n".
            'END:VTIMEZONE'."\r\n";

        $reminder = $user ? $user->getCalendarAlarm() : null;

        $relevant_only = $user ? $user->getCalendarRelevantSetting() : false;

        foreach (Event::query()->where('start', '>', strtotime('-6 months'))->get() as $event) {
            /** @var Event $event */
            if (! $event->mayViewEvent(Auth::user())) {
                continue;
            }

            if (! $event->force_calendar_sync && $relevant_only && ! ($event->isOrganising($user) || $event->hasBoughtTickets($user) || ($event->activity && ($event->activity->isHelping($user) || $event->activity->isParticipating($user))))) {
                continue;
            }

            if ($event->over()) {
                $info_text = 'This activity is over.';
            } elseif ($event->activity !== null && $event->activity->participants == -1) {
                $info_text = 'Sign-up required, but no participant limit.';
            } elseif ($event->activity !== null && $event->activity->participants > 0) {
                $info_text = 'Sign-up required! There are roughly '.$event->activity->freeSpots().' of '.$event->activity->participants.' places left.';
            } elseif ($event->tickets->count() > 0) {
                $info_text = 'Ticket purchase required.';
            } else {
                $info_text = 'No sign-up necessary.';
            }

            $status = null;

            if ($user) {
                if ($event->isOrganising($user)) {
                    $status = 'Organizing';
                    $info_text .= ' You are organizing this activity.';
                } elseif ($event->activity) {
                    if ($event->activity->isHelping($user)) {
                        $status = 'Helping';
                        $info_text .= ' You are helping with this activity.';
                    } elseif ($event->activity->isOnBackupList($user)) {
                        $status = 'On back-up list';
                        $info_text .= ' You are on the back-up list for this activity';
                    } elseif ($event->activity->isParticipating($user) || $event->hasBoughtTickets($user)) {
                        $status = 'Participating';
                        $info_text .= ' You are participating in this activity.';
                    }
                }
            }

            $calendar .= 'BEGIN:VEVENT
'.
                sprintf('UID:%s@proto.utwente.nl', $event->id)."\r\n".
                sprintf('DTSTAMP:%s', gmdate('Ymd\THis\Z', strtotime($event->created_at)))."\r\n".
                sprintf('DTSTART:%s', date('Ymd\THis', $event->start))."\r\n".
                sprintf('DTEND:%s', date('Ymd\THis', $event->end))."\r\n".
                sprintf('SUMMARY:%s', empty($status) ? $event->title : sprintf('[%s] %s', $status, $event->title))."\r\n".
                sprintf('DESCRIPTION:%s', $info_text.' More information: '.route('event::show', ['id' => $event->getPublicId()]))."\r\n".
                sprintf('LOCATION:%s', $event->location)."\r\n".
                sprintf(
                    'ORGANIZER;CN=%s:MAILTO:%s',
                    ($event->committee ? $event->committee->name : 'S.A. Proto'),
                    ($event->committee ? $event->committee->email_address : 'board@proto.utwente.nl')
                )."\r\n".
                sprintf('LAST_UPDATED:%s', gmdate('Ymd\THis\Z', strtotime($event->updated_at)))."\r\n".
                sprintf('SEQUENCE:%s', $event->update_sequence)."\r\n";

            if ($reminder && $status) {
                $calendar .= 'BEGIN:VALARM
'.
                    sprintf('TRIGGER:-PT%dM', ceil($reminder * 60))."\r\n".
                    'ACTION:DISPLAY'."\r\n".
                    sprintf('DESCRIPTION:%s at %s', sprintf('[%s] %s', $status, $event->title), date('l F j, H:i:s', $event->start))."\r\n".
                    'END:VALARM'."\r\n";
            }

            $calendar .= 'END:VEVENT
';
        }

        $calendar .= 'END:VCALENDAR';

        $calendar_wrapped = '';
        foreach (explode("\r\n", $calendar) as $line) {
            if (preg_match('(SUMMARY|DESCRIPTION|LOCATION)', $line) === 1) {
                $search = [';', ','];
                $replace = ['\;', '\,'];
                $line = str_replace($search, $replace, $line);
            }

            $calendar_wrapped .= wordwrap($line, 75, "\r\n ", true)."\r\n";
        }

        return Response::make($calendar_wrapped)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="protocalendar.ics"');
    }

    /**
     * @return View
     */
    public function categoryAdmin(Request $request)
    {
        $category = EventCategory::query()->find($request->id);

        return view('event.categories', ['cur_category' => $category]);
    }

    /**
     * @return RedirectResponse
     */
    public function categoryStore(Request $request)
    {
        $category = new EventCategory;
        $category->name = $request->input('name');
        $category->icon = $request->input('icon');
        $category->save();

        Session::flash('flash_message', 'The category '.$category->name.' has been created.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function categoryUpdate(Request $request, $id)
    {
        $category = EventCategory::query()->findOrFail($id);
        $category->name = $request->input('name');
        $category->icon = $request->input('icon');
        $category->save();

        Session::flash('flash_message', 'The category '.$category->name.' has been updated.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function categoryDestroy($id)
    {
        $category = EventCategory::query()->findOrFail($id);
        $events = $category->events;
        if ($events) {
            foreach ($events as $event) {
                $event->category()->dissociate();
            }
        }

        $category->delete();

        Session::flash('flash_message', 'The category '.$category->name.' has been deleted.');

        return Redirect::route('event::category::admin', ['category' => null]);
    }

    public function copyEvent(Request $request)
    {
        $event = Event::query()->findOrFail($request->id);

        $oldStart = Carbon::createFromTimestamp($event->start);

        $newDate = Carbon::createFromFormat('Y-m-d', $request->input('newDate'))
            ->setHour($oldStart->hour)
            ->setMinute($oldStart->minute)
            ->setSecond($oldStart->second)
            ->timestamp;

        $diff = $newDate - $event->start;

        $newEvent = $event->withoutRelations()->replicate()->fill([
            'title' => $event->title.' [copy]',
            'secret' => $event->publication ? false : $event->secret,
            'start' => $newDate,
            'end' => $event->end + $diff,
            'publication' => $event->publication ? $event->publication + $diff : null,
            'unique_users_count' => 0,
            'update_sequence' => 0,
        ]);
        $newEvent->save();

        if ($event->activity) {
            $newActivity = $event->activity->replicate([
                'attendees',
                'closed',
                'closed_account',
            ])->fill([
                'event_id' => $newEvent->id,
                'registration_start' => $event->activity->registration_start + $diff,
                'registration_end' => $event->activity->registration_end + $diff,
                'deregistration_end' => $event->activity->deregistration_end + $diff,
            ]);
            $newActivity->save();

            foreach ($event->activity->helpingCommitteeInstances as $helpingCommittee) {
                HelpingCommittee::query()->create([
                    'activity_id' => $newActivity->id,
                    'committee_id' => $helpingCommittee->committee_id,
                    'amount' => $helpingCommittee->amount,
                ]);
            }
        }

        Session::flash('flash_message', 'Copied the event!');

        return Redirect::to(route('event::edit', ['id' => $newEvent->id]));
    }
}
