<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\SoundboardSound;
use App\Models\StorageEntry;
use stdClass;

class SoundboardController extends Controller
{
    /** @return View */
    public function index()
    {
        $sounds = SoundboardSound::orderBy('name', 'asc')->paginate(50);

        return view('protube.soundboard.index', ['sounds' => $sounds]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $sound = new SoundboardSound();
        $sound->name = $request->name;

        $file = new StorageEntry();
        $file->createFromFile($request->file('sound'));

        $sound->file()->associate($file);
        $sound->save();

        Session::flash('flash_message', 'Sound added.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $sound = SoundboardSound::findOrFail($id);
        Session::flash('flash_message', 'Sound '.$sound->name.' deleted.');
        $sound->delete();

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function toggleHidden($id)
    {
        $sound = SoundboardSound::findOrFail($id);
        $sound->hidden = ! $sound->hidden;
        $sound->save();

        return Redirect::back();
    }

    /** @return array */
    public function apiIndex()
    {
        $sounds = SoundboardSound::all();

        $returnSounds = [];

        foreach ($sounds as $sound) {
            $returnSounds[$sound->id] = new stdClass();
            $returnSounds[$sound->id]->name = $sound->name;
            $returnSounds[$sound->id]->file = $sound->file->generatePath();
        }

        return $returnSounds;
    }
}
