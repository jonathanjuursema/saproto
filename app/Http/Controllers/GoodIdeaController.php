<?php

namespace App\Http\Controllers;

use Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\GoodIdea;
use App\Models\GoodIdeaVote;
use Session;

class GoodIdeaController extends Controller
{
    /**
     * @param  int  $page
     * @return View
     */
    public function index($page = 1)
    {
        $goodIdeas = GoodIdea::where('updated_at', '>', Carbon::now()->subWeeks(4));
        $leastVoted = null;
        $leastVotes = INF;
        foreach ($goodIdeas->get() as $idea) {
            $voteCount = $idea->votes()->count();
            if ($voteCount < $leastVotes) {
                $leastVotes = $voteCount;
                $leastVoted = $idea;
            }
        }

        return view('goodideaboard.index', ['data' => $goodIdeas->orderBy('created_at', 'desc')->paginate(20), 'leastVoted' => $leastVoted]);
    }

    /**
     * @return RedirectResponse
     */
    public function add(Request $request)
    {
        $temp = nl2br(trim($request->input('idea')));
        $new = ['idea' => $temp, 'user_id' => Auth::id()];
        $idea = new GoodIdea($new);
        $idea->save();

        Session::flash('flash_message', 'Idea added.');

        return Redirect::route('goodideas::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete($id)
    {
        $idea = GoodIdea::findOrFail($id);
        if (! (Auth::user()->can('board') || Auth::user()->id == $idea->user->id)) {
            Session::flash('flash_message', 'You are not allowed to delete this idea.');

            return Redirect::back();
        }
        $idea->votes()->delete();
        $idea->delete();
        Session::flash('flash_message', 'Good Idea deleted.');

        return Redirect::route('goodideas::index');
    }

    /** @throws Exception */
    public function deleteAll()
    {
        $ideas = GoodIdea::all();
        foreach ($ideas as $idea) {
            $idea->delete();
        }
    }

    /**
     * @return JsonResponse
     */
    public function vote(Request $request)
    {
        $idea = GoodIdea::findOrFail($request->input('id'));

        /** @var GoodIdeaVote $vote */
        $vote = GoodIdeaVote::firstOrCreate(['user_id' => Auth::id(), 'good_idea_id' => $request->input('id')]);
        $vote->vote = $request->input('voteValue') > 0 ? 1 : -1;
        $vote->save();

        return response()->json(['voteScore' => $idea->voteScore(), 'userVote' => $idea->userVote(Auth::user())]);
    }
}
