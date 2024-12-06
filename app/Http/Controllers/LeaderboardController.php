<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\Leaderboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RedirectResponse|View
     */
    public function index()
    {
        $leaderboards = Leaderboard::all()->reverse();
        if (count($leaderboards) > 0) {
            return view('leaderboards.list', ['leaderboards' => $leaderboards]);
        }

        Session::flash('flash_message', 'There are currently no leaderboards, but please check back real soon!');

        return Redirect::back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function adminIndex()
    {
        if (Auth::user()->can('board')) {
            $leaderboards = Leaderboard::all();
        } else {
            $leaderboards = Leaderboard::query()->whereRelation('committee.users', 'users.id', Auth::user()->id)->get();
        }

        return view('leaderboards.adminlist', ['leaderboards' => $leaderboards]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('leaderboards.edit', ['leaderboard' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $leaderboard = new Leaderboard;
        $committee = Committee::query()->findOrFail($request->input('committee'));
        $leaderboard->committee()->associate($committee);
        $leaderboard->name = $request->input('name');
        $leaderboard->featured = $request->has('featured');
        $leaderboard->description = $request->input('description');
        $leaderboard->icon = $request->input('icon');
        $leaderboard->points_name = $request->input('points_name');
        $leaderboard->save();

        Session::flash('flash_message', "Your leaderboard '".$leaderboard->name."' has been added.");

        return Redirect::route('leaderboards::edit', ['id' => $leaderboard->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $leaderboard = Leaderboard::query()->findOrFail($id);

        if (! $leaderboard->canEdit(Auth::user())) {
            abort(403, "Only the board or member of the {$leaderboard->committee->name} can edit this leaderboard");
        }

        $entries = $leaderboard->entries->sortByDesc('points');

        return view('leaderboards.edit', ['leaderboard' => $leaderboard, 'entries' => $entries]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $leaderboard = Leaderboard::query()->findOrFail($id);

        if (! $leaderboard->canEdit(Auth::user())) {
            abort(403, "Only the board or member of the {$leaderboard->committee->name} can edit this leaderboard");
        }

        $leaderboard->name = $request->input('name');
        $leaderboard->description = $request->input('description');
        $leaderboard->points_name = $request->input('points_name');
        if ($request->input('icon') != null) {
            $leaderboard->icon = $request->input('icon');
        }

        //Only editable for board permission
        if (Auth::user()->can('board')) {
            if ($request->has('featured') && Leaderboard::query()->where('featured', true)->first() != null) {
                Leaderboard::query()->where('featured', true)->update(['featured' => false]);
            }

            $leaderboard->featured = $request->has('featured');
            $committee = Committee::query()->findOrFail($request->input('committee'));
            if ($committee != $leaderboard->committee) {
                $leaderboard->committee()->associate($committee);
            }
        }

        $leaderboard->save();

        Session::flash('flash_message', 'Leaderboard has been updated.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $leaderboard = Leaderboard::query()->findOrFail($id);

        Session::flash('flash_message', "The leaderboard '".$leaderboard->name."' has been deleted.");
        $leaderboard->delete();

        return Redirect::route('leaderboards::admin');
    }
}
