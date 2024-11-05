<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\PhotoManager;
use App\Models\StorageEntry;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PhotoAdminController extends Controller
{
    /** @return View */
    public function index(Request $request)
    {
        return view('photos.admin.index', ['query' => $request->input('query') ?? '']);
    }

    /**
     * @return RedirectResponse
     */
    public function create(Request $request)
    {
        $album = new PhotoAlbum;
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
     * @return View
     */
    public function edit(int $id)
    {
        $photos = PhotoManager::getPhotos($id);
        $fileSizeLimit = ini_get('post_max_size');

        if ($photos == null) {
            abort(404);
        }

        return view('photos.admin.edit', ['photos' => $photos, 'fileSizeLimit' => $fileSizeLimit]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $album = PhotoAlbum::query()->find($id);
        $album->name = $request->input('album');
        $album->date_taken = strtotime($request->input('date'));
        $album->private = (bool)$request->input('private');

        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|string
     */
    public function upload(Request $request, int $id)
    {
        $album = PhotoAlbum::query()->findOrFail($id);
        if (!$request->hasFile('file')) {
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
            $album->addMedia($request->file('file'))->toMediaCollection();

            $uploadFile = $request->file('file');

            $photo = $this->createPhotoFromUpload($uploadFile, $id);

            return html_entity_decode(view('photos.includes.selectablephoto', ['photo' => $photo]));

        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception,
            ], 500);
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function action(Request $request, int $id)
    {
        $action = $request->input('action');
        $photos = $request->input('photos');

        if ($photos) {
            $album = PhotoAlbum::query()->findOrFail($id);

            if ($album->published && !Auth::user()->can('publishalbums')) {
                abort(403, 'Unauthorized action.');
            }

            switch ($action) {
                case 'remove':
                    foreach ($photos as $photoId) {
                        Photo::query()->find($photoId)->delete();
                    }

                    break;

                case 'thumbnail':
                    $album->thumb_id = (int)$photos[0];
                    break;

                case 'private':
                    foreach ($photos as $photoId) {
                        $photo = Photo::query()->find($photoId);
                        if ($album->published && $photo->private) {
                            continue;
                        }

                        $photo->private = !$photo->private;
                        $photo->save();
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
        PhotoManager::deleteAlbum($id);

        return Redirect::route('photo::admin::index');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function publish($id)
    {
        $album = PhotoAlbum::query()->where('id', '=', $id)->first();

        if (count($album->items) <= 0 || $album->thumb_id == null) {
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
        $album = PhotoAlbum::query()->where('id', '=', $id)->first();
        $album->published = false;
        $album->save();

        return Redirect::route('photo::admin::edit', ['id' => $id]);
    }

    /**
     * @param int $album_id
     *
     * @throws FileNotFoundException
     */
    private function createPhotoFromUpload(UploadedFile $uploaded_photo, $album_id): Photo
    {
        $path = 'photos/' . $album_id . '/';

        $file = new StorageEntry;
        $file->createFromFile($uploaded_photo, $path);
        $file->save();

        $photo = new Photo;
        $photo->date_taken = $uploaded_photo->getCTime();
        $photo->album_id = $album_id;
        $photo->file_id = $file->id;
        $photo->save();

        return $photo;
    }
}
