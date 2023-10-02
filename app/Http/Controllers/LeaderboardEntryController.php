<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\User;
use Redirect;
use Session;

class LeaderboardEntryController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $leaderboard = Leaderboard::findOrFail($request->input('leaderboard_id'));

        if (! $leaderboard->canEdit(Auth::user())) {
            abort(403, "Only the board or member of the {$leaderboard->committee->name} can edit this leaderboard");
        }

        if ($leaderboard->entries()->where('user_id', $request->user_id)->first()) {
            Session::flash('flash_message', 'There is already a entry for this user');

            return Redirect::back();
        }

        $entry = LeaderboardEntry::create($request->all());
        $user = User::findOrFail($request->user_id);
        $entry->leaderboard()->associate($leaderboard);
        $entry->user()->associate($user);
        $entry->save();

        Session::flash('flash_message', 'Added new entry successfully.');

        return Redirect::back();
    }

    /**
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $entry = LeaderboardEntry::findOrFail($request->id);

        if (! $entry->leaderboard->canEdit(Auth::user())) {
            abort(403, "Only the board or member of the {$entry->leaderboard->committee->name} can edit this leaderboard");
        }

        $entry->points = $request->points;
        $entry->save();

        return response()->json(['points' => $entry->points]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $entry = LeaderboardEntry::findOrFail($id);

        if (! $entry->leaderboard->canEdit(Auth::user())) {
            abort(403, "Only the board or member of the {$entry->leaderboard->committee->name} can edit this leaderboard");
        }

        $entry->delete();
        Session::flash('flash_message', 'The entry has been deleted.');

        return Redirect::back();
    }
}
