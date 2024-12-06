<?php

namespace App\Http\Controllers;

use App\Models\NarrowcastingItem;
use App\Models\StorageEntry;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class NarrowcastingController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('narrowcasting.list', ['messages' => NarrowcastingItem::query()->orderBy('campaign_start', 'desc')->paginate(10)]);
    }

    /** @return View */
    public function show()
    {
        return view('narrowcasting.show');
    }

    /** @return View */
    public function create()
    {
        return view('narrowcasting.edit', ['item' => null]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        if (! $request->file('image') && ! $request->has('youtube_id')) {
            Session::flash('flash_message', 'Every campaign needs either an image or a video!');

            return Redirect::back();
        }

        $narrowcasting = new NarrowcastingItem;
        $narrowcasting->name = $request->name;
        $narrowcasting->campaign_start = strtotime($request->campaign_start);
        $narrowcasting->campaign_end = strtotime($request->campaign_end);
        $narrowcasting->slide_duration = $request->slide_duration;

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));

            $narrowcasting->image()->associate($file);
        }

        $youtube_id = $request->get('youtube_id');

        if ($request->has('youtube_id') && strlen($youtube_id) > 0) {
            $narrowcasting->youtube_id = $youtube_id;
            $narrowcasting->save();
            $narrowcasting->slide_duration = -1;
        }

        $narrowcasting->save();

        Session::flash('flash_message', "Your campaign '".$narrowcasting->name."' has been added.");

        return Redirect::route('narrowcasting::index');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $narrowcasting = NarrowcastingItem::query()->findOrFail($id);

        return view('narrowcasting.edit', ['item' => $narrowcasting]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function update(Request $request, $id)
    {
        $narrowcasting = NarrowcastingItem::query()->findOrFail($id);

        $narrowcasting->name = $request->name;
        $narrowcasting->campaign_start = strtotime($request->campaign_start);
        $narrowcasting->campaign_end = strtotime($request->campaign_end);
        $narrowcasting->slide_duration = $request->slide_duration;

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));

            $narrowcasting->image()->associate($file);
        }

        $youtube_id = $request->get('youtube_id');

        if ($request->has('youtube_id') && strlen($youtube_id) > 0) {

            $narrowcasting->youtube_id = $youtube_id;
            $narrowcasting->save();
            $narrowcasting->slide_duration = -1;
        } else {
            $narrowcasting->youtube_id = null;
        }

        $narrowcasting->save();

        Session::flash('flash_message', "Your campaign '".$narrowcasting->name."' has been saved.");

        return Redirect::route('narrowcasting::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $narrowcasting = NarrowcastingItem::query()->findOrFail($id);

        Session::flash('flash_message', "Your campaign '".$narrowcasting->name."' has been deleted.");
        $narrowcasting->delete();

        return Redirect::route('narrowcasting::index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function clear()
    {
        foreach (NarrowcastingItem::query()->where('campaign_end', '<', date('U'))->get() as $item) {
            $item->delete();
        }

        Session::flash('flash_message', 'All finished campaigns have been deleted.');

        return Redirect::route('narrowcasting::index');
    }

    /** @return array Return a JSON object of all currently active campaigns. */
    public function indexApi(): array
    {
        $data = [];
        foreach (
            NarrowcastingItem::query()->where('campaign_start', '<', date('U'))->where('campaign_end', '>', date('U'))->get() as $item) {
            if ($item->youtube_id) {
                $data[] = [
                    'slide_duration' => $item->slide_duration,
                    'video' => $item->youtube_id,
                ];
            } elseif ($item->image) {
                $data[] = [
                    'slide_duration' => $item->slide_duration,
                    'image' => $item->image->generateImagePath(2000, 1200),
                ];
            }
        }

        return $data;
    }
}
