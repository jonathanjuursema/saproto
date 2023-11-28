<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Member;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class QueryController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('queries.index');
    }

    /**
     * @return View
     */
    public function activityOverview(Request $request)
    {
        if ($request->missing('start') || $request->missing('end')) {
            if (intval(date('n')) >= 9) {
                $year_start = intval(date('Y'));
            } else {
                $year_start = intval(date('Y')) - 1;
            }
            $start = strtotime("$year_start-09-01 00:00:01");
            $end = date('U');
        } else {
            $start = strtotime($request->start);
            $end = strtotime($request->end) + 86399; // Add one day to make it inclusive.
        }

        $events = Event::with(['activity', 'activity.users', 'activity.helpingCommitteeInstances'])
            ->where('start', '>=', $start)->where('end', '<=', $end)
            ->orderBy('start', 'asc')->get();

        return view('queries.activity_overview', [
            'start' => $start,
            'end' => $end,
            'events' => $events,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response|View
     */
    public function membershipTotals(Request $request)
    {
        // Get a list of all CreaTe students.
        $students = LdapController::searchStudents();
        $names = $students['names'];
        $emails = $students['emails'];
        $usernames = $students['usernames'];

        $count_total = 0;
        $count_primary = 0;
        $count_secondary = 0;
        $count_ut = 0;
        $count_active = 0;
        $count_lifelong = 0;
        $count_honorary = 0;
        $count_donor = 0;
        $count_pending = 0;

        $export_subsidies = [];
        $export_active = [];

        // Loop over all members and determine if they are studying CreaTe.
        foreach (Member::all() as $member) {
            $is_primary_student = in_array(strtolower($member->user->email), $emails) ||
                in_array($member->user->utwente_username, $usernames) ||
                in_array(strtolower($member->user->name), $names);
            $has_ut_mail = substr($member->user->email, -10) == 'utwente.nl';
            $is_ut = $is_primary_student || $has_ut_mail || $member->user->utwente_username !== null;

            if ($member->is_pending) {
                $count_pending++;
            } else {
                if (! $member->is_pet) {
                    $count_total++;
                }

                if ($member->user->isActiveMember()) {
                    $count_active++;

                    if ($request->has('export_active')) {
                        $export_active[] = (object) [
                            'name' => $member->user->name,
                            'committees' => $member->user->committees->pluck('name'),
                        ];
                    }
                }
                if ($member->is_lifelong) {
                    $count_lifelong++;
                }
                if ($member->is_honorary) {
                    $count_honorary++;
                }
                if ($member->is_donor) {
                    $count_donor++;
                }

                if ($is_primary_student) {
                    $count_primary++;
                } else {
                    $count_secondary++;
                }

                if ($is_ut) {
                    $count_ut++;
                }

                if ($request->has('export_subsidies')) {
                    if ($is_ut) {
                        $export_subsidies[] = (object) [
                            'primary' => $is_primary_student ? 'true' : 'false',
                            'name' => $member->user->name,
                            'email' => $has_ut_mail ? $member->user->email : null,
                            'ut_number' => $member->user->utwente_username ? $member->user->utwente_username : null,
                        ];
                    }
                }
            }
        }

        if ($request->has('export_subsidies')) {
            $headers = [
                'Content-Encoding' => 'UTF-8',
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => sprintf('attachment; filename="primary_member_overview_%s.csv"', date('d_m_Y')),
            ];

            return Response::make(view('queries.export_subsidies', ['export' => $export_subsidies]), 200, $headers);
        } elseif ($request->has('export_active')) {
            $headers = [
                'Content-Encoding' => 'UTF-8',
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => sprintf('attachment; filename="active_member_overview_%s.csv"', date('d_m_Y')),
            ];

            return Response::make(view('queries.export_active_members', ['export' => $export_active]), 200, $headers);
        } else {
            return view('queries.membership_totals', [
                'total' => $count_total,
                'primary' => $count_primary,
                'secondary' => $count_secondary,
                'ut' => $count_ut,
                'active' => $count_active,
                'lifelong' => $count_lifelong,
                'honorary' => $count_honorary,
                'donor' => $count_donor,
                'pending' => $count_pending,
            ]);
        }
    }

    public function activityStatistics(Request $request)
    {

        if ($request->missing('start') || $request->missing('end')) {
            if (intval(date('n')) >= 9) {
                $year_start = intval(date('Y'));
            } else {
                $year_start = intval(date('Y')) - 1;
            }
            $start = strtotime("$year_start-09-01 00:00:01");
            $end = date('U');
        } else {
            $start = strtotime($request->start);
            $end = strtotime($request->end) + 86399; // Add one day to make it inclusive.
        }

        $eventCategories = EventCategory::withCount(['events' => function ($query) use ($start, $end) {
            $query->where('start', '>=', $start)->where('end', '<=', $end);
        }])->get()->sortBy('name');

        foreach ($eventCategories as $category) {
            $category->spots = Activity::whereHas('event', function ($query) use ($category, $start, $end) {
                $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
            })->where('participants', '>', 0)
                ->sum('participants');

            $category->signups = ActivityParticipation::whereHas('activity', function ($query) use ($category, $start, $end) {
                $query->whereHas('event', function ($query) use ($category, $start, $end) {
                    $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
                })->where('participants', '>', 0);
            })->count();

            $category->attendees = Activity::where('participants', '>', 0)->whereHas('event', function ($query) use ($category, $start, $end) {
                $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
            })->sum('attendees');
        }

        $events = Event::selectRaw('YEAR(FROM_UNIXTIME(start)) AS Year, WEEK(FROM_UNIXTIME(start)) AS Week, start as Start, COUNT(*) AS Total')
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('YEAR(FROM_UNIXTIME(start)), MONTH(FROM_UNIXTIME(start))'))
            ->get();

        $totalEvents = Event::where('start', '>=', $start)->where('end', '<=', $end)->count();

        $changeGMM = Carbon::create('01-09-2010');
        foreach ($events as $event) {
            $event->Board = Carbon::createFromTimestamp($event->Start)->diffInYears($changeGMM);
        }

        return view('queries.activity_statistics', ['start' => $start, 'end' => $end, 'events' => $events->groupBy('Board'), 'totalEvents' => $totalEvents, 'eventCategories' => $eventCategories]);
    }
}
