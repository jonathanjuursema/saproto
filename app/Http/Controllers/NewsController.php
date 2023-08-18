<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Models\Event;
use App\Models\Newsitem;
use App\Models\Newsletter;
use App\Models\StorageEntry;
use Auth;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Redirect;
use Session;

class NewsController extends Controller
{
    /** @return View */
    public function admin()
    {
        $newsitems = Newsitem::orderBy('published_at', 'desc')->paginate(20);

        return view('news.admin', ['newsitems' => $newsitems]);
    }

    /** @return View */
    public function index()
    {
        $newsitems = Newsitem::all()->where('publication' , '<', Carbon::now()->timestamp)->sortByDesc('published_at');

        return view('news.list', ['newsitems' => $newsitems->where('is_weekly', false), 'weeklies' => $newsitems->where('is_weekly', true)]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $preview = false;

        $newsitem = Newsitem::findOrFail($id);

        if (! $newsitem->isPublished()) {
            if (Auth::user()?->can('board')) {
                $preview = true;
            } else {
                abort(404);
            }
        }

        return view('news.show', ['newsitem' => $newsitem, 'parsedContent' => Markdown::convert($newsitem->content), 'preview' => $preview, 'events' => $newsitem->events()->get()]);
    }

    public function showWeeklyPreview(int $id){
        $newsitem = Newsitem::findOrFail($id);
        return view('emails.newsletter', [
            'user' => Auth::user(),
            'list' => EmailList::find(config('proto.weeklynewsletter')),
            'events' => $newsitem->events()->get(),
            'text' => $newsitem->content,
            'image_url'=>$newsitem->featuredImage->generateImagePath(600, 300),
        ]);
    }

    /** @return View */
    public function create()
    {
        $upcomingEvents = Event::where('start', '>', date('U'))->where('secret', false)->orderBy('start')->get();
        return view('news.edit', ['item' => null, 'new' => true, 'is_weekly'=>$is_weekly, 'upcomingEvents'=>$upcomingEvents, 'events'=>[]]);
    }

    /** @return View */
    public function edit($id)
    {
        $newsitem = Newsitem::findOrFail($id);
        $upcomingEvents = Event::where('start', '>', date('U'))->where('secret', false)->orderBy('start')->get()->merge($newsitem->events()->get());
        $events=$newsitem->events()->pluck('id')->toArray();
        return view('news.edit', ['item' => $newsitem, 'new' => false, 'upcomingEvents'=>$upcomingEvents, 'events'=>$events]);
    }

    /**
     * @return RedirectResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $newsitem = new Newsitem();
        return $this->storeNews($newsitem, $request);
    }

    /**
     * @param  int  $id

     */
    public function update(Request $request, $id)
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::findOrFail($id);
        return $this->storeNews($newsitem, $request);
    }

    /**
     * @param Newsitem $newsitem
     * @param Request $request
     * @return RedirectResponse
     * @throws FileNotFoundException
     */
    public function storeNews(Newsitem $newsitem, Request $request): RedirectResponse
    {
        $newsitem->user_id = Auth::user()->id;
        $newsitem->content = $request->input('content');
        if ($request->has('title')) {
            $newsitem->published_at = date('Y-m-d H:i:s', strtotime($request->published_at));
        } else {
            $newsitem->title = "Weekly update for week " . date('W') . " of " . date('Y') . ".";
            $newsitem->published_at = null;
        }
        $newsitem->save();

        $newsitem->events()->sync($request->input('event'));

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry();
            $file->createFromFile($image);
            $file->save();
            $newsitem->featuredImage()->associate($file);
        }
        $newsitem->save();

        return Redirect::route('news::edit', ['id' => $newsitem->id]);
    }



    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::findOrFail($id);

        Session::flash('flash_message', 'Newsitem '.$newsitem->title.' has been removed.');

        $newsitem->delete();

        return Redirect::route('news::admin');
    }

    public function sendWeeklyEmail($id){
        $newsitem = Newsitem::findOrFail($id);

        Session::flash('flash_message', 'Newsletter has been sent.');
        return Redirect::route('news::admin');
    }

    /** @return array */
    public function apiIndex()
    {
        $newsitems = Newsitem::all()->sortByDesc('published_at');

        $return = [];

        foreach ($newsitems as $newsitem) {
            if ($newsitem->isPublished()) {
                $returnItem = new \stdClass();
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
