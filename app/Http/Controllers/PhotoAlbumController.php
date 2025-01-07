<?php

namespace App\Http\Controllers;

use App\Models\PhotoAlbum;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PhotoAlbumController extends Controller
{
    /** @return View */
    public function index()
    {
        $albums = PhotoAlbum::query()->orderBy('date_taken', 'desc')
            ->where('published', true)
            ->paginate(24);

        return view('photos.albums.list', ['albums' => $albums]);
    }

    public function show(PhotoAlbum $photoAlbum): View|RedirectResponse
    {
        $photos = $photoAlbum->items()->orderBy('date_taken', 'desc')->paginate(24);

        return view('photos.albums.show', ['album' => $photoAlbum, 'photos' => $photos]);
    }
}
