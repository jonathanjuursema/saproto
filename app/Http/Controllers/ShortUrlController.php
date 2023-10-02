<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\ShortUrl;

class ShortUrlController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $urls = ShortUrl::orderBy('url')->paginate(25);

        return view('short_url.index', ['urls' => $urls]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit(Request $request, $id)
    {
        $url = $id == 'new' ? null : ShortUrl::findOrFail($id);

        return view('short_url.edit', ['url' => $url]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $url = $id == 'new' ? new ShortUrl() : ShortUrl::findOrFail($id);
        $url->fill($request->all());
        $url->save();
        Session::flash('flash_message', 'Short URL updated!');

        return Redirect::route('short_url::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        $url = ShortUrl::findOrFail($id);
        $url->delete();

        Session::flash('flash_message', 'Short URL deleted!');

        return Redirect::route('short_url::index');
    }

    /**
     * @param  string  $short
     * @return RedirectResponse
     */
    public function go(Request $request, $short)
    {
        $url = ShortUrl::where('url', $short)->firstOrFail();
        $url->clicks = $url->clicks + 1;
        $url->save();

        return Redirect::to(sprintf('https://%s', $url->target));
    }
}
