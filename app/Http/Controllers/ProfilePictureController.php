<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\StorageEntry;
use Redirect;
use Session;

class ProfilePictureController extends Controller
{
    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $image = $request->file('image');
        if ($image) {
            if (substr($image->getMimeType(), 0, 5) == 'image') {
                $file = new StorageEntry();
                $file->createFromFile($image);

                $user->photo()->associate($file);
                $user->save();
            } else {
                Session::flash('flash_message', 'This is not an image file!');

                return Redirect::back();
            }
        } else {
            Session::flash('flash_message', 'You forget an image to upload, silly!');

            return Redirect::back();
        }
        Session::flash('flash_message', 'Your profile picture has been updated!');

        return Redirect::back();
    }

    /** @return RedirectResponse */
    public function destroy()
    {
        $user = Auth::user();

        $user->photo()->dissociate();
        $user->save();

        Session::flash('flash_message', 'Your profile picture has been cleared!');

        return Redirect::back();
    }
}
