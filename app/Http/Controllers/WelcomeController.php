<?php

namespace App\Http\Controllers;

use App\Models\WelcomeMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('welcomemessages.list', ['messages' => WelcomeMessage::query()->orderBy('created_at', 'desc')->get()]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'nullable|integer',
            'message' => 'required|string',
        ]);
        $message = WelcomeMessage::query()->where('user_id', $request->user_id)->first();
        if (! $message) {
            $message = new WelcomeMessage;
            $message->user_id = $request->user_id;
            $message->message = $request->message;
            $message->save();

            Session::flash('flash_message', 'Welcome Message set');
        } else {
            $message->message = $request->message;
            $message->save();

            Session::flash('flash_message', 'Welcome Message updated');
        }

        return Redirect::route('welcomeMessages.index');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(WelcomeMessage $welcomeMessage)
    {
        $welcomeMessage->delete();
        Session::flash('flash_message', 'Welcome Message removed');

        return Redirect::route('welcomeMessages.index');
    }
}
