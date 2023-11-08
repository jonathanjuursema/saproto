<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LikedPicturesController extends Controller
{
    /**
     * @return View
     */
    public function show()
    {
        $likedPhotos = Photo::whereHas('likes', function (Builder $query) {
            $query->where('user_id', Auth::user()->id);
        })->orderBy('date_taken', 'asc')->orderBy('id', 'asc')->paginate(24);

        $album = new \stdClass();
        $album->title = 'My liked photos';
        $album->name = 'My liked photos';
        $album->date_taken = \Carbon::today()->timestamp;
        $album->event = null;

        if ($likedPhotos->isEmpty()) {
            abort(404, 'No liked photos!');
        }

        return view('photos.album', ['album' => $album, 'photos' => $likedPhotos, 'liked' => true]);
    }

    /**
     * @param int $id
     * @return View
     */
    public function photo($id)
    {
        $photo = (new PhotoController())->getPhoto($id)->getData();

        return view('photos.photopage', ['photo' => $photo, 'nextRoute' => route('api::photos::getNextLikedPhoto', ['id' => ':id']), 'previousRoute' => route('api::photos::getPreviousLikedPhoto', ['id' => ':id'])]);
    }

    /** @return JsonResponse */
    public function getNextPhoto($id)
    {
        return $this->getAdjacentResponse($id, true);
    }

    /** @return JsonResponse */
    public function getPreviousPhoto($id)
    {
        return $this->getAdjacentResponse($id, false);
    }

    /**
     * @param int $id
     * @param bool $next
     * @return JsonResponse
     */
    private function getAdjacentResponse($id, $next)
    {
        $photo = Photo::findOrFail($id);
        $adjacent = $this->getAdjacentPhoto($photo, $next);

        if ($adjacent) {
            return response()->JSON([
                'id' => $adjacent->id,
                'largeUrl' => $adjacent->getLargeUrlAttribute(),
                'tinyUrl' => $adjacent->getTinyUrlAttribute(),
                'albumUrl' => route('photo::album::list', ['id' => $photo->album_id]) . '?page=' . $photo->getAlbumPageNumber(24),
                'albumTitle' => $photo->album->name,
                'likes' => $adjacent->getLikes(),
                'likedByUser' => $adjacent->likedByUser(Auth::user()),
                'private' => $adjacent->private,
                'hasNextPhoto' => (bool)$this->getAdjacentPhoto($adjacent, true),
                'hasPreviousPhoto' => (bool)$this->getAdjacentPhoto($adjacent, false),
                'downloadUrl' => route('image::get', ['id' => $photo->file->id, 'hash' => $photo->file->hash]),
            ]);
        }

        return response()->json(['message' => 'adjacent photo not found.'], 404);
    }

    private function getAdjacentPhoto($photo, $next)
    {
        if ($next) {
            $ord = 'ASC';
            $comp = '>';
        } else {
            $ord = 'DESC';
            $comp = '<';
        }
        $adjacent = Photo::whereHas('likes', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->where('date_taken', $comp . '=', $photo->date_taken);

        if (Auth::user() == null || Auth::user()->member() == null) {
            $adjacent = $adjacent->where('private', false);
        }

        $adjacent = $adjacent->orderBy('date_taken', $ord)->orderBy('id', $ord);

        if ($adjacent->count() <= 1) {
            return null;
        }

        return $adjacent->where('id', $comp, $photo->id)->first();
    }
}
