<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Models\Event;
use App\Models\Newsitem;
use App\Models\StorageEntry;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use stdClass;

class NewsController extends Controller
{
    /** @return View */
    public function admin()
    {
        $newsitems = Newsitem::query()->orderBy('published_at', 'desc')->paginate(20);

        return view('news.admin', ['newsitems' => $newsitems]);
    }

    /** @return View */
    public function index()
    {
        $newsitems = Newsitem::all()->whereNotNull('published_at')->where('published_at', '<=', Carbon::now())->sortByDesc('published_at');

        return view('news.list', ['newsitems' => $newsitems]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $preview = false;

        $newsitem = Newsitem::query()->findOrFail($id);

        if (! $newsitem->isPublished()) {
            if (Auth::user()?->can('board')) {
                $preview = true;
            } else {
                abort(404);
            }
        }

        $events = Event::query()->whereIn('id', $newsitem->events()->pluck('id'))->get();

        return view('news.show', [
            'newsitem' => $newsitem,
            'parsedContent' => Markdown::convert($newsitem->content),
            'preview' => $preview, 'events' => $events,
        ]);
    }

    public function showWeeklyPreview(int $id)
    {
        $newsitem = Newsitem::query()->findOrFail($id);

        if (! $newsitem->published_at && ! Auth::user()?->can('board')) {
            Session::flash('flash_message', 'This weekly newsletter has not been published yet.');

            return Redirect::back();
        }

        return view('emails.newsletter', [
            'user' => Auth::user(),
            'list' => EmailList::query()->find(Config::integer('proto.weeklynewsletter')),
            'events' => $newsitem->events()->get(),
            'text' => $newsitem->content,
            'image_url' => $newsitem->featuredImage?->generateImagePath(600, 300),
        ]);
    }

    /** @return View */
    public function create(Request $request)
    {
        $lastWeekly = Newsitem::query()->where('is_weekly', true)->orderBy('published_at', 'desc')->first();
        $upcomingEvents = Event::query()->where('start', '>', date('U'))->where('secret', false)->orderBy('start')->get();

        return view('news.edit', ['item' => null, 'new' => true, 'is_weekly' => $request->boolean('is_weekly'), 'upcomingEvents' => $upcomingEvents, 'events' => [], 'lastWeekly' => $lastWeekly]);
    }

    /** @return View */
    public function edit($id)
    {
        $newsitem = Newsitem::query()->findOrFail($id);
        $upcomingEvents = Event::query()->where('start', '>', date('U'))->where('secret', false)->orderBy('start')->get()->merge($newsitem->events()->get());
        $events = $newsitem->events()->pluck('id')->toArray();
        $lastWeekly = Newsitem::query()->where('is_weekly', true)->orderBy('published_at', 'desc')->first();

        return view('news.edit', ['item' => $newsitem, 'new' => false, 'upcomingEvents' => $upcomingEvents, 'events' => $events, 'is_weekly' => $newsitem->is_weekly, 'lastWeekly' => $lastWeekly]);
    }

    public function store(Request $request): RedirectResponse
    {
        $newsitem = new Newsitem;

        return $this->storeNews($newsitem, $request);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::query()->findOrFail($id);

        return $this->storeNews($newsitem, $request);
    }

    public function storeNews(Newsitem $newsitem, Request $request): RedirectResponse
    {
        $newsitem->user_id = Auth::user()->id;
        $newsitem->content = $request->input('content');

        if ($request->has('title')) {
            $newsitem->is_weekly = false;
            $newsitem->title = $request->input('title');
            $newsitem->published_at = date('Y-m-d H:i:s', strtotime($request->published_at));
        } else {
            $newsitem->is_weekly = true;
            $newsitem->title = 'Weekly update for week '.date('W').' of '.date('Y').'.';
            $newsitem->published_at = null;
        }

        $newsitem->save();

        $newsitem->events()->sync($request->input('event'));

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry;
            $file->createFromFile($image);
            $file->save();
            $newsitem->featuredImage()->associate($file);
        }

        $newsitem->save();

        return Redirect::route('news::edit', ['id' => $newsitem->id]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(int $id)
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::query()->findOrFail($id);

        Session::flash('flash_message', 'Newsitem '.$newsitem->title.' has been removed.');

        $newsitem->delete();

        return Redirect::route('news::admin');
    }

    public function sendWeeklyEmail(int $id)
    {
        $newsitem = Newsitem::query()->findOrFail($id);
        if (! Auth::user()->can('board')) {
            abort(403, 'Only the board can do this.');
        }

        Artisan::call('proto:newslettercron', ['id' => $newsitem->id]);

        $newsitem->published_at = date('Y-m-d H:i:s', Carbon::now()->timestamp);

        $newsitem->save();
        Session::flash('flash_message', 'Newsletter has been sent.');

        return Redirect::route('news::admin');
    }

    public function apiIndex(): array
    {
        $newsitems = Newsitem::all()->sortByDesc('published_at');

        $return = [];

        foreach ($newsitems as $newsitem) {
            if ($newsitem->isPublished()) {
                $returnItem = new stdClass;
                $returnItem->id = $newsitem->id;
                $returnItem->title = $newsitem->title;
                $returnItem->featured_image_url = $newsitem->featuredImage ? $newsitem->featuredImage->generateImagePath(700, null) : null;
                $returnItem->content = $newsitem->content;
                $returnItem->published_at = strtotime($newsitem->published_at);

                $return[] = $returnItem;
            }
        }

        return $return;
    }
}
