<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\HeaderImage;
use App\Models\StorageEntry;

class HeaderImageController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('headerimages.index', ['images' => HeaderImage::paginate(5)]);
    }

    /** @return View */
    public function create()
    {
        return view('headerimages.add');
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $header = HeaderImage::create([
            'title' => $request->get('title'),
            'credit_id' => $request->get('user'),
        ]);

        $image = $request->file('image');
        if (! $image) {
            Session::flash('flash_message', 'Image is required.');
            Redirect::back();
        }
        $file = new StorageEntry();
        $file->createFromFile($image);

        $header->image()->associate($file);
        $header->save();

        return Redirect::route('headerimage::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        HeaderImage::findOrFail($id)->delete();
        Session::flash('flash_message', 'Image deleted.');

        return Redirect::route('headerimage::index');
    }
}
