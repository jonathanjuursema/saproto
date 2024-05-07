<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use Auth;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
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
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $fileSizeLimit = ini_get('post_max_size');
        $album = PhotoAlbum::findOrFail($id);
        $photos = $album->items()->get();

        return view('photos.admin.edit', ['album' => $album, 'photos' => $photos, 'fileSizeLimit' => $fileSizeLimit]);
    }

    /**
     * @param  int  $id
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
     * @param  int  $id
     * @return JsonResponse|string
     */
    public function upload(Request $request, $id)
    {
        $album = PhotoAlbum::findOrFail($id);
        if (! $request->hasFile('file')) {
            return response()->json([
                'message' => 'photo not found in request!',
            ], 404);
        }

        if ($album->published) {
            return response()->json([
                'message' => 'album already published! Unpublish to add more photos!',
            ], 500);
        }
        try {
            $uploadFile = $request->file('file');
            $addWaterMark = $request->has('addWaterMark');

            $photo = $this->createPhotoFromUpload($uploadFile, $id, $addWaterMark);

            return html_entity_decode(view('photos.includes.selectablephoto', ['photo' => $photo]));
        } catch (Exception $e) {
            return response()->json([
                'message' => $e,
            ], 500);
        }
    }

    /**
     * @param  int  $id
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

            if ($album->published && ! Auth::user()->can('publishalbums')) {
                abort(403, 'Unauthorized action.');
            }

            switch ($action) {
                case 'remove':
                    foreach ($photos as $photoId) {
                        Photo::find($photoId)->delete();
                    }
                    break;

                case 'thumbnail':
                    $album->thumb_id = (int) $photos[0];
                    break;

                case 'private':
                    foreach ($photos as $photoId) {
                        $photo = Photo::find($photoId);
                        if ($photo && ! $album->published) {
                            $photo->private = ! $photo->private;
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
     * @param  int  $id
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
     * @param  int  $id
     * @return RedirectResponse
     */
    public function publish($id)
    {
        $album = PhotoAlbum::where('id', '=', $id)->first();

        if (! count($album->items) > 0 || $album->thumb_id == null) {
            Session::flash('flash_message', 'Albums need at least one photo and a thumbnail to be published.');

            return Redirect::back();
        }

        $album->published = true;
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function unpublish($id)
    {
        $album = PhotoAlbum::where('id', '=', $id)->first();
        $album->published = false;
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @throws FileNotFoundException
     */
    private function createPhotoFromUpload(UploadedFile $uploaded_photo, int $album_id, $addWatermark = false): Photo
    {
        $album = PhotoAlbum::findOrFail($album_id);
        $photo = new Photo();
        $photo->makePhoto($uploaded_photo, $uploaded_photo->getClientOriginalName(), $uploaded_photo->getCTime(), $album->private, $album->id, $album->id, $addWatermark, Auth::user()->name);
        $photo->save();

        return $photo;
    }
}
