<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PhotoAlbumAdminController extends Controller
{
    /** @return View */
    public function index(Request $request)
    {
        $name = $request->input('query');
        $published = PhotoAlbum::query()->where('published', true);
        $unpublished = PhotoAlbum::query()->where('published', false);

        if ($name) {
            $published = $published->name($name);
            $unpublished = $unpublished->name($name);
        }

        return view('photos.admin.index', ['query' => $name, 'published' => $published->get(), 'unpublished' => $unpublished->get()]);
    }

    /**
     * @return RedirectResponse
     */
    public function create(Request $request)
    {
        $photoalbum = new PhotoAlbum;
        $photoalbum->name = $request->input('name');
        $photoalbum->date_taken = strtotime($request->input('date'));
        if ($request->input('private')) {
            $photoalbum->private = true;
        }

        $photoalbum->save();

        return Redirect::route('photo::admin::photoalbums.edit', ['photoalbum' => $photoalbum]);
    }

    /**
     * @return View
     */
    public function edit(PhotoAlbum $photoalbum)
    {
        Gate::authorize('edit', PhotoAlbum::class);
        $photoalbum = $photoalbum
            ->load([
                'items' => function ($query) {
                    $query->orderBy('date_taken', 'desc');
                },
                'event']);

        $fileSizeLimit = ini_get('post_max_size');

        return view('photos.admin.edit', ['album' => $photoalbum, 'fileSizeLimit' => $fileSizeLimit]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request, PhotoAlbum $photoalbum)
    {
        Gate::authorize('update', $photoalbum);
        $photoalbum->name = $request->input('album');
        $photoalbum->date_taken = strtotime($request->input('date'));
        $photoalbum->private = (bool) $request->input('private');
        $photoalbum->save();

        return Redirect::route('photo::admin::photoalbums.index', ['photoalbum' => $photoalbum]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete(PhotoAlbum $photoalbum)
    {
        Gate::authorize('delete', PhotoAlbum::class);
        $photoalbum->items->each->delete();
        $photoalbum->delete();

        return Redirect::route('photo::admin::photoalbums.index');
    }

    /**
     * @return RedirectResponse
     */
    public function action(Request $request, PhotoAlbum $photoalbum)
    {
        $action = $request->input('action');
        $photos = $request->input('photos');

        if ($photos) {

            if ($photoalbum->published && ! Auth::user()->can('publishalbums')) {
                abort(403, 'Unauthorized action.');
            }

            switch ($action) {
                case 'remove':
                    foreach ($photos as $photoId) {
                        Photo::query()->find($photoId)->delete();
                    }

                    break;

                case 'thumbnail':
                    $photoalbum->thumb_id = (int) $photos[0];
                    break;

                case 'private':
                    foreach ($photos as $photoId) {
                        $photo = Photo::query()->find($photoId);
                        if ($photoalbum->published && $photo->private) {
                            continue;
                        }

                        $photo->private = ! $photo->private;
                        $photo->save();
                    }

                    break;
            }

            $photoalbum->save();
        }

        return Redirect::route('photo::admin::photoalbums.edit', ['photoalbum' => $photoalbum]);
    }

    /**
     * @return RedirectResponse
     */
    public function publish(PhotoAlbum $photoalbum)
    {
        Gate::authorize('publish', PhotoAlbum::class);

        if (! $photoalbum->items()->exists() || $photoalbum->thumb_id === null) {
            Session::flash('flash_message', 'Albums need at least one photo and a thumbnail to be published.');

            return Redirect::back();
        }

        $photoalbum->published = true;
        $photoalbum->save();

        return Redirect::route('photo::admin::photoalbums.edit', ['photoalbum' => $photoalbum]);
    }

    /**
     * @return RedirectResponse
     */
    public function unpublish(PhotoAlbum $photoalbum)
    {
        Gate::authorize('unpublish', PhotoAlbum::class);
        $photoalbum->update(['published' => false]);

        return Redirect::route('photo::admin::photoalbums.edit', ['photoalbum' => $photoalbum]);
    }
}
