<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\CarbonInterval;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\PlayedVideo;
use App\Models\SoundboardSound;
use App\Models\User;
use Session;

class ProtubeController extends Controller
{
    /** @return View */
    public function admin()
    {
        if (Auth::user()->can('protube') || Auth::user()->isTempadmin()) {
            $sounds = SoundboardSound::where('hidden', '=', false)->get();

            return view('protube.admin', ['sounds' => $sounds]);
        } else {
            abort(403);
        }
    }

    /** @return View */
    public function screen(Request $request)
    {
        return view('protube.screen', ['showPin' => $request->has('showPin')]);
    }

    /** @return View */
    public function offline()
    {
        return view('protube.offline');
    }

    /** @return View */
    public function remote()
    {
        error_reporting(0);
        $max_duration = CarbonInterval::seconds(file_get_contents(config('herbert.server').'/maxDuration?secret='.config('herbert.secret')))->cascade()->forHumans();

        return view('protube.remote', ['max_duration' => $max_duration]);
    }

    /** @return RedirectResponse */
    public function loginRedirect()
    {
        return Redirect::away('protube::remote');
    }

    /** @return View */
    public function topVideos()
    {
        $data = (object) [
            'alltime' => $this->getTopVideos(10),
            'month' => $this->getTopVideos(10, '-1 month'),
            'week' => $this->getTopVideos(10, '-1 week'),
        ];

        return view('protube.topvideos', ['data' => $data]);
    }

    /** @return View */
    public function dashboard()
    {
        $user_count = PlayedVideo::where('user_id', Auth::user()->id)->count();

        return view('protube.dashboard', [
            'history' => $this->getHistory(),
            'usercount' => $user_count,
            'user' => Auth::user(),
            'usertop' => $this->getTopVideos(15, null, Auth::user()),
        ]);
    }

    /**
     * @param  string  $since
     * @param  User|null  $user
     * @param  int  $max
     * @return array
     */
    private function getHistory($since = '-1 week', $user = null, $max = 50)
    {
        $query = DB::table('playedvideos')
            ->select(DB::raw('video_id, video_title, spotify_id, spotify_name, created_at as played_at'))
            ->where('created_at', '>', date('Y-m-d', strtotime($since)));

        if ($user != null) {
            $query = $query->where('user_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')->limit($max)->get()->all();
    }

    /**
     * @param  int  $limit
     * @param  string|null  $since
     * @param  User|null  $user
     * @return array
     */
    private function getTopVideos($limit = 10, $since = '2011-04-20', $user = null)
    {
        $query = DB::table('playedvideos')
            ->select(DB::raw('video_id, video_title, spotify_id, spotify_name, count(*) as played_count'))
            ->where('created_at', '>', date('Y-m-d', strtotime($since)));

        if ($user != null) {
            $query = $query->where('user_id', $user->id);
        }

        return $query->groupBy('video_id')
            ->orderBy('played_count', 'desc')
            ->orderBy('created_at', 'asc')
            ->limit($limit)->get()->all();
    }

    /** @return RedirectResponse */
    public function toggleHistory()
    {
        $user = Auth::user();
        $user->keep_protube_history = ! $user->keep_protube_history;
        $user->save();

        Session::flash('flash_message', 'Changes saved.');

        return Redirect::back();
    }

    /** @return RedirectResponse */
    public function clearHistory()
    {
        $user = Auth::user();
        PlayedVideo::where('user_id', $user->id)->update(['user_id' => null]);

        Session::flash('flash_message', 'History cleared.');

        return Redirect::back();
    }
}
