<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('announcements.list', ['announcements' => Announcement::query()->orderBy('display_from', 'asc')->get()]);
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
        $announcement = Announcement::query()->create([
            'description' => $request->input('description'),
            'content' => $request->input('content'),
            'display_from' => date('Y-m-d H:i:s', strtotime($request->input('display_from'))),
            'display_till' => date('Y-m-d H:i:s', strtotime($request->input('display_till'))),
            'show_guests' => $request->has('show_guests'),
            'show_users' => $request->has('show_users'),
            'show_members' => $request->has('show_members'),
            'show_only_homepage' => $request->has('show_only_homepage'),
            'show_only_new' => $request->has('show_only_new'),
            'show_only_firstyear' => $request->has('show_only_firstyear'),
            'show_only_active' => $request->has('show_only_active'),
            'show_as_popup' => $request->has('show_as_popup'),
            'show_style' => $request->input('show_style'),
            'is_dismissable' => $request->has('is_dismissable'),
        ]);

        Session::flash('flash_message', 'Announcement created.');

        return Redirect::route('announcement::edit', ['id' => $announcement->id]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit(Request $request, $id)
    {
        return view('announcements.edit', ['announcement' => Announcement::query()->findOrFail($id)]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Announcement $announcement */
        $announcement = Announcement::query()->findOrFail($id);

        $announcement->update([
            'description' => $request->input('description'),
            'content' => $request->input('content'),
            'display_from' => date('Y-m-d H:i:s', strtotime($request->input('display_from'))),
            'display_till' => date('Y-m-d H:i:s', strtotime($request->input('display_till'))),
            'show_guests' => $request->has('show_guests'),
            'show_users' => $request->has('show_users'),
            'show_members' => $request->has('show_members'),
            'show_only_homepage' => $request->has('show_only_homepage'),
            'show_only_new' => $request->has('show_only_new'),
            'show_only_firstyear' => $request->has('show_only_firstyear'),
            'show_only_active' => $request->has('show_only_active'),
            'show_as_popup' => $request->has('show_as_popup'),
            'show_style' => $request->input('show_style'),
            'is_dismissable' => $request->has('is_dismissable'),
        ]);

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
        $announcement = Announcement::query()->findOrFail($id);
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
        Announcement::query()->where('display_till', '<', date('Y-m-d'))->delete();

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
        $announcement = Announcement::query()->find($id);

        if ($announcement == null || ! $announcement->is_dismissable) {
            return Redirect::back();
        }

        $announcement->dismissForUser(Auth::user());

        return Redirect::back();
    }
}
