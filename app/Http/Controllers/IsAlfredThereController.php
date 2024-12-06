<?php

namespace App\Http\Controllers;

use App\Models\HashMapItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use stdClass;

class IsAlfredThereController extends Controller
{
    public static $HashMapItemKey = 'is_alfred_there';

    public static $HashMapTextKey = 'is_alfred_there_text';

    /** @return View */
    public function index()
    {
        return view('isalfredthere.minisite');
    }

    /** @return false|string */
    public function getApi()
    {
        return json_encode(self::getAlfredsStatusObject());
    }

    /** @return View */
    public function edit()
    {
        return view('isalfredthere.admin', ['status' => self::getAlfredsStatusObject()]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $text = self::getOrCreateHasMapItem(self::$HashMapTextKey);
        $status = self::getOrCreateHasMapItem(self::$HashMapItemKey);
        $new_status = $request->input('where_is_alfred');
        $arrival_time = $request->input('back');

        if ($new_status === 'there' || $new_status === 'unknown' || $new_status === 'jur') {
            $status->value = $new_status;
            $text->value = '';
        } elseif ($new_status === 'away') {
            $status->value = $arrival_time;
            $text->value = $request->input('is_alfred_there_text');
        } elseif ($new_status === 'text_only') {
            $text->value = $request->input('is_alfred_there_text');
            $status->value = 'unknown';
        }

        $status->save();
        $text->save();

        return Redirect::back();
    }

    /** @return HashMapItem */
    public static function getOrCreateHasMapItem($key)
    {
        $item = HashMapItem::query()->where('key', $key)->first();
        if (! $item) {
            return HashMapItem::query()->create([
                'key' => $key,
                'value' => '',
            ]);
        }

        return $item;
    }

    /** @return stdClass */
    public static function getAlfredsStatusObject()
    {
        $result = new stdClass;
        $result->text = self::getOrCreateHasMapItem(self::$HashMapTextKey)->value;

        $status = self::getOrCreateHasMapItem(self::$HashMapItemKey);
        if ($status->value == 'there' || $status->value == 'jur' || $status->value == 'unknown') {
            $result->status = $status->value;

            return $result;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $status->value) === 1) {
            $result->status = 'away';
            $result->back = Carbon::parse($status->value)->format('Y-m-d H:i');
            $result->backunix = Carbon::parse($status->value)->getTimestamp();

            return $result;
        }

        $result->status = 'unknown';

        return $result;
    }
}
