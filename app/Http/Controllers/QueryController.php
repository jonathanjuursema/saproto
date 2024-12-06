<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTypeEnum;
use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class QueryController extends Controller
{
    public function index(): View
    {
        return view('queries.index');
    }

    public function activityOverview(Request $request): View
    {
        if ($request->missing('start') || $request->missing('end')) {
            $year_start = intval(date('n')) >= 9 ? intval(date('Y')) : intval(date('Y')) - 1;
            $start = strtotime("{$year_start}-09-01 00:00:01");
            $end = date('U');
        } else {
            $start = strtotime($request->start);
            $end = strtotime($request->end) + 86399; // Add one day to make it inclusive.
        }

        $events = Event::with(['activity', 'activity.users', 'activity.helpingCommitteeInstances'])
            ->where('start', '>=', $start)->where('end', '<=', $end)
            ->orderBy('start')->get();

        return view('queries.activity_overview', [
            'start' => $start,
            'end' => $end,
            'events' => $events,
        ]);
    }

    public function membershipTotals(Request $request)
    {
        $utQuery = User::query()->whereHas('member', function ($q) {
            $q->primary()->whereHas('UtAccount', function ($q) {
                $q->where('department', 'like', '%CREA%')->orWhere('department', 'like', '%TECH%');
            });
        });

        if ($request->has('export_subsidies')) {
            $export_subsidies = $utQuery->with('member.UtAccount')->get();
            $headers = [
                'Content-Encoding' => 'UTF-8',
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => sprintf('attachment; filename="primary_member_overview_%s.csv"', date('d_m_Y')),
            ];

            return Response::make(view('queries.export_subsidies', ['users' => $export_subsidies]), 200, $headers);
        }

        $activeUserQuery = User::query()->whereHas('member', function ($query) {
            $query->type(MembershipTypeEnum::REGULAR);
        })->whereHas('committees');

        if ($request->has('export_active')) {
            $export_active = $activeUserQuery->with('committees')->get();
            $headers = [
                'Content-Encoding' => 'UTF-8',
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => sprintf('attachment; filename="active_member_overview_%s.csv"', date('d_m_Y')),
            ];

            return Response::make(view('queries.export_active_members', ['export' => $export_active]), 200, $headers);
        }

        $count_per_type = Member::query()->selectRaw('membership_type, COUNT(*) as count')
            ->groupBy('membership_type')
            ->get()
            /** @phpstan-ignore-next-line */
            ->keyBy(fn ($item) => $item->membership_type->value)->map(fn ($item) => $item->count);

        $count_total = $count_per_type->reject(static fn ($value, $key): bool => in_array($key, [MembershipTypeEnum::PENDING->value, MembershipTypeEnum::PET->value]))->sum();

        $count_active = $activeUserQuery->count();

        $count_primary = Member::query()->primary()->count();
        $count_secondary = $count_total - $count_primary;
        $count_ut = $utQuery->count();

        return view('queries.membership_totals', [
            'total' => $count_total,
            'primary' => $count_primary,
            'secondary' => $count_secondary,
            'ut' => $count_ut,
            'active' => $count_active,
            'count_per_type' => $count_per_type,
        ]);
    }

    public function activityStatistics(Request $request): View
    {
        if ($request->missing('start') || $request->missing('end')) {
            $year_start = intval(date('n')) >= 9 ? intval(date('Y')) : intval(date('Y')) - 1;
            $start = strtotime("{$year_start}-09-01 00:00:01");
            $end = date('U');
        } else {
            $start = Carbon::parse($request->start)->getTimestamp();
            $end = Carbon::parse($request->end)->addDay()->getTimestamp();
        }

        $eventCategories = EventCategory::query()->withCount(['events' => static function ($query) use ($start, $end) {
            $query->where('start', '>=', $start)->where('end', '<=', $end);
        }])->get()->sortBy('name');

        foreach ($eventCategories as $category) {
            /** @var EventCategory $category */
            /** @phpstan-ignore-next-line */
            $category->spots = Activity::query()->whereHas('event', static function ($query) use ($category, $start, $end) {
                $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
            })->where('participants', '>', 0)
                ->sum('participants');
            /** @phpstan-ignore-next-line */
            $category->signups = ActivityParticipation::query()->whereHas('activity', static function ($query) use ($category, $start, $end) {
                $query->whereHas('event', static function ($query) use ($category, $start, $end) {
                    $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
                })->where('participants', '>', 0);
            })->count();
            /** @phpstan-ignore-next-line */
            $category->attendees = Activity::query()->where('participants', '>', 0)->whereHas('event', static function ($query) use ($category, $start, $end) {
                $query->where('category_id', $category->id)->where('start', '>=', $start)->where('end', '<=', $end);
            })->sum('attendees');
        }

        $events = Event::query()->selectRaw('YEAR(FROM_UNIXTIME(start)) AS Year, WEEK(FROM_UNIXTIME(start)) AS Week, start as Start, COUNT(*) AS Total')
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('YEAR(FROM_UNIXTIME(start)), MONTH(FROM_UNIXTIME(start))'))
            ->get();

        $totalEvents = Event::query()->where('start', '>=', $start)->where('end', '<=', $end)->count();

        $changeGMM = Carbon::parse('01-09-2010');
        foreach ($events as $event) {
            /** @phpstan-ignore-next-line */
            $event->Board = Carbon::createFromTimestamp($event->start)->diffInYears($changeGMM);
        }

        return view('queries.activity_statistics', ['start' => $start, 'end' => $end, 'events' => $events->groupBy('Board'), 'totalEvents' => $totalEvents, 'eventCategories' => $eventCategories]);
    }
}
