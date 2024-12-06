<?php

namespace App\Http\Controllers;

use App\Enums\EmailDestination;
use App\Mail\NewManualEmail;
use App\Models\Email;
use App\Models\EmailList;
use App\Models\EmailListSubscription;
use App\Models\StorageEntry;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class EmailController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('emailadmin.overview', [
            'lists' => EmailList::query()->withCount('users')->get(),
            'emails' => Email::query()->orderBy('id', 'desc')->paginate(10),
        ]);
    }

    public function filter(Request $request): View
    {
        $filteredEmails = Email::query()->orderBy('id', 'desc');
        $description = $request->has('search_description');
        $subject = $request->has('search_subject');
        $body = $request->has('search_body');
        $searchTerm = $request->input('searchterm');

        if ($description) {
            $filteredEmails = $filteredEmails->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($subject) {
            $filteredEmails = $filteredEmails->orWhere('subject', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($body) {
            $filteredEmails = $filteredEmails->orWhere('body', 'LIKE', '%' . $searchTerm . '%');
        }

        return view('emailadmin.overview', [
            'lists' => EmailList::query()->withCount('users')->get(),
            'emails' => $filteredEmails->paginate(10),
            'searchTerm' => $searchTerm,
            'description' => $description,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    /** @return View */
    public function create(Request $request)
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

            return Redirect::route('email::index');
        }

        if ($request->enum('destination', EmailDestination::class) === EmailDestination::NO_DESTINATION) {
            Session::flash('flash_message', 'Please select a destination for the email.');

            return Redirect::back();
        }

        $email = Email::query()->create([
            'description' => $request->input('description'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'time' => strtotime($request->input('time')),
            'sender_name' => $request->input('sender_name'),
            'sender_address' => $request->input('sender_address'),
        ]);
        $this->updateEmailDestination($email, $request->enum('destination', EmailDestination::class), $request->input('listSelect') ?? [], $request->input('eventSelect') ?? [], $request->input('users') ?? []);
        Session::flash('flash_message', 'Your e-mail has been saved.');

        return Redirect::route('email::index');
    }

    /**
     * @return NewManualEmail
     *
     * @throws Exception
     */
    public function show(int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);
        
        return new NewManualEmail($email, Auth::user());
    }

    /**
     * @return View|RedirectResponse
     */
    public function edit(int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::index');
        }

        return view('emailadmin.editmail', ['email' => $email]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);

        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::index');
        }

        if (strtotime($request->input('time')) === false) {
            Session::flash('flash_message', 'Schedule time improperly formatted.');

            return Redirect::back();
        }

        if ($request->enum('destination', EmailDestination::class) === EmailDestination::NO_DESTINATION) {
            Session::flash('flash_message', 'Please select a destination for the email.');

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

        $this->updateEmailDestination($email, $request->enum('destination', EmailDestination::class), $request->input('listSelect') ?? [], $request->input('eventSelect') ?? [], $request->input('users') ?? []);

        Session::flash('flash_message', 'Your e-mail has been saved.');

        return Redirect::route('email::index');
    }

    /**
     * @return RedirectResponse
     */
    public function toggleReady(Request $request, int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);

        if ($email->sent) {
            Session::flash('flash_message', 'This e-mail has been sent and can thus not be edited.');

            return Redirect::route('email::index');
        }

        if ($email->ready) {
            $email->ready = false;
            $email->save();
            Session::flash('flash_message', 'The e-mail has been put on hold.');
        } else {
            if ($email->time - date('U') < 0) {
                Session::flash('flash_message', 'An e-mail can only be queued for delivery if the delivery time is in the future!');

                return Redirect::route('email::index');
            }

            $email->ready = true;
            $email->save();
            Session::flash('flash_message', 'The e-mail has been queued for delivery at the specified time.');
        }

        return Redirect::route('email::index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function addAttachment(Request $request, int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::index');
        }

        $upload = $request->file('attachment');
        if ($upload) {
            $file = new StorageEntry;
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
     * @return RedirectResponse
     */
    public function deleteAttachment(int $id, int $file_id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);
        if ($email->sent || $email->ready) {
            Session::flash('flash_message', 'You can currently not edit this e-mail. Please make sure it is in draft mode.');

            return Redirect::route('email::index');
        }

        $file = StorageEntry::query()->findOrFail($file_id);

        $email->attachments()->detach($file);
        $email->save();

        Session::flash('flash_message', 'Attachment deleted.');

        return Redirect::route('email::edit', ['id' => $email->id]);
    }

    /**
     * @return RedirectResponse
     */
    public function unsubscribeLink(Request $request, string $hash)
    {
        $data = EmailList::parseUnsubscribeHash($hash);

        /** @var User $user */
        $user = User::query()->findOrFail($data->user);
        $list = EmailList::query()->findOrFail($data->list);

        $sub = EmailListSubscription::query()->where('user_id', $user->id)->where('list_id', $list->id)->first();
        if ($sub != null) {
            Session::flash('flash_message', $user->name . ' has been unsubscribed from ' . $list->name);
            $sub->delete();
        } else {
            Session::flash('flash_message', $user->name . ' was already unsubscribed from ' . $list->name);
        }

        return Redirect::route('homepage');
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        /** @var Email $email */
        $email = Email::query()->findOrFail($id);
        if ($email->sent) {
            Session::flash('flash_message', 'This e-mail has been sent and can thus not be deleted.');

            return Redirect::route('email::index');
        }

        $email->delete();
        Session::flash('flash_message', 'The e-mail has been deleted.');

        return Redirect::route('email::index');
    }

    private function updateEmailDestination(Email $email, EmailDestination $type, array $lists = [], array $events = [], array $users = []): void
    {
        switch ($type) {
            case EmailDestination::EVENT_WITH_BACKUP:
            case EmailDestination::EVENT:
                $email->destination = $type;
                $email->specificUsers()->sync([]);
                $email->lists()->sync([]);
                if ($events !== []) {
                    $email->events()->sync($events);
                }

                break;
            case EmailDestination::SPECIFIC_USERS:
                $email->destination = $type;
                $email->lists()->sync([]);
                $email->events()->sync([]);
                if ($users !== []) {
                    $email->specificUsers()->sync($users);
                }

                break;
            case EmailDestination::EMAIL_LISTS:
                $email->destination = $type;
                $email->events()->sync([]);
                $email->specificUsers()->sync([]);
                if ($lists !== []) {
                    $email->lists()->sync($lists);
                }

                break;
            default:
                $email->destination = $type;
                $email->specificUsers()->sync([]);
                $email->lists()->sync([]);
                $email->events()->sync([]);

        }

        $email->save();
    }
}
