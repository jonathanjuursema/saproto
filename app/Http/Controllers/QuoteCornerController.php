<?php

namespace Proto\Http\Controllers;

use Auth;
use Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Proto\Models\Quote;
use Proto\Models\QuoteLike;
use Redirect;
use Session;

class QuoteCornerController extends Controller
{
    /** @return View|array */
    public function overview()
    {
        $quotes = Quote::where('updated_at', '>', Carbon::now()->subWeeks(4))->get();
        $popular = null;
        $popularLikes = 0;
        foreach ($quotes as $key => $quote) {
            $likes = QuoteLike::where('quote_id', $quote->id)->get();
            if ($popularLikes < count($likes)) {
                $popular = $quote;
                $popularLikes = count($likes);
            }
        }

        if (request()->wantsJson()) {
            $quotes = Quote::orderBy('created_at', 'desc')->paginate(20);
            foreach ($quotes as $quote) {
                $quote->quote = str_replace('<br />', "\n", strip_tags($quote->quote, 'br'));
                $quote->user_info = (object) [
                    'name' => $quote->user->name,
                    'photo' => $quote->user->photo_preview,
                ];
            }
            $popular->quote = str_replace('<br />', "\n", strip_tags($popular->quote, 'br'));
            $popular->user_info = (object) [
                'name' => $popular->user->name,
                'photo' => $popular->user->photo_preview,
            ];

            return ['data' => $quotes, 'popular' => $popular];
        } else {
            return view('quotecorner.list', ['data' => Quote::orderBy('created_at', 'desc')->paginate(20), 'popular' => $popular]);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse|string|false
     */
    public function add(Request $request)
    {
        $temp = $request->input('quote');
        $temp = nl2br(trim($temp));
        if (! (strlen($temp) > 0)) {
            if ($request->wantsJson()) {
                abort(400, json_encode((object) [
                    'success' => false,
                    'message' => 'Quote too short',
                ]));
            } else {
                return Redirect::route('quotes::list');
            }
        }
        $new = [
            'quote' => $temp,
            'user_id' => Auth::id(),
        ];
        $quote = new Quote($new);
        $quote->save();

        if ($request->wantsJson()) {
            $data_quote = $quote;
            $data_quote->quote = str_replace('<br />', "\n", strip_tags($data_quote->quote, 'br'));
            $data_quote->user_info = (object) [
                'name' => $data_quote->user->name,
                'photo' => $data_quote->user->photo_preview,
            ];

            return json_encode((object) [
                'success' => true,
                'message' => 'Quote saved',
                'data' => $data_quote,
            ]);
        } else {
            Session::flash('flash_message', 'Quote added.');

            return Redirect::route('quotes::list');
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        QuoteLike::where('quote_id', $id)->delete();
        $quote->delete();
        Session::flash('flash_message', 'Quote deleted.');

        return Redirect::route('quotes::list');
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function toggleLike($id)
    {
        $quote = QuoteLike::where('quote_id', $id)->where('user_id', Auth::user()->id)->get();
        if (count($quote) != 0) {
            $quote[0]->delete();
        } else {
            $new = [
                'user_id' => Auth::user()->id,
                'quote_id' => $id,
            ];
            $relation = new QuoteLike($new);
            $relation->save();
        }
    }
}
