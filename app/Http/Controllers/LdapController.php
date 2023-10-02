<?php

namespace App\Http\Controllers;

class LdapController extends Controller
{
    /**
     * @param  string  $query
     * @param  bool  $only_active
     * @return array
     */
    public static function searchUtwente($query, $only_active = false)
    {
        $response = file_get_contents(sprintf('%s?key=%s&filter=(%s)', config('ldap.proxy.utwente.url'), config('ldap.proxy.utwente.key'), urlencode($query)));
        $result = json_decode($response)->result;

        if ($only_active) {
            $result = array_filter($result, function ($row) {
                if ($row->active) {
                    return true;
                }

                return false;
            });
        }

        return $result;
    }

    public static function searchStudents()
    {
        $ldap_students = LdapController::searchUtwente('|(department=*B-CREA*)(department=*M-ITECH*)');

        $names = [];
        $emails = [];
        $usernames = [];

        foreach ($ldap_students as $student) {
            $names[] = strtolower($student->givenname.' '.$student->sn);
            $emails[] = strtolower($student->userprincipalname);
            $usernames[] = $student->uid;
        }

        return ['names' => $names, 'emails' => $emails, 'usernames' => $usernames];
    }
}
