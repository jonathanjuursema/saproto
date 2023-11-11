<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\StorageEntry;
use Auth;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;
use Redirect;
use Session;

class PhotoAdminController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('photos.admin.index');
    }

    /** @return View */
    public function search(Request $request)
    {
        return view('photos.admin.index', ['query' => $request->input('query')]);
    }

    /**
     * @return RedirectResponse
     */
    public function create(Request $request)
    {
        $album = new PhotoAlbum();
        $album->name = $request->input('name');
        $album->date_taken = strtotime($request->input('date'));
        if ($request->input('private')) {
            $album->private = true;
        }
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $album->id]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function edit(int $id)
    {
        $album = PhotoAlbum::findOrFail($id);
        return Inertia::render('Photos/UploadPage', [
            'album' => $album,
            'photos' => $album->items()->orderBy('date_taken')->orderBy('id')->get(),
            'thumbnailUrl' => $album->thumb(),
        ]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $album = PhotoAlbum::find($id);
        $album->name = $request->input('album');
        $album->date_taken = strtotime($request->input('date'));
        $album->private = $request->has('private');
        foreach ($album->items as $photo) {
            $photo->private = $request->has('private');
            $photo->save();
        }
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param int $id
     */
    public function upload(Request $request, $id)
    {
        $album = PhotoAlbum::findOrFail($id);
        foreach (config('proto.photo_resizes') as $key => $value) {
            if (!$request->hasFile($value) || !$request->file($value)->isValid()) {
                $imageSizes = getimagesize($request->file($value));
                if (!$imageSizes || $imageSizes[0] == 0 || $imageSizes[1] == 0) {
                    return response()->json([
                        'message' => 'The response does not have the correct form with key ' . $key . ' in it!',
                    ], 400);
                }
                $width = $imageSizes[0];
                $height = $imageSizes[1];
                if (($width > $height && $width > $value) || ($height > $width && $height > $value)) {
                    return response()->json([
                        'message' => 'The image with key' . $key . ' is not the right size!',
                    ], 400);
                }
            }
        }

        if ($album->published) {
            return response()->json([
                'message' => 'album already published! Unpublish to add more photos!',
            ], 500);
        } else if (!$request->hasFile('original')) {
            return response()->json([
                'message' => 'original photo not found in request!',
            ], 404);
        }

        try {
            $original = $request->file('original');
            $large = $request->file('1080');
            $medium = $request->file('750');
            $small = $request->file('420');
            $tiny = $request->file('50');

            $photo = new Photo();
            $photo->savePhoto($original, $large, $medium, $small, $tiny, $original->getCTime(), $album->private, $album->id, $album->id);

            $photo->save();

            return response()->json([
                'message' => 'Photo uploaded successfully!',
                'url' => $photo->getMediumUrlAttribute(),
                'name' => $original->getClientOriginalName(),
                'photo' => json_encode($photo),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e,
            ], 500);
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function action(Request $request, $id)
    {
        $action = $request->input('action');
        $photos = $request->input('photos');

        if ($photos) {
            $album = PhotoAlbum::findOrFail($id);

            if ($album->published && !Auth::user()->can('publishalbums')) {
                abort(403, 'Unauthorized action.');
            }

            switch ($action) {
                case 'remove':
                    foreach ($photos as $photoId) {
                        Photo::find($photoId)->delete();
                    }
                    break;

                case 'thumbnail':
                    $album->thumb_id = (int)$photos[0];
                    break;

                case 'private':
                    foreach ($photos as $photoId) {
                        $photo = Photo::find($photoId);
                        if ($photo && !$album->published) {
                            $photo->private = !$photo->private;
                            $photo->save();
                        }
                    }
                    break;
            }
            $album->save();
        }

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete($id)
    {
        $album = PhotoAlbum::findOrFail($id);
        $album->delete();

        return redirect(route('photo::admin::index'));
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function publish($id)
    {
        $album = PhotoAlbum::where('id', '=', $id)->first();

        if (!count($album->items) > 0 || $album->thumb_id == null) {
            Session::flash('flash_message', 'Albums need at least one photo and a thumbnail to be published.');

            return Redirect::back();
        }

        $album->published = true;
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function unpublish($id)
    {
        $album = PhotoAlbum::where('id', '=', $id)->first();
        $album->published = false;
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }
}
