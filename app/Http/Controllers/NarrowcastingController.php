<?php

namespace App\Http\Controllers;

use App\Http\Requests\NarrowCastingRequest;
use App\Models\NarrowcastingItem;
use App\Models\StorageEntry;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
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
    public function store(NarrowCastingRequest $request)
    {
        $data = $request->validated();
        if ($data['youtube_id']) {
            $data['slide_duration'] = -1;
        }

        $narrowcasting = NarrowcastingItem::query()->create($data);

        if ($data(['image']) && !$data['youtube_id']) {
            $file = StorageEntry::create();
            $file->createFromFile($request->file('image'));
            $narrowcasting->image()->associate($file);
        }

        Session::flash('flash_message', "Your campaign '" . $narrowcasting->name . "' has been added.");

        return Redirect::route('narrowcastings.index');
    }

    /**
     * @return View
     */
    public function edit(NarrowcastingItem $narrowcasting)
    {
        return view('narrowcasting.edit', ['item' => $narrowcasting]);
    }

    public function update(NarrowCastingRequest $request, NarrowcastingItem $narrowcasting)
    {
        $data = $request->validated();
        if ($data['youtube_id']) {
            $data['slide_duration'] = -1;
        }

        $narrowcasting->update($data);

        if ($data(['image']) && !$data['youtube_id']) {
            $narrowcasting->image?->delete();
            $file = StorageEntry::create();
            $file->createFromFile($request->file('image'));
            $narrowcasting->image()->associate($file);
        }

        Session::flash('flash_message', "Your campaign '" . $narrowcasting->name . "' has been saved.");

        return Redirect::route('narrowcastings.index');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(NarrowcastingItem $narrowcasting)
    {
        Session::flash('flash_message', "Your campaign '" . $narrowcasting->name . "' has been deleted.");
        $narrowcasting->delete();

        return Redirect::route('narrowcastings.index');
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

        return Redirect::route('narrowcastings.index');
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
