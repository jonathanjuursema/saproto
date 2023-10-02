<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\WelcomeMessage;
use Redirect;
use Session;

class WelcomeController extends Controller
{
    /** @return View */
    public function overview()
    {
        return view('welcomemessages.list', ['messages' => WelcomeMessage::orderBy('created_at', 'desc')->get()]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $message = WelcomeMessage::where('user_id', $request->user_id)->first();
        if (! $message) {
            $message = new WelcomeMessage();
            $message->user_id = $request->user_id;
            $message->message = $request->message;
            $message->save();

            Session::flash('flash_message', 'Welcome Message set');
        } else {
            $message->message = $request->message;
            $message->save();

            Session::flash('flash_message', 'Welcome Message updated');
        }

        return Redirect::route('welcomeMessages::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $message = WelcomeMessage::findOrFail($id);
        $message->delete();
        Session::flash('flash_message', 'Welcome Message removed');

        return Redirect::route('welcomeMessages::list');
    }
}
