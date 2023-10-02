<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\Display;

class DisplayController extends Controller
{
    /** @return View */
    public function index()
    {
        $displays = Display::all();

        return view('protube.display.index', ['displays' => $displays]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        Display::create($request->all());
        Session::flash('flash_message', 'Display added.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Display $display */
        $display = Display::findOrFail($id);
        Session::flash('flash_message', 'Display '.$display->name.' updated.');
        $display->fill($request->all());
        $display->save();

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
        /** @var Display $display */
        $display = Display::findOrFail($id);
        Session::flash('flash_message', 'Display '.$display->name.' added.');
        $display->delete();

        return Redirect::back();
    }

    /**
     * Return a public list of radio stations for Herbert.
     *
     * @return Collection|Display[]
     */
    public function api()
    {
        return Display::all();
    }
}
