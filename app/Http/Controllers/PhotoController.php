<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\PhotoLikes;
use App\Models\PhotoManager;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use stdClass;

class PhotoController extends Controller
{
    /** @return View */
    public function index()
    {
        $albums = PhotoManager::getAlbums(24);

        return view('photos.list', ['albums' => $albums]);
    }

    public function show(int $id): View|RedirectResponse
    {
        $album = PhotoAlbum::query()->findOrFail($id);

        if (! $album->published && ! Auth::user()?->can('protography')) {
            Session::flash('flash_message', 'You do not have the permissions for this.');

            return Redirect::back();
        }

        $photos = PhotoManager::getPhotos($id, 24);

        if ($photos instanceof stdClass) {
            return view('photos.album', ['photos' => $photos]);
        }

        Session::flash('flash_message', 'Album not found.');

        return Redirect::back();
    }

    /**
     * @return View
     */
    public function photo(int $id)
    {
        return view('photos.photopage', ['photo' => PhotoManager::getPhoto($id)]);
    }

    /** @return View */
    public function slideshow()
    {
        return view('photos.slideshow');
    }

    /**
     * @return View
     */
    public function likedPhotos()
    {
        $photos = Photo::query()->with('album')->whereIn('id', PhotoLikes::query()->where('user_id', Auth::id())->pluck('photo_id'))->paginate(24);
        $photos->setCollection($photos->groupBy(fn ($photo): string => $photo->album->name.' ('.date('d-m-Y', $photo->album->date_taken).')'));

        return view('photos.liked', ['albums' => $photos]);
    }

    /**
     * @return RedirectResponse
     */
    public function likePhoto(int $photo_id)
    {
        $exist = PhotoLikes::query()->where('user_id', Auth::user()->id)->where('photo_id', $photo_id)->count();

        if ($exist == null) {
            PhotoLikes::query()->create([
                'photo_id' => $photo_id,
                'user_id' => Auth::user()->id,
            ]);
        }

        return Redirect::route('photo::view', ['id' => $photo_id]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function dislikePhoto(int $photo_id)
    {
        PhotoLikes::query()->where('user_id', Auth::user()->id)->where('photo_id', $photo_id)->delete();

        return Redirect::route('photo::view', ['id' => $photo_id]);
    }

    /** @return string JSON */
    public function apiIndex()
    {
        $albums = PhotoManager::getAlbums();

        return json_encode($albums);
    }

    /**
     * @return string JSON
     */
    public function apiShow(int $id)
    {
        $photos = PhotoManager::getPhotos($id);

        return json_encode($photos);
    }
}
