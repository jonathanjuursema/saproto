<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\RadioStation;

class RadioController extends Controller
{
    /** @return View */
    public function index()
    {
        $stations = RadioStation::all();

        return view('protube.radio.index', ['stations' => $stations]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        RadioStation::create($request->all());
        Session::flash('flash_message', 'Radio station added.');

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
        $radio = RadioStation::findOrFail($id);
        Session::flash('flash_message', 'Radio station '.$radio->name.' added.');
        $radio->delete();

        return Redirect::back();
    }

    /**
     * @return Collection|RadioStation[]
     */
    public function api()
    {
        return RadioStation::all();
    }
}
