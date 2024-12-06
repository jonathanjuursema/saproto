<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\StorageEntry;
use Exception;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * These slugs can't be used for pages, as they are used by the app.
     *
     * @var string[]
     */
    protected $reservedSlugs = ['add', 'edit', 'delete'];

    /** @return View */
    public function index()
    {
        $pages = Page::query()->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.list', ['pages' => $pages]);
    }

    /** @return View */
    public function create()
    {
        return view('pages.edit', ['item' => null, 'new' => true]);
    }

    /**
     * @return RedirectResponse|View
     */
    public function store(Request $request)
    {
        $page = new Page($request->all());

        $page->is_member_only = $request->has('is_member_only');

        $page->show_attachments = $request->has('show_attachments');

        if (in_array($request->slug, $this->reservedSlugs)) {
            Session::flash('flash_message', "This URL has been reserved and can't be used. Please choose a different URL.");

            return view('pages.edit', ['item' => $page, 'new' => true]);
        }

        if (Page::query()->where('slug', $page->slug)->exists()) {
            Session::flash('flash_message', "This URL has already been used and can't be used again. Please choose a different URL.");

            return view('pages.edit', ['item' => $page, 'new' => true]);
        }

        $page->save();

        Session::flash('flash_message', "Page $page->title has been created.");

        return Redirect::route('page::list');
    }

    /**
     * @param  string  $slug
     * @return View
     */
    public function show($slug)
    {
        $page = Page::query()->where('slug', '=', $slug)->first();

        if ($page == null) {
            abort(404, 'Page not found.');
        }

        if ($page->is_member_only && ! Auth::user()?->is_member) {
            abort(403, 'You need to be a member of S.A. Proto to see this page.');
        }

        return view('pages.show', ['page' => $page, 'parsedContent' => Markdown::convert($page->content)]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $page = Page::query()->findOrFail($id);

        return view('pages.edit', ['item' => $page, 'new' => false]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse|View
     */
    public function update(Request $request, $id)
    {
        /** @var Page $page */
        $page = Page::query()->findOrFail($id);

        if (($request->slug != $page->slug) && Page::query()->where('slug', $page->slug)->exists()) {
            Session::flash('flash_message', "This URL has been reserved and can't be used. Please choose a different URL.");

            return view('pages.edit', ['item' => $request, 'new' => false]);
        }

        $page->fill($request->all());

        $page->is_member_only = $request->has('is_member_only');

        $page->show_attachments = $request->has('show_attachments');

        if (in_array($request->slug, $this->reservedSlugs)) {
            Session::flash('flash_message', "This URL has been reserved and can't be used. Please choose a different URL.");

            return view('pages.edit', ['item' => $page, 'new' => false]);
        }

        $page->save();

        Session::flash('flash_message', 'Page '.$page->title.' has been saved.');

        return Redirect::route('page::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Page $page */
        $page = Page::query()->findOrfail($id);

        Session::flash('flash_message', 'Page '.$page->title.' has been removed.');

        $page->delete();

        return Redirect::route('page::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function featuredImage(Request $request, $id)
    {
        $page = Page::query()->find($id);

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry;
            $file->createFromFile($image);
            $page->featuredImage()->associate($file);
        } else {
            $page->featuredImage()->dissociate();
        }

        $page->save();

        return Redirect::route('page::edit', ['id' => $id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function addFile(Request $request, $id)
    {
        if (! $request->file('files')) {
            Session::flash('flash_message', 'You forgot to add any files.');

            return Redirect::back();
        }

        $page = Page::query()->find($id);

        foreach ($request->file('files') as $file) {
            $newFile = new StorageEntry;
            $newFile->createFromFile($file);

            $page->files()->attach($newFile);
            $page->save();
        }

        return Redirect::route('page::edit', ['id' => $id]);
    }

    /**
     * @param  int  $id
     * @param  int  $file_id
     * @return RedirectResponse
     */
    public function deleteFile($id, $file_id)
    {
        $page = Page::query()->find($id);

        $page->files()->detach($file_id);
        $page->save();

        return Redirect::route('page::edit', ['id' => $id]);
    }
}
