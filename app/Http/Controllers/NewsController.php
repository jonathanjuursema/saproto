<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Newsitem;
use App\Models\StorageEntry;
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
        $newsitems = Newsitem::all()->sortByDesc('published_at');

        $return = [];

        foreach ($newsitems as $newsitem) {
            if ($newsitem->isPublished()) {
                $return[] = $newsitem;
            }
        }

        return view('news.list', ['newsitems' => $return]);
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

        return view('news.show', ['newsitem' => $newsitem, 'parsedContent' => Markdown::convert($newsitem->content), 'preview' => $preview]);
    }

    /** @return View */
    public function create()
    {
        return view('news.edit', ['item' => null, 'new' => true]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $newsitem = new Newsitem();

        $newsitem->fill($request->all());
        $newsitem->published_at = date('Y-m-d H:i:s', strtotime($request->published_at));
        $newsitem->user_id = Auth::user()->id;
        $newsitem->save();

        return Redirect::route('news::admin');
    }

    /** @return View */
    public function edit($id)
    {
        $newsitem = Newsitem::findOrFail($id);

        return view('news.edit', ['item' => $newsitem, 'new' => false]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::findOrFail($id);

        $newsitem->fill($request->all());
        $newsitem->published_at = date('Y-m-d H:i:s', strtotime($request->published_at));
        $newsitem->save();

        return Redirect::route('news::admin');
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

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function featuredImage(Request $request, $id)
    {
        /** @var Newsitem $newsitem */
        $newsitem = Newsitem::findOrFail($id);

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry();
            $file->createFromFile($image);
            $newsitem->featuredImage()->associate($file);
        } else {
            $newsitem->featuredImage()->dissociate();
        }
        $newsitem->save();

        return Redirect::route('news::edit', ['id' => $id]);
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
