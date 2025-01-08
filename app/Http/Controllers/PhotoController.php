<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\PhotoLikes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * @return View
     */
    public function index(Photo $photo)
    {
        $photo = $photo::with('album')
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => static function ($q) {
                $q->where('user_id', Auth::id());
            }])->get();

        return view('photos.photopage', ['photo' => $photo]);
    }

    public function show()
    {
    }

    /**
     * @return JsonResponse|string
     */
    public function store(Request $request, PhotoAlbum $photoalbum)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'photo not found in request!',
            ], 404);
        }

        if ($photoalbum->published) {
            return response()->json([
                'message' => 'album already published! Unpublish to add more photos!',
            ], 500);
        }

        try {
            $photo = Photo::query()->create(['private' => true]);
            $media = $photo->addMediaFromRequest('file')->toMediaCollection('photos');
            return response()->json([
                'message' => $media,
            ]);
//            return html_entity_decode(view('photos.includes.selectablephoto', ['photo' => $photo]));
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception,
            ], 500);
        }
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
