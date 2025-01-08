<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackReplyEmail;
use App\Models\Feedback;
use App\Models\FeedbackCategory;
use App\Models\FeedbackVote;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(string $category): View
    {
        $category = FeedbackCategory::query()->where('url', $category)->firstOrFail();
        $mostVoted = $this->getMostVoted($category);

        $unreviewed = $this->getUnreviewed($category);

        return view('feedbackboards.index', ['data' => $this->getFeedbackQuery($category)->paginate(20), 'mostVoted' => $mostVoted ?? null, 'category' => $category, 'unreviewed' => $unreviewed]);
    }

    public function goodIdeas(): View
    {
        return $this->index('goodideas');
    }

    public function quotes(): View
    {
        return $this->index('quotes');
    }

    private function getFeedbackQuery(FeedbackCategory $category): HasMany
    {
        $feedback = $category->feedback()
            ->orderBy('created_at', 'desc')
            ->with('votes');

        if ($category->review) {
            return $feedback->where('reviewed', true);
        }

        return $feedback;
    }

    private function getMostVoted(FeedbackCategory $category): Model|Feedback|null
    {
        // find the most voted piece of feedback
        $mostVotedID = FeedbackVote::query()
            ->whereHas('feedback', static function ($query) use ($category) {
                $query->where('feedback_category_id', $category->id)
                    ->where('created_at', '>', Carbon::now()->subMonth());
            })
            ->groupBy('feedback_id')
            ->selectRaw('feedback_id, sum(vote) as votes')
            ->having('votes', '>=', 0)
            ->orderBy('votes', 'desc')
            ->first();

        $mostVoted = Feedback::query()->where('id', $mostVotedID?->feedback_id)->first();

        return $mostVoted ?? null;
    }

    private function getUnreviewed(FeedbackCategory $category): array|Collection
    {
        if ($category->review) {
            $unreviewed = Feedback::query()->where('reviewed', false)->where('feedback_category_id', $category->id);

            // get all unreviewed feedback for authorized users
            if (Auth::user()->id === $category->reviewer_id || Auth::user()->can('sysadmin')) {
                return $unreviewed->limit(20)->get();
            }

            return $unreviewed->where('user_id', Auth::user()->id)->limit(20)->get();
        }

        return [];
    }

    public function search(Request $request, string $category): View
    {
        $searchTerm = $request->input('searchTerm');
        $category = FeedbackCategory::query()->where('url', $category)->firstOrFail();
        $mostVoted = $this->getMostVoted($category);
        $unreviewed = $this->getUnreviewed($category);
        $feedback = $this->getFeedbackQuery($category)->where('feedback', 'LIKE', "%{$searchTerm}%");

        return view('feedbackboards.index', ['data' => $feedback->paginate(20), 'mostVoted' => $mostVoted, 'category' => $category, 'unreviewed' => $unreviewed]);
    }

    /**
     * @param  FeedbackCategory  $category
     */
    public function archived($category): View|RedirectResponse
    {
        $category = FeedbackCategory::query()->where('url', $category)->firstOrFail();
        if (! Auth::user()->can('board')) {
            Session::flash('flash_message', 'You are not allowed to view archived feedback.');

            return Redirect::back();
        }

        $feedback = Feedback::onlyTrashed()->where('feedback_category_id', $category->id)->orderBy('created_at', 'desc');

        return view('feedbackboards.archive', ['data' => $feedback->paginate(20), 'category' => $category]);
    }

    public function store(Request $request, $category): RedirectResponse
    {
        $category = FeedbackCategory::query()->findOrFail($category);
        $feedback = new Feedback(['feedback' => trim($request->input('feedback')), 'user_id' => Auth::id(), 'feedback_category_id' => $category->id]);
        $feedback->save();

        $categoryTitle = Str::singular($category->title);
        if ($category->review) {
            Session::flash('flash_message', "{$categoryTitle} added. This first needs to be reviewed by the board so it might take some time to show up!");
        } else {
            Session::flash('flash_message', "{$categoryTitle} added.");
        }

        return Redirect::back();
    }

    public function reply(int $id, Request $request): RedirectResponse
    {
        $feedback = Feedback::query()->findOrFail($id);

        $categoryTitle = Str::singular($feedback->category->title);
        if (! Auth::user()->can('board')) {
            Session::flash('flash_message', "You are not allowed to reply to this {$categoryTitle}.");

            return Redirect::back();
        }

        $reply = $request->input('reply');
        $accepted = $request->input('responseBtn') === 'accept';

        if ($feedback->reply == null && $reply != null) {
            $user = User::query()->findOrFail($feedback->user_id);
            Mail::to($user)->queue((new FeedbackReplyEmail($feedback, $user, $reply, $accepted))->onQueue('low'));
        }

        $feedback->reply = $reply;
        $feedback->accepted = $accepted;
        $feedback->save();

        $acceptText = $accepted ? 'accepted' : 'rejected';
        Session::flash('flash_message', "You have {$acceptText} this {$categoryTitle} with a reply.");

        return Redirect::back();
    }

    public function archive(int $id): RedirectResponse
    {
        $feedback = Feedback::withTrashed()->findOrFail($id);
        $categoryTitle = Str::singular($feedback->category->title);

        if (! Auth::user()->can('board')) {
            Session::flash('flash_message', "You are not allowed to archive this {$categoryTitle}.");

            return Redirect::back();
        }

        if ($feedback->trashed()) {
            $feedback->restore();
            Session::flash('flash_message', 'Feedback restored.');
        } else {
            $feedback->delete();
            Session::flash('flash_message', 'Feedback archived.');
        }

        return Redirect::back();
    }

    public function restore(int $id): RedirectResponse
    {
        if (! Auth::user()->can('board')) {
            Session::flash('flash_message', 'You are not allowed to restore this feedback.');

            return Redirect::back();
        }

        $feedback = Feedback::onlyTrashed()->findOrFail($id);
        $feedback->restore();
        Session::flash('flash_message', 'Feedback restored.');

        return Redirect::back();
    }

    public function delete(int $id): RedirectResponse
    {
        $feedback = Feedback::withTrashed()->findOrFail($id);
        if (! Auth::user()->can('board') && Auth::user()->id != $feedback->user->id) {
            Session::flash('flash_message', 'You are not allowed to delete this feedback.');

            return Redirect::back();
        }

        if (! Auth::user()->can('board') && $feedback->reply) {
            Session::flash('flash_message', 'You are not allowed to delete this feedback as it has already received a reply.');

            return Redirect::back();
        }

        $feedback->votes()->delete();
        $feedback->forceDelete();
        Session::flash('flash_message', 'Feedback deleted.');

        return Redirect::back();
    }

    public function archiveAll(string $category): RedirectResponse
    {
        $category = FeedbackCategory::query()->where('url', $category)->firstOrFail();
        $feedback = $category->feedback;
        foreach ($feedback as $item) {
            $item->delete();
        }

        return Redirect::route('feedback::archived', ['category' => $category->url]);
    }

    public function vote(Request $request): JsonResponse
    {
        $feedback = Feedback::query()->findOrFail($request->input('id'));

        /** @var FeedbackVote $vote */
        $vote = FeedbackVote::query()->firstOrCreate(['user_id' => Auth::id(), 'feedback_id' => $request->input('id')]);
        if ($vote->vote === $request->input('voteValue')) {
            $vote->delete();
        } else {
            $vote->vote = $request->input('voteValue') > 0 ? 1 : -1;
            $vote->save();
        }

        return response()->json([
            'voteScore' => $feedback->voteScore(),
            'userVote' => $feedback->userVote(Auth::user()),
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $feedback = Feedback::query()->findOrFail($id);
        if ($feedback->category->reviewer_id !== Auth::user()->id && ! Auth::user()->can('sysadmin')) {
            Session::flash('flash_message', 'Feedback may only be approved by the dedicated reviewer!');

            return Redirect::back();
        }

        $feedback->reviewed = true;
        $feedback->save();
        Session::flash('flash_message', 'Feedback Approved to be public!');

        return Redirect::back();
    }

    public function categoryAdmin(Request $request): View
    {
        $category = FeedbackCategory::query()->find($request->id);

        return view('feedbackboards.categories', ['categories' => FeedbackCategory::all(), 'cur_category' => $category]);
    }

    public function categoryStore(Request $request): RedirectResponse
    {
        // regex to remove all non-alphanumeric characters
        $newUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $request->input('name')));
        if (FeedbackCategory::query()->where('url', $newUrl)->first()) {
            Session::flash('flash_message', 'This category-url already exists! Try a different name!');

            return Redirect::back();
        }

        if ($request->has('reviewed') && ! $request->input('user_id')) {
            Session::flash('flash_message', 'You need to enter a reviewer to have this as a reviewed category!');

            return Redirect::back();
        }

        $category = FeedbackCategory::query()->create([
            'title' => $request->input('name'),
            'url' => $newUrl,
            'review' => $request->has('can_review'),
            'reviewer_id' => $request->has('can_review') ? $request->input('user_id') : null,
            'can_reply' => $request->has('can_reply'),
            'show_publisher' => $request->has('show_publisher'),
        ]);

        Session::flash('flash_message', 'The category '.$category->title.' has been created.');

        return Redirect::back();
    }

    public function categoryUpdate(Request $request, int $id)
    {
        // regex to remove all non-alphanumeric characters
        $newUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $request->input('name')));
        if (FeedbackCategory::query()->where('url', $newUrl)->first() && FeedbackCategory::query()->where('url', $newUrl)->first()->id !== $id) {
            Session::flash('flash_message', 'This category-url already exists! Try a different name!');

            return Redirect::back();
        }

        if ($request->has('can_review') && ! $request->input('user_id')) {
            Session::flash('flash_message', 'You need to enter a reviewer to have this as a reviewed category!');

            return Redirect::back();
        }

        $category = FeedbackCategory::query()->findOrFail($id);
        /** @var FeedbackCategory $category */
        $category->title = $request->input('name');
        $category->url = $newUrl;
        $category->review = $request->has('can_review');
        $category->reviewer_id = $request->has('can_review') ? $request->input('user_id') : null;
        $category->can_reply = $request->has('can_reply');
        $category->show_publisher = $request->has('show_publisher');
        $category->save();

        Session::flash('flash_message', 'The category '.$category->title.' has been updated.');

        return Redirect::back();
    }

    /**
     * @throws Exception
     */
    public function categoryDestroy(int $id): RedirectResponse
    {
        /** @var FeedbackCategory $category */
        $category = FeedbackCategory::query()->findOrFail($id);
        foreach ($category->feedback as $item) {
            $item->category()->dissociate();
        }

        $category->delete();

        Session::flash('flash_message', 'The category '.$category->title.' has been deleted.');

        return Redirect::route('feedback::category::admin', ['category' => null]);
    }
}
