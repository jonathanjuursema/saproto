<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;
use Proto\Http\Requests;

use Proto\Models\StorageEntry;
use Proto\Models\User;
use Proto\Models\Achievement;
use Proto\Models\AchievementOwnership;

use Session;
use Auth;
use Redirect;

class AchievementController extends Controller
{

    public function overview()
    {
        return view('achievement.list', ['achievements' => Achievement::orderBy('name', 'asc')->paginate(15)]);
    }

    public function create()
    {
        return view('achievement.manage', ['new' => true]);
    }

    public function store(Request $request)
    {
        $achievement = new Achievement($request->all());
        $achievement->save();
        Session::flash('flash_message', "Achievement '" . $achievement->name . "' has been created.");
        return Redirect::route("achievement::manage", ['id' => $achievement->id]);
    }

    public function update($id, Request $request)
    {
        $achievement = Achievement::find($id);
        if (!$achievement) abort(404);
        $achievement->fill($request->all());
        $achievement->save();
        Session::flash('flash_message', "Achievement '" . $achievement->name . "' has been updated.");
        return Redirect::back();
    }

    public function manage($id)
    {
        $achievement = Achievement::find($id);
        if (!$achievement) abort(404);
        return view('achievement.manage', ['new' => false, 'achievement' => $achievement]);
    }

    public function destroy($id)
    {
        $achievement = Achievement::find($id);
        if (!$achievement) abort(404);
        if (count($achievement->users) > 0) {
            Session::flash('flash_message', "Achievement '" . $achievement->name . "' has users associated with it. You cannot remove it.");
            return Redirect::route("achievement::list");
        }
        $achievement->delete();
        Session::flash('flash_message', "Achievement '" . $achievement->name . "' has been removed.");
        return Redirect::route("achievement::list");
    }

    public function give($achievement_id, Request $request)
    {
        $achievement = Achievement::find($achievement_id);
        $user = User::find($request->get('user-id'));
        if (!$user || !$achievement) abort(500, 'User or achievement not found.');
        $achieved = $user->achieved();
        $hasAchievement = false;
        foreach ($achieved as $entry) {
            if ($entry->id == $achievement->id) $hasAchievement = true;
        }
        if (!$hasAchievement) {
            $new = array(
                'user_id' => $user->id,
                'achievement_id' => $achievement->id
            );
            $relation = new AchievementOwnership($new);
            $relation->save();
            Session::flash('flash_message', "Achievement $achievement->name has been given to $user->name.");
        } else {
            Session::flash('flash_message', "This user already has this achievement");
        }
        return Redirect::back();
    }

    public function take($achievement_id, $user_id)
    {
        $achievement = Achievement::find($achievement_id);
        $user = User::find($user_id);
        if (!$user || !$achievement) abort(404);
        $achieved = AchievementOwnership::all();
        foreach ($achieved as $entry) {
            if ($entry->achievement_id == $achievement_id && $entry->user_id == $user_id) {
                $entry->delete();
                Session::flash('flash_message', "Achievement $achievement->name taken from $user->name.");
            }
        }
        return Redirect::back();
    }

    public function takeAll($achievement_id)
    {
        $this->staticTakeAll($achievement_id);
        $achievement = Achievement::find($achievement_id);
        Session::flash('flash_message', "Achievement $achievement->name taken from everyone");
        return Redirect::back();
    }

    public function icon($id, Request $request)
    {
        $achievement = Achievement::find($id);
        if (!$achievement) abort(404);
        $achievement->fa_icon = $request->fa_icon;
        $achievement->save();

        Session::flash('flash_message', "Achievement Icon set");
        return Redirect::route('achievement::manage', ['id' => $id]);
    }

    static function staticTakeAll($id)
    {
        $achievement = Achievement::find($id);
        if (!$achievement) abort(404);
        $achieved = AchievementOwnership::all();
        foreach ($achieved as $entry) {
            if ($entry->achievement_id == $id) {
                $entry->delete();
            }
        }
    }

    public function gallery()
    {
        $common = Achievement::where('tier', 'COMMON')->get();
        $uncommon = Achievement::where('tier', 'UNCOMMON')->get();
        $rare = Achievement::where('tier', 'RARE')->get();
        $epic = Achievement::where('tier', 'EPIC')->get();
        $legendary = Achievement::where('tier', 'LEGENDARY')->get();
        $obtained = array();
        if (Auth::check()) {
            $achievements = Auth::user()->achieved();
            foreach ($achievements as $achievement) {
                $obtained[] = $achievement->id;
            }
        }
        return view('achievement.gallery', ['common' => $common, 'uncommon' => $uncommon, 'rare' => $rare, 'epic' => $epic, 'legendary' => $legendary, 'obtained' => $obtained]);
    }
}