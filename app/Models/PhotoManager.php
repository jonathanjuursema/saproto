<?php

namespace App\Models;

use Auth;
use Eloquent;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Photo Manager Model.
 *
 * @method static Builder|PhotoManager newModelQuery()
 * @method static Builder|PhotoManager newQuery()
 * @method static Builder|PhotoManager query()
 *
 * @mixin Eloquent
 */
class PhotoManager extends Model
{
    /**
     * @param  int|null  $max
     * @param  string|null  $query
     * @param  bool  $unpublished
     * @param  bool  $no_thumb
     * @return Collection|LengthAwarePaginator
     */
    public static function getAlbums($max = null, $query = null, $unpublished = false, $no_thumb = true)
    {
        $include_private = (Auth::check() && Auth::user()->member() !== null);
        $base = PhotoAlbum::orderBy('date_taken', 'desc');

        if (! $include_private) {
            $base = $base->where('private', '=', false);
        }
        if ($query) {
            $base = $base->where('name', 'LIKE', '%'.$query.'%');
        }
        if ($unpublished) {
            $base = $base->where('published', '=', false);
        } else {
            $base = $base->where('published', '=', true);
        }
        if (! $no_thumb) {
            $base = $base->where('thumb_id', '!=', 'null');
        }
        if ($max != 0) {
            $albums = $base->paginate($max);
        } else {
            $albums = $base->get();
        }

        return $albums;
    }

    /**
     * @param  int  $album_id
     * @param  int|null  $max
     * @return stdClass|null
     */
    public static function getPhotos($album_id, $max = null)
    {
        $include_private = (Auth::check() && Auth::user()->member() !== null);

        $album = PhotoAlbum::where('id', $album_id);
        if (! $include_private) {
            $album->where('private', '=', false);
        }
        $album = $album->get();

        if ($album->count() == 0) {
            return null;
        }

        $items = Photo::where('album_id', $album_id);

        if (! $include_private) {
            $items = $items->where('private', '=', false);
        }
        $items = $items->orderBy('date_taken', 'asc')->orderBy('id', 'asc');
        if ($max != 0) {
            $items = $items->paginate($max);
        } else {
            $items = $items->get();
        }

        $data = new stdClass();
        $data->album_id = $album_id;

        $album = $album->first();
        $data->album_title = $album->name;
        $data->album_date = $album->date_taken;
        $data->event = ($album->event ? $album->event : null);
        $data->private = $album->private;
        $data->published = $album->published;
        $data->thumb = $album->thumb();
        $data->photos = $items;

        return $data;
    }

    /**
     * @param  int  $photo_id
     * @return stdClass
     */
    public static function getPhoto($photo_id)
    {
        $photo = Photo::findOrFail($photo_id);

        $data = new stdClass();
        $data->photo_url = $photo->url;
        $data->album_id = $photo->album_id;
        $data->album_name = $photo->album->name;
        $data->private = $photo->private;
        $data->likes = $photo->getLikes();
        $data->liked = Auth::check() ? PhotoLikes::where('photo_id', '=', $photo_id)->where('user_id', Auth::id())->count() : 0;

        if ($photo->getNextPhoto() != null) {
            $data->next = $photo->getNextPhoto()->id;
        } else {
            $data->next = null;
        }

        if ($photo->getPreviousPhoto() != null) {
            $data->previous = $photo->getPreviousPhoto()->id;
        } else {
            $data->previous = null;
        }

        $data->id = $photo_id;
        $data->albumPage = $photo->getAlbumPageNumber(24);

        return $data;
    }

    /**
     * @param  int  $album_id
     *
     * @throws Exception
     */
    public static function deleteAlbum($album_id)
    {
        $album = PhotoAlbum::find($album_id);
        $photos = $album->items;

        foreach ($photos as $photo) {
            $photo->delete();
        }
        $album->delete();
    }
}
