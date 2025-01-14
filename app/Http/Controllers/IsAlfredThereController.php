<?php

namespace App\Http\Controllers;

use App\Enums\IsAlfredThereEnum;
use App\Events\IsAlfredThereEvent;
use App\Models\HashMapItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IsAlfredThereController extends Controller
{
    public static string $HashMapItemKey = 'is_alfred_there';

    public static string $HashMapUnixKey = 'is_alfred_there_unix';

    public static string $HashMapTextKey = 'is_alfred_there_text';

    /** @return View */
    public function index()
    {
        return view('isalfredthere.minisite', $this->getStatus());
    }

    /** @return View */
    public function edit()
    {
        return view('isalfredthere.admin',
            $this->getStatus()
        );
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $text = $request->input('is_alfred_there_text');
        $status = $request->input('where_is_alfred');
        $unix = $request->input('back');
        switch ($status) {
            case IsAlfredThereEnum::UNKNOWN->value:
            case IsAlfredThereEnum::JUR->value:
                $text = '';
                $unix = '';
                break;
            case IsAlfredThereEnum::THERE->value:
            case IsAlfredThereEnum::TEXT_ONLY->value:
                $unix = '';
                break;
        }

        $text = HashMapItem::query()->updateOrCreate(['key' => self::$HashMapTextKey], ['value' => $text]);

        $status = HashMapItem::query()->updateOrCreate(['key' => self::$HashMapItemKey], ['value' => $status]);

        $unix = HashMapItem::query()->updateOrCreate(['key' => self::$HashMapUnixKey], ['value' => $unix]);

        IsAlfredThereEvent::dispatch($status->value, $text->value, $unix->value);

        return Redirect::back();
    }

    private function getStatus(): array
    {
        return [
            'text' => HashMapItem::query()->firstOrCreate(['key' => self::$HashMapTextKey], ['value' => ''])->value,
            'status' => HashMapItem::query()->firstOrCreate(['key' => self::$HashMapItemKey], ['value' => IsAlfredThereEnum::UNKNOWN])->value,
            'unix' => HashMapItem::query()->firstOrCreate(['key' => self::$HashMapUnixKey], ['value' => ''])->value,
        ];
    }
}
