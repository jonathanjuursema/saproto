<?php

namespace Proto\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartXpScreenController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function show(Request $request)
    {
        return view('smartxp.screen');
    }

    /** @return array */
    public function timetable()
    {
        return CalendarController::returnGoogleCalendarEvents(config('proto.google-timetable-id'), date('c', strtotime('today')), date('c', strtotime('tomorrow')));
    }

    /** @return array */
    public function protopenersTimetable()
    {
        return CalendarController::returnGoogleCalendarEvents(config('proto.protopeners-google-timetable-id'), date('c', strtotime('today')), date('c', strtotime('tomorrow')));
    }

    /**
     * @param $stop
     * @return object[]
     */
    public function bus($stop)
    {
        try {
            $departures = json_decode(stripslashes(file_get_contents("https://api.9292.nl/0.1/locations/enschede/$stop/departure-times?lang=en-GB")));

            return $departures->tabs[0]->departures;
        } catch (Exception $e) {
            return [(object) [
                'time' => '00:00',
                'service' => '',
                'mode' => (object) [
                    'name' => 'Error in API!',
                ],
                'realtimeText' => '',
                'realtimeState' => '9292',
                'destinationName' => 'who knows?',
            ]];
        }
    }

    /** @return object */
    public function smartxpTimetable()
    {
        $url = 'https://www.googleapis.com/calendar/v3/calendars/'.config('proto.smartxp-google-timetable-id').'/events?singleEvents=true&orderBy=startTime&key='.config('app-proto.google-key-private').'&timeMin='.urlencode(date('c', strtotime('last monday', strtotime('tomorrow')))).'&timeMax='.urlencode(date('c', strtotime('next monday'))).'';
        $data = json_decode(str_replace('$', '', file_get_contents($url)));
        $roster = [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
            'weekend' => [],
        ];
        $answer = true;
        foreach ($data->items as $entry) {
            $end_time = ($entry->end->date ?? $entry->end->dateTime);
            $start_time = (isset($entry->start->date) ? $entry->end->date : $entry->start->dateTime);
            $name = $entry->summary;
            $name_exp = explode(' ', $name);
            if (is_numeric($name_exp[0])) {
                $name_exp[0] = '';
            }
            $name = '';
            foreach ($name_exp as $key => $val) {
                $name .= $val.' ';
            }
            preg_match('/Type: (.*)/', $entry->description, $type);
            $current = strtotime($start_time) < time() && strtotime($end_time) > time();
            if ($current) {
                $answer = false;
            }
            $roster[strtolower(str_replace(['Saturday', 'Sunday'], ['weekend', 'weekend'], date('l', strtotime($start_time))))][] = (object) [
                'title' => $name,
                'start' => strtotime($start_time),
                'end' => strtotime($end_time),
                'type' => $type[1],
                'over' => strtotime($end_time) < time(),
                'current' => $current,
            ];
        }

        return (object) ['roster' => $roster, 'answer' => $answer];
    }

    /** @return View */
    public function canWork()
    {
        return view('smartxp.caniwork', ['timetable' => $this->smartxpTimetable()->roster,
            'answer' => $this->smartxpTimetable()->answer, ]);
    }
}
