<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Proto\Models\HashMapItem;
use stdClass;

class IsAlfredThereController extends Controller
{
    public static $HashMapItemKey = 'is_alfred_there';

    /** @return View */
    public function showMiniSite()
    {
        return view('isalfredthere.minisite');
    }

    /** @return false|string */
    public function getApi()
    {
        header('Access-Control-Allow-Origin: *');
        return json_encode(self::getAlfredsStatusObject());
    }

    /** @return View */
    public function getAdminInterface()
    {
        return view('isalfredthere.admin', ['status' => self::getAlfredsStatusObject()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function postAdminInterface(Request $request)
    {
        $status = self::getAlfredsStatus();

        $new_status = $request->input('where_is_alfred');
        $arrival_time = $request->input('back');

        if ($new_status == 'there' || $new_status == 'unknown') {
            $status->value = $new_status;
        } elseif ($new_status == 'away') {
            $status->value = strtotime($arrival_time);
        }
        $status->save();

        return Redirect::back();
    }

    /** @return HashMapItem|null */
    public static function getAlfredsStatus()
    {
        $status = HashMapItem::where('key', self::$HashMapItemKey)->first();
        if ($status == null) {
            $status = HashMapItem::create([
                'key' => self::$HashMapItemKey,
                'value' => 'unknown',
            ]);
        }

        return $status;
    }

    /** @return stdClass */
    public static function getAlfredsStatusObject()
    {
        $status = self::getAlfredsStatus();
        $result = new stdClass();
        if ($status->value == 'there' ?? $status->value == 'unknown') {
            $result->status = $status->value;
            return $result;
        } elseif (preg_match('/^[0-9]{10}/', $status->value) === 1) {
            $result->status = 'away';
            $result->back = date('Y-m-d H:i', $status->value);
            $result->backunix = $status->value;
            return $result;
        }
        $result->status = 'unknown';
        return $result;
    }
}
