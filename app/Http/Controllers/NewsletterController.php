<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\EmailList;
use App\Models\Event;
use App\Models\Newsletter;
use Redirect;
use Session;

class NewsletterController extends Controller
{
    /** @return View */
    public function getInNewsletter()
    {
        $events = Event::where('start', '>', date('U'))->where('secret', false)->orderBy('start', 'asc')->get();

        return view('event.innewsletter', ['events' => $events]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function toggleInNewsletter($id)
    {
        /** @var Event $event */
        $event = Event::findOrFail($id);
        $event->include_in_newsletter = ! $event->include_in_newsletter;
        $event->save();

        return Redirect::back();
    }

    /** @return View */
    public function newsletterPreview()
    {
        return view('emails.newsletter', [
            'user' => Auth::user(),
            'list' => EmailList::find(config('proto.weeklynewsletter')),
            'events' => Event::getEventsForNewsletter(),
            'text' => Newsletter::text(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function sendNewsletter(Request $request)
    {
        if (! Auth::user()->can('board')) {
            abort(403, 'Only the board can do this.');
        }

        Newsletter::send();

        Session::flash('flash_message', 'The weekly newsletter has been sent.');

        return Redirect::back();
    }

    /**
     * @return RedirectResponse
     */
    public function saveNewsletterText(Request $request)
    {
        Newsletter::setText($request->text);

        Session::flash('flash_message', 'The newsletter text has been set.');

        return Redirect::back();
    }
}
