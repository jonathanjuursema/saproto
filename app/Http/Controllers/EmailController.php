<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Email;
use App\Models\EmailList;
use App\Models\EmailListSubscription;
use App\Models\StorageEntry;
use App\Models\User;
use Redirect;
use Session;

class EmailController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('emailadmin.overview', [
            'lists' => EmailList::all(),
            'emails' => Email::orderBy('id', 'desc')->paginate(10),
        ]);
    }

    /** @return View */
    public function create()
    {
        return view('emailadmin.editmail', ['email' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (strtotime($request->input('time')) === false) {
            Session::flash('flash_message', 'Schedule time improperly formatted.');

            return Redirect::route('email::admin');
        }
        $email = Email::create([
            'description' => $request->input('description'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'time' => strtotime($request->input('time')),
            'sender_name' => $request->input('sender_name'),
            'sender_address' => $request->input('sender_address'),
        ]);
        $this->updateEmailDestination($email, $request->input('destinationType'), $request->input('listSelect'), $request->input('eventSelect'), $request->has('toBackup'));
        Session::flash('flash_message', 'Your e-mail has been saved.');

        return Redirect::route('email::admin');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $email = Email::findOrFail($id);

        return view('emails.manualemail', [
            'body' => $email->parseBodyFor(Auth::user()),
            'attachments' => $email->attachments,
            'destination' => $email->destinationForBody(),
            'user_id' => Auth::user()->id,
            'events' => $email->events()->get(),
            'email_id' => $email->id,
        ]);
    }

    /**
     * @param  int  $id
     * @return View|RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::admin');
        }

        return view('emailadmin.editmail', ['email' => $email]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);

        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::admin');
        }

        if (strtotime($request->input('time')) === false) {
            Session::flash('flash_message', 'Schedule time improperly formatted.');

            return Redirect::back();
        }

        $email->fill([
            'description' => $request->input('description'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'time' => strtotime($request->input('time')),
            'sender_name' => $request->input('sender_name'),
            'sender_address' => $request->input('sender_address'),
        ]);

        $this->updateEmailDestination($email, $request->input('destinationType'), $request->input('listSelect'), $request->input('eventSelect'), $request->has('toBackup'));

        Session::flash('flash_message', 'Your e-mail has been saved.');

        return Redirect::route('email::admin');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function toggleReady(Request $request, $id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);

        if ($email->sent) {
            Session::flash('flash_message', 'This e-mail has been sent and can thus not be edited.');

            return Redirect::route('email::admin');
        }

        if ($email->ready) {
            $email->ready = false;
            $email->save();
            Session::flash('flash_message', 'The e-mail has been put on hold.');
        } else {
            if ($email->time - date('U') < 5 * 60) {
                Session::flash('flash_message', 'An e-mail can only be queued for delivery if the delivery time is at least 5 minutes in the future.');

                return Redirect::route('email::admin');
            }
            $email->ready = true;
            $email->save();
            Session::flash('flash_message', 'The e-mail has been queued for deliver at the specified time.');
        }

        return Redirect::route('email::admin');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function addAttachment(Request $request, $id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::admin');
        }

        $upload = $request->file('attachment');
        if ($upload) {
            $file = new StorageEntry();
            $file->createFromFile($upload);
            $email->attachments()->attach($file);
            $email->save();
        } else {
            Session::flash('flash_message', 'Do not forget the attachment.');

            return Redirect::route('email::edit', ['id' => $email->id]);
        }

        Session::flash('flash_message', 'Attachment uploaded.');

        return Redirect::route('email::edit', ['id' => $email->id]);
    }

    /**
     * @param  int  $id
     * @param  int  $file_id
     * @return RedirectResponse
     */
    public function deleteAttachment(Request $request, $id, $file_id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::admin');
        }

        $file = StorageEntry::findOrFail($file_id);

        $email->attachments()->detach($file);
        $email->save();

        Session::flash('flash_message', 'Attachment deleted.');

        return Redirect::route('email::edit', ['id' => $email->id]);
    }

    /**
     * @param  string  $hash
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function unsubscribeLink(Request $request, $hash)
    {
        $data = EmailList::parseUnsubscribeHash($hash);

        /** @var User $user */
        $user = User::findOrFail($data->user);
        $list = EmailList::findOrFail($data->list);

        $sub = EmailListSubscription::where('user_id', $user->id)->where('list_id', $list->id)->first();
        if ($sub != null) {
            Session::flash('flash_message', $user->name.' has been unsubscribed from '.$list->name);
            $sub->delete();
        } else {
            Session::flash('flash_message', $user->name.' was already unsubscribed from '.$list->name);
        }

        return Redirect::route('homepage');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var Email $email */
        $email = Email::findOrFail($id);
        if ($email->sent) {
            Session::flash('flash_message', 'This e-mail has been sent and can thus not be deleted.');

            return Redirect::route('email::admin');
        }
        $email->delete();
        Session::flash('flash_message', 'The e-mail has been deleted.');

        return Redirect::route('email::admin');
    }

    /**
     * @param  array  $type
     * @param  array  $lists
     * @param  array  $events
     * @param  bool  $toBackup
     */
    private function updateEmailDestination(Email $email, $type, $lists = [], $events = [], $toBackup = false)
    {

        switch ($type) {

            case 'members':
                $email->to_user = false;
                $email->to_pending = false;
                $email->to_member = true;
                $email->to_active = false;

                $email->to_list = false;
                $email->to_event = false;
                $email->to_backup = false;

                $email->lists()->sync([]);
                $email->events()->sync([]);
                break;

            case 'pending':
                $email->to_user = false;
                $email->to_pending = true;
                $email->to_member = false;
                $email->to_active = false;

                $email->to_list = false;
                $email->to_event = false;
                $email->to_backup = false;

                $email->lists()->sync([]);
                $email->events()->sync([]);
                break;

            case 'active':
                $email->to_user = false;
                $email->to_pending = false;
                $email->to_member = false;
                $email->to_active = true;

                $email->to_list = false;
                $email->to_event = false;
                $email->to_backup = false;

                $email->lists()->sync([]);
                $email->events()->sync([]);
                break;

            case 'event':
                $email->to_user = false;
                $email->to_pending = false;
                $email->to_member = false;
                $email->to_active = false;

                $email->to_list = false;
                $email->to_event = true;
                $email->to_backup = $toBackup;
                $email->lists()->sync([]);
                if (! empty($events)) {
                    $email->events()->sync($events);
                }
                break;

            case 'lists':
                $email->to_user = false;
                $email->to_pending = false;
                $email->to_member = false;
                $email->to_active = false;

                $email->to_list = true;
                $email->to_event = false;
                $email->to_backup = false;

                $email->lists()->sync((gettype($lists) == 'array' ? $lists : []));
                $email->events()->sync([]);
                break;

            default:
                $email->to_user = false;
                $email->to_pending = false;
                $email->to_member = false;
                $email->to_active = false;

                $email->to_list = false;
                $email->to_event = false;
                $email->to_backup = false;

                $email->lists()->sync([]);
                $email->events()->sync([]);
                break;

        }
        $email->save();
    }
}
