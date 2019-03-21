<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Proto\Http\Requests;
use Proto\Http\Controllers\Controller;
use Proto\Models\Flickr;
use Proto\Models\PhotoLikes;
use Auth;

use Proto\Models\FlickrAlbum;
use Redirect;

class PhotoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Flickr::getAlbums(null);

        return view('photos.list', ['albums' => $albums]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $photos = Flickr::getPhotos($id);

        if ($photos) return view('photos.album', ['photos' => $photos]);

        abort(404);
    }

    public function photo($id)
    {
        $photo = Flickr::getPhoto($id);
        if ($photo) return view('photos.photopage', ['photo' => $photo]);
    }

    public function likePhoto($photoID)
    {

        $exist = PhotoLikes::where('user_id', Auth::user()->id)->where('photo_id', $photoID)->count();

        if ($exist == null) {

            PhotoLikes::create([
                'photo_id' => $photoID,
                'user_id' => Auth::user()->id,
            ]);
        }

        return Redirect::route("photo::view", ["id" => $photoID]);
    }

    public function dislikePhoto($photoID)
    {
            PhotoLikes::where('user_id', Auth::user()->id)->where('photo_id', $photoID)->delete();

        return Redirect::route("photo::view", ["id" => $photoID]);
    }

    /**
     * Return JSON for a listing of the resource.
     *
     * @return string
     */
    public function apiIndex()
    {
        $albums = Flickr::getAlbums();
        return json_encode($albums);
    }

    /**
     * Return JSON for the specified resource.
     *
     * @param $id
     * @return string
     */
    public function apiShow($id)
    {
        $photos = Flickr::getPhotos($id);
        return json_encode($photos);
    }

    public function slideshow()
    {
        return view('photos.slideshow');
    }


}
