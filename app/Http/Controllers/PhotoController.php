<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\PhotoLikes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /** @return View */
    public function index()
    {
        $albums = PhotoAlbum::query()->orderBy('date_taken', 'desc')
            ->where('published', true)
            ->paginate(24);

        return view('photos.list', ['albums' => $albums]);
    }

    public function show(int $id): View|RedirectResponse
    {
        $album = PhotoAlbum::query()->findOrFail($id);

        $photos = $album->items()->orderBy('date_taken', 'desc')->paginate(24);

        return view('photos.album', ['album' => $album, 'photos' => $photos]);
    }

    /**
     * @return View
     */
    public function photo(int $id)
    {
        $photo = Photo::with('album')
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => static function ($q) {
                $q->where('user_id', Auth::id());
            }])->findOrFail($id);

        return view('photos.photopage', ['photo' => $photo]);
    }

    /**
     * @return RedirectResponse
     */
    public function toggleLike(int $photo_id)
    {
        $like = PhotoLikes::query()->where('user_id', Auth::user()->id)->where('photo_id', $photo_id)->first();

        if ($like) {
            $like->delete();

            return Redirect::route('photo::view', ['id' => $photo_id]);
        }

        PhotoLikes::query()->create([
            'photo_id' => $photo_id,
            'user_id' => Auth::user()->id,
        ]);

        return Redirect::route('photo::view', ['id' => $photo_id]);
    }
}
