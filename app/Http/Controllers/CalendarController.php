<?php

namespace App\Http\Controllers;

use Exception;

class CalendarController extends Controller
{
    /**
     * @param  string  $google_calendar_id
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    public static function returnGoogleCalendarEvents($google_calendar_id, $start, $end)
    {
        try {
            $url = 'https://www.googleapis.com/calendar/v3/calendars/'.$google_calendar_id.'/events?singleEvents=true&orderBy=startTime&key='.config('app-proto.google-key-private').'&timeMin='.urlencode($start).'&timeMax='.urlencode($end).'';
            $data = json_decode(str_replace('$', '', file_get_contents($url)));
        } catch (Exception $e) {
            return [];
        }

        $results = [];

        foreach ($data->items as $entry) {
            $endTime = $entry->end->date ?? $entry->end->dateTime;
            $startTime = isset($entry->start->date) ? $entry->end->date : $entry->start->dateTime;

            if (property_exists($entry, 'summary')) {
                $name = $entry->summary;
            } else {
                $name = '(no name)';
            }
            $name_exp = explode(' ', $name);
            if (is_numeric($name_exp[0])) {
                $name_exp[0] = '';
            }
            $name = '';
            foreach ($name_exp as $key => $val) {
                $name .= $val.' ';
            }

            if (property_exists($entry, 'description')) {
                preg_match(' /Type: (.*)/', $entry->description, $type);
                preg_match('/Student set\(s\):.*(CRE MOD[0-9]{2}|ITECH M [0-9][a-zA-Z]).*/', $entry->description, $study);
            } else {
                $type = null;
                $study = null;
            }

            $year = null;
            $studyShort = null;
            if ($study) {
                $study = $study[1];
                if (substr($study, 0, 3) == 'CRE') {
                    $year = ceil(intval(str_replace('CRE MOD', '', $study)) / 4);
                    $study = 'Creative Technology';
                    $studyShort = 'CreaTe';
                } elseif (substr($study, 0, 5) == 'ITECH') {
                    $study = 'Interaction Technology';
                    $studyShort = 'ITech';
                }
            }

            $results[] = [
                'title' => trim($name),
                'place' => isset($entry->location) ? trim($entry->location) : 'Unknown',
                'start' => strtotime($startTime),
                'end' => strtotime($endTime),
                'type' => ($type ? $type[1] : null),
                'year' => $year,
                'study' => $study,
                'studyShort' => $studyShort,
                'over' => strtotime($endTime) < time(),
                'current' => strtotime($startTime) < time() && strtotime($endTime) > time(),
            ];
        }

        return $results;
    }
}
