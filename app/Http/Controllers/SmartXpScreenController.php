<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class SmartXpScreenController extends Controller
{
    /**
     * @return View
     */
    public function show()
    {
        return view('smartxp.screen');
    }

    /**
     * @return View
     */
    public function showProtopolis()
    {
        return view('smartxp.protopolis_screen');
    }

    public function timetable(): array
    {
        return CalendarController::returnGoogleCalendarEvents(
            Config::string('proto.google-calendar.timetable-id'),
            date('c', strtotime('today')),
            date('c', strtotime('tomorrow'))
        );
    }

    public function protopenersTimetable(): array
    {
        return CalendarController::returnGoogleCalendarEvents(
            Config::string('proto.google-calendar.protopeners-id'),
            date('c', strtotime('today')),
            date('c', strtotime('tomorrow'))
        );
    }

    /**
     * @return Response|JsonResponse
     */
    public function bus(Request $request)
    {
        try {
            return response(file_get_contents("http://v0.ovapi.nl/tpc/$request->tpc_id,$request->tpc_id_other"), 200)->header('Content-Type', 'application/json');
        } catch (Exception) {
            return response()->json([
                'message' => 'OV_API not available',
            ], 503);
        }
    }

    /** @return object */
    public function smartxpTimetable()
    {
        $roster = [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
            'weekend' => [],
        ];
        $occupied = false;
        $url = 'https://www.googleapis.com/calendar/v3/calendars/'.Config::string('proto.google-calendar.smartxp-id').'/events?singleEvents=true&orderBy=startTime&key='.Config::string('app-proto.google-key-private').'&timeMin='.urlencode(date('c', strtotime('last monday', strtotime('tomorrow')))).'&timeMax='.urlencode(date('c', strtotime('next monday')));

        try {
            $data = json_decode(str_replace('$', '', file_get_contents($url)));
        } catch (Exception) {
            return (object) ['roster' => $roster, 'occupied' => $occupied];
        }

        foreach ($data->items as $entry) {
            $end_time = ($entry->end->date ?? $entry->end->dateTime);
            $start_time = (isset($entry->start->date) ? $entry->end->date : $entry->start->dateTime);

            $name = $entry->summary ?? '';
            preg_match('/Course\/Description:\s*([^\.]+)\./', $entry->summary ?? '', $name);
            $name = $name[1] ?? 'unknown';

            preg_match('/Hall: ZI A138, (.*)/', $entry->summary ?? '', $type);
            foreach (Config::array('proto.timetable-translations') as $key => $value) {
                $type = str_replace($key, $value, $type);
            }

            $current = strtotime($start_time) < time() && strtotime($end_time) > time();
            if ($current) {
                $occupied = true;
            }

            $day = strtolower(str_replace(['Saturday', 'Sunday'], ['weekend', 'weekend'], date('l', strtotime($start_time))));
            $roster[$day][] = (object) [
                'title' => $name,
                'start' => strtotime($start_time),
                'end' => strtotime($end_time),
                'type' => $type[1] ?? 'Other',
                'over' => strtotime($end_time) < time(),
                'current' => $current,
            ];
        }

        return (object) ['roster' => $roster, 'occupied' => $occupied];
    }

    /** @return View */
    public function canWork()
    {
        return view('smartxp.caniwork', ['timetable' => $this->smartxpTimetable()->roster,
            'occupied' => $this->smartxpTimetable()->occupied, ]);
    }
}
