<?php

namespace Proto\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Proto\Models\Photo;
use Proto\Models\PhotoAlbum;
use Proto\Models\User;

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

        $album=new \stdClass();
        $album->title='my liked photos!';
        $album->name='my liked photos!';
        $album->date_taken=\Carbon::today()->timestamp;
        $album->event=null;

        if ($likedPhotos->count()) {
            return view('photos.album', ['album' => $album, 'photos' => $likedPhotos, 'liked'=>true]);
        }

        abort(404, 'No liked photos!');
    }

    /**
     * @param int $id
     * @return View
     */
    public function photo($id)
    {
        $photo = Photo::findOrFail($id);
        return view('photos.photopage', ['photo' => $photo, 'nextRoute'=> route('api::photos::getNextLikedPhoto', ['id' => ':id']), 'previousRoute'=>route('api::photos::getPreviousLikedPhoto', ['id' => ':id'])]);
    }

    /** @return JsonResponse */
    public function getNextPhoto($id)
    {
        return $this->getAdjacentResponse($id, true);
    }

    /** @return JsonResponse */
    public function getPreviousPhoto($id)
    {
        return $this->getAdjacentResponse($id, true);
    }

    /**
     * @param int $id
     * @param bool $next
     * @return JsonResponse
     */
    private function getAdjacentResponse($id, $next)    {
        $photo = Photo::findOrFail($id);
        $adjacent= $this->getAdjacentPhoto($photo, $next);

        if($adjacent) {
            return response()->JSON([
                'id' =>$adjacent->id,
                'originalUrl' => $adjacent->getOriginalUrl(),
                'largeUrl' => $adjacent->getLargeUrl(),
                'tinyUrl' => $adjacent->getTinyUrl(),
                'albumUrl' => route('photo::album::list', ['id' => $photo->album_id]).'?page='.$photo->getAlbumPageNumber(24),
                'likes'=>$adjacent->getLikes(),
                'likedByUser'=>$adjacent->likedByUser(Auth::user()),
                'private' => $adjacent->private,
                'hasNextPhoto'=> (bool)$this->getAdjacentPhoto($adjacent, true),
                'hasPreviousPhoto'=>(bool)$this->getAdjacentPhoto($adjacent, false),
            ]);
        }
        return response()->json(['message' => 'adjacent photo not found.'], 404);
    }

    private function getAdjacentPhoto($photo, $next){
        if ($next) {
            $ord = 'ASC';
            $comp = '>';
        } else {
            $ord = 'DESC';
            $comp = '<';
        }
        $adjacent = Photo::whereHas('likes', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->where('date_taken', $comp.'=', $photo->date_taken);

        if(Auth::user() == null || Auth::user()->member() == null) $adjacent = $adjacent->where('private', false);

        $adjacent = $adjacent->orderBy('date_taken', $ord)->orderBy('id', $ord);
        if ($adjacent->count() > 1) {
            return $adjacent->where('id', $comp, $photo->id)->first();
        }
        return null;
    }
}
