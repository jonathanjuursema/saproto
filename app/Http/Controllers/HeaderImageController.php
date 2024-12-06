<?php

namespace App\Http\Controllers;

use App\Models\HeaderImage;
use App\Models\StorageEntry;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HeaderImageController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('headerimages.index', ['images' => HeaderImage::query()->paginate(5)]);
    }

    /** @return View */
    public function create()
    {
        return view('headerimages.add');
    }

    /**
     * @throws FileNotFoundException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'user' => 'required|integer',
            'image' => 'required|image:jpeg,png,jpg|max:2048',
        ]);

        $file = new StorageEntry;
        $file->createFromFile($validated['image']);
        $file->save();

        $header = HeaderImage::query()->create([
            'title' => $validated['title'],
            'credit_id' => $validated['user'],
            'image_id' => $file->id,
        ]);
        $header->save();

        return Redirect::route('headerimages.index');
    }

    public function destroy(HeaderImage $headerimage)
    {
        $headerimage->image->delete();
        $headerimage->delete();
        Session::flash('flash_message', 'Header image deleted.');

        return Redirect::route('headerimages.index');
    }
}
