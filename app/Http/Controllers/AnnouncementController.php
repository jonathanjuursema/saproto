<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('announcements.list', ['announcements' => Announcement::orderBy('display_from', 'asc')->get()]);
    }

    /** @return View */
    public function create()
    {
        return view('announcements.edit', ['announcement' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['display_from'] = date('Y-m-d H:i:s', strtotime($data['display_from']));
        $data['display_till'] = date('Y-m-d H:i:s', strtotime($data['display_till']));
        $announcement = Announcement::create($data);
        $announcement->save();

        Session::flash('flash_message', 'Announcement created.');

        return Redirect::route('announcement::edit', ['id' => $announcement->id]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit(Request $request, $id)
    {
        return view('announcements.edit', ['announcement' => Announcement::findOrFail($id)]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Announcement $announcement */
        $announcement = Announcement::findOrFail($id);

        $data = $request->except(['_token', 'id']);
        $data['display_from'] = date('Y-m-d H:i:s', strtotime($data['display_from']));
        $data['display_till'] = date('Y-m-d H:i:s', strtotime($data['display_till']));
        $announcement->fill($data);
        $announcement->save();

        Session::flash('flash_message', 'Announcement updated.');

        return Redirect::route('announcement::edit', ['id' => $announcement->id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Announcement $announcement */
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        Session::flash('flash_message', 'Announcement deleted.');

        return Redirect::route('announcement::index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function clear()
    {
        Announcement::where('display_till', '<', date('Y-m-d'))->delete();

        Session::flash('flash_message', 'Announcements cleared.');

        return Redirect::route('announcement::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function dismiss($id)
    {
        /** @var Announcement $announcement */
        $announcement = Announcement::find($id);

        if ($announcement == null || ! $announcement->is_dismissable) {
            return Redirect::back();
        }

        $announcement->dismissForUser(Auth::user());

        return Redirect::back();
    }
}
