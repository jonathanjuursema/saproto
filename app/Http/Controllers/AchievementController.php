<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementOwnership;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        return view('achievement.list', ['achievements' => Achievement::query()->orderBy('name', 'asc')->paginate(15)]);
    }

    public function gallery(): View
    {
        $common = Achievement::query()->where('tier', 'COMMON')->where('is_archived', false)->get();
        $uncommon = Achievement::query()->where('tier', 'UNCOMMON')->where('is_archived', false)->get();
        $rare = Achievement::query()->where('tier', 'RARE')->where('is_archived', false)->get();
        $epic = Achievement::query()->where('tier', 'EPIC')->where('is_archived', false)->get();
        $legendary = Achievement::query()->where('tier', 'LEGENDARY')->where('is_archived', false)->get();
        $obtained = Auth::user()?->achievements->where('is_archived', false);

        return view('achievement.gallery', ['common' => $common, 'uncommon' => $uncommon, 'rare' => $rare, 'epic' => $epic, 'legendary' => $legendary, 'obtained' => $obtained]);
    }

    public function edit(int $id): View
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::query()->findOrFail($id);

        return view('achievement.manage', ['achievement' => $achievement]);
    }

    public function create(): View
    {
        return view('achievement.manage', ['achievement' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $achievement = new Achievement;
        $achievement->name = $request->name;
        $achievement->desc = $request->desc;
        $achievement->fa_icon = $request->fa_icon;
        $achievement->tier = $request->tier;
        $achievement->has_page = $request->has('has_page');
        $achievement->page_content = $request->page_content;
        $achievement->is_archived = $request->has('is_archived');

        if (Achievement::query()->where('page_name', '=', $request->page_name)->first()) {
            $achievement->save();
            Session::flash('flash_message', 'saproto.nl/achieve/'.$achievement->page_name.' is not a unique url.');
        } else {
            $achievement->page_name = $request->page_name != '' ? $request->page_name : null;
            $achievement->save();
            Session::flash('flash_message', "Achievement '".$achievement->name."' has been created.");
        }

        return Redirect::route('achievement::edit', ['id' => $achievement->id]);
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::query()->findOrFail($id);
        $achievement->name = $request->name;
        $achievement->desc = $request->desc;
        $achievement->fa_icon = $request->fa_icon;
        $achievement->tier = $request->tier;
        $achievement->has_page = $request->has('has_page');
        $achievement->page_content = $request->page_content;
        $achievement->is_archived = $request->has('is_archived');

        if (Achievement::query()->where('page_name', '=', $request->page_name)->where('id', '!=', $achievement->id)->first()) {
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
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::query()->findOrFail($id);
        if (count($achievement->users) > 0) {
            Session::flash('flash_message', "Achievement '".$achievement->name."' has users associated with it. You cannot remove it.");

            return Redirect::route('achievement::index');
        }

        $achievement->delete();
        Session::flash('flash_message', "Achievement '".$achievement->name."' has been removed.");

        return Redirect::route('achievement::index');
    }

    public function achieve(string $page_name): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->is_member) {
            Session::flash('flash_message', 'You need to be a member to receive this achievement');

            return Redirect::back();
        }

        $achievement = Achievement::query()->where('has_page', '=', true)->where('page_name', '=', $page_name)->firstOrFail();
        if ($achievement->is_archived) {
            Session::flash('flash_message', 'You can no longer earn this achievement');

            return Redirect::back();
        }

        if ($this->giveAchievement($achievement, $user, null, null)) {
            Session::flash('flash_message', "You have earned the achievement: '$achievement->name'");
        } else {
            Session::flash('flash_message', 'You have already earned this achievement');
        }

        return view('achievement.achieve', ['achievement' => $achievement, 'parsed_content' => Markdown::convert($achievement->page_content)]);
    }

    public function award(int $achievement_id, Request $request): RedirectResponse
    {
        $achievement = Achievement::query()->findOrFail($achievement_id);
        $user = User::query()->findOrFail($request->get('user-id'));

        if ($this->giveAchievement($achievement, $user, $request->input('description'), $request->input('achieved_on'))) {
            Session::flash('flash_message', "Achievement $achievement->name has been given to $user->name.");
        } else {
            Session::flash('flash_message', "$user->name already has this achievement");
        }

        return Redirect::back();
    }

    public function give(Request $request): RedirectResponse
    {
        $achievement = Achievement::query()->findOrFail($request->input('achievement-id'));
        $userIds = $request->input('users');
        $awarded = '';
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            if (! $this->giveAchievement($achievement, $user, $request->input('description'), $request->input('achieved_on'))) {
                continue;
            }

            $awarded = $awarded.' '.$user->name.',';
        }

        if ($awarded !== '' && $awarded !== '0') {
            Session::flash('flash_message', "Achievement $achievement->name has been newly given to:".$awarded);
        } else {
            Session::flash('flash_message', "Achievement $achievement->name had already been achieved by all users!");
        }

        return Redirect::back();
    }

    /**
     * @throws Exception
     */
    public function take(int $achievement_id, int $user_id): RedirectResponse
    {
        $achievement = Achievement::query()->findOrFail($achievement_id);
        $user = User::query()->findOrFail($user_id);

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
     * @throws Exception
     */
    public function takeAll(int $achievement_id): RedirectResponse
    {
        static::staticTakeAll($achievement_id);
        $achievement = Achievement::query()->findOrFail($achievement_id);
        Session::flash('flash_message', "Achievement $achievement->name taken from everyone");

        return Redirect::back();
    }

    public function icon(int $id, Request $request): RedirectResponse
    {
        /** @var Achievement $achievement */
        $achievement = Achievement::query()->findOrFail($id);
        $achievement->fa_icon = $request->fa_icon;
        $achievement->save();

        Session::flash('flash_message', 'Achievement Icon set');

        return Redirect::route('achievement::edit', ['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public static function staticTakeAll(int $id): void
    {
        $achieved = AchievementOwnership::query()->where('achievement_id', $id)->get();
        foreach ($achieved as $entry) {
            $entry->delete();
        }
    }

    private function giveAchievement($achievement, $user, $description, $achievedOn): bool
    {
        $achieved = $user->achievements()->where('achievement_id', $achievement->id)->first();
        if (! $achieved) {
            $relation = new AchievementOwnership([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'description' => $description,
            ]);
            if ($achievedOn) {
                $relation->created_at = Carbon::parse($achievedOn);
            }

            $relation->save();

            return true;
        }

        if ($description) {
            $achieved->pivot->description = $description;
            $achievement->save();

            return false;
        }

        return false;
    }
}
