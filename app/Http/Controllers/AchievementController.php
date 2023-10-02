<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Achievement;
use App\Models\AchievementOwnership;
use App\Models\User;
use Redirect;
use Session;

class AchievementController extends Controller
{
    /** @return View */
    public function overview()
    {
        return view('achievement.list', ['achievements' => Achievement::orderBy('name', 'asc')->paginate(15)]);
    }

    /** @return View */
    public function gallery()
    {
        $common = Achievement::where('tier', 'COMMON')->where('is_archived', false)->get();
        $uncommon = Achievement::where('tier', 'UNCOMMON')->where('is_archived', false)->get();
        $rare = Achievement::where('tier', 'RARE')->where('is_archived', false)->get();
        $epic = Achievement::where('tier', 'EPIC')->where('is_archived', false)->get();
        $legendary = Achievement::where('tier', 'LEGENDARY')->where('is_archived', false)->get();
        $obtained = [];
        if (Auth::check()) {
            $achievements = Auth::user()->achieved();
            foreach ($achievements as $achievement) {
                $obtained[] = $achievement->id;
            }
        }

        return view('achievement.gallery', ['common' => $common, 'uncommon' => $uncommon, 'rare' => $rare, 'epic' => $epic, 'legendary' => $legendary, 'obtained' => $obtained]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function manage($id)
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::findOrFail($id);

        return view('achievement.manage', ['achievement' => $achievement]);
    }

    /** @return View */
    public function create()
    {
        return view('achievement.manage', ['achievement' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $achievement = new Achievement();
        $achievement->name = $request->name;
        $achievement->desc = $request->desc;
        $achievement->fa_icon = $request->fa_icon;
        $achievement->tier = $request->tier;
        $achievement->has_page = $request->has('has_page');
        $achievement->page_content = $request->page_content;
        $achievement->is_archived = $request->has('is_archived');

        if (Achievement::where('page_name', '=', $request->page_name)->first()) {
            $achievement->save();
            Session::flash('flash_message', 'saproto.nl/achieve/'.$achievement->page_name.' is not a unique url.');
        } else {
            $achievement->page_name = $request->page_name != '' ? $request->page_name : null;
            $achievement->save();
            Session::flash('flash_message', "Achievement '".$achievement->name."' has been created.");
        }

        return Redirect::route('achievement::manage', ['id' => $achievement->id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::findOrFail($id);
        $achievement->name = $request->name;
        $achievement->desc = $request->desc;
        $achievement->fa_icon = $request->fa_icon;
        $achievement->tier = $request->tier;
        $achievement->has_page = $request->has('has_page');
        $achievement->page_content = $request->page_content;
        $achievement->is_archived = $request->has('is_archived');

        if (Achievement::where('page_name', '=', $request->page_name)->where('id', '!=', $achievement->id)->first()) {
            $achievement->save();
            Session::flash('flash_message', 'saproto.nl/achieve/'.$request->page_name.' is not a unique url.');
        } else {
            $achievement->page_name = $request->page_name != '' ? $request->page_name : null;
            $achievement->save();
            Session::flash('flash_message', "Achievement '".$achievement->name."' has been updated.");
        }

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
        /** @var Achievement $achievement */
        $achievement = Achievement::findOrFail($id);
        if (count($achievement->users) > 0) {
            Session::flash('flash_message', "Achievement '".$achievement->name."' has users associated with it. You cannot remove it.");

            return Redirect::route('achievement::list');
        }
        $achievement->delete();
        Session::flash('flash_message', "Achievement '".$achievement->name."' has been removed.");

        return Redirect::route('achievement::list');
    }

    /**
     * @param  string  $page_name
     * @return View|RedirectResponse
     */
    public function achieve($page_name)
    {
        $user = Auth::user();
        if (! $user->is_member) {
            Session::flash('flash_message', 'You need to be a member to receive this achievement');

            return Redirect::back();
        }

        $achievement = Achievement::where('has_page', '=', true)->where('page_name', '=', $page_name)->firstOrFail();
        if ($achievement->is_archived) {
            Session::flash('flash_message', 'You can no longer earn this achievement');

            return Redirect::back();
        }

        if ($this->giveAchievement($achievement, $user)) {
            Session::flash('flash_message', "You have earned the achievement: '$achievement->name'");
        } else {
            Session::flash('flash_message', 'You have already earned this achievement');
        }

        return view('achievement.achieve', ['achievement' => $achievement, 'parsed_content' => Markdown::convert($achievement->page_content)]);
    }

    /**
     * @param  int  $achievement_id
     * @return RedirectResponse
     */
    public function award($achievement_id, Request $request)
    {
        $achievement = Achievement::findOrFail($achievement_id);
        $user = User::findOrFail($request->get('user-id'));

        if ($this->giveAchievement($achievement, $user)) {
            Session::flash('flash_message', "Achievement $achievement->name has been given to $user->name.");
        } else {
            Session::flash('flash_message', "$user->name already has this achievement");
        }

        return Redirect::back();
    }

    public function give(Request $request)
    {
        $achievement = Achievement::find($request->get('achievement-id'));
        $userIds = $request->input('users');
        $awarded = '';
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                if ($this->giveAchievement($achievement, $user)) {
                    $awarded = $awarded.' '.$user->name.',';
                }
            }
        }
        if ($awarded) {
            Session::flash('flash_message', "Achievement $achievement->name has been newly given to:".$awarded);
        } else {
            Session::flash('flash_message', "Achievement $achievement->name had already been achieved by all users!");
        }

        return Redirect::back();
    }

    /**
     * @param  int  $achievement_id
     * @param  int  $user_id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function take($achievement_id, $user_id)
    {
        $achievement = Achievement::findOrFail($achievement_id);
        $user = User::findOrFail($user_id);

        $achieved = AchievementOwnership::all();
        foreach ($achieved as $entry) {
            if ($entry->achievement_id == $achievement_id && $entry->user_id == $user_id) {
                $entry->delete();
                Session::flash('flash_message', "Achievement $achievement->name taken from $user->name.");
            }
        }

        return Redirect::back();
    }

    /**
     * @param  int  $achievement_id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function takeAll($achievement_id)
    {
        $this->staticTakeAll($achievement_id);
        $achievement = Achievement::findOrFail($achievement_id);
        Session::flash('flash_message', "Achievement $achievement->name taken from everyone");

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function icon($id, Request $request)
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::findOrFail($id);
        $achievement->fa_icon = $request->fa_icon;
        $achievement->save();

        Session::flash('flash_message', 'Achievement Icon set');

        return Redirect::route('achievement::manage', ['id' => $id]);
    }

    /**
     * @param  int  $id
     *
     * @throws Exception
     */
    public static function staticTakeAll($id)
    {
        $achieved = AchievementOwnership::all();
        foreach ($achieved as $entry) {
            if ($entry->achievement_id == $id) {
                $entry->delete();
            }
        }
    }

    private function giveAchievement($achievement, $user)
    {
        $achieved = $user->achieved();
        $hasAchievement = false;
        foreach ($achieved as $entry) {
            if ($entry->id == $achievement->id) {
                $hasAchievement = true;
            }
        }
        if (! $hasAchievement) {
            $relation = new AchievementOwnership([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
            $relation->save();

            return true;
        }

        return false;
    }
}
