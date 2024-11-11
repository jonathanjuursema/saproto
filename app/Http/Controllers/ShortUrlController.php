<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Milon\Barcode\DNS2D;

class ShortUrlController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $urls = ShortUrl::query()->orderBy('url')->paginate(25);

        return view('short_url.index', ['urls' => $urls]);
    }

    public function create()
    {
        return view('short_url.edit', ['url' => null]);
    }

    public function store(Request $request)
    {
        ShortUrl::query()->create($request->all());
        Session::flash('flash_message', 'New URL created!');

        return Redirect::route('short_urls.index');
    }

    /**
     * @param  ShortUrl  $url
     * @return View
     */
    public function edit(ShortUrl $short_url)
    {
        return view('short_url.edit', ['url' => $short_url]);
    }

    /**
     * @param  ShortUrl  $url
     * @return RedirectResponse
     */
    public function update(Request $request, ShortUrl $short_url)
    {
        $short_url->update($request->all());
        Session::flash('flash_message', 'Short URL updated!');

        return Redirect::route('short_urls.index');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(ShortUrl $short_url)
    {
        $short_url->delete();

        Session::flash('flash_message', 'Short URL deleted!');

        return Redirect::route('short_urls.index');
    }

    public function qrCode(int $id)
    {
        $url = ShortUrl::query()->findOrFail($id);

        return response((new DNS2D)->getBarcodeSVG(sprintf('https://%s', $url->target), 'QRCODE,M'))->header('Content-Type', 'image/svg+xml');
    }

    /**
     * @return RedirectResponse
     */
    public function go(string $short)
    {
        $url = ShortUrl::query()->where('url', $short)->firstOrFail();
        $url->clicks++;
        $url->save();

        return Redirect::to(sprintf('https://%s', $url->target));
    }
}
